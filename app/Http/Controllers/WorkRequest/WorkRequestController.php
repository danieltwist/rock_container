<?php

namespace App\Http\Controllers\WorkRequest;

use App\Events\NotificationReceived;
use App\Events\TaskDone;
use App\Events\TelegramNotify;
use App\Filters\WorkRequestFilter;
use App\Http\Controllers\Controller;
use App\Models\Container;
use App\Models\ContainerGroup;
use App\Models\Invoice;
use App\Models\Notification;
use App\Models\Project;
use App\Models\WorkRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class WorkRequestController extends Controller
{
    public function index()
    {
        $role = Auth::user()->getRoleNames()[0];

        if(in_array($role,['super-admin','director'])){
            $work_requests = WorkRequest::orderBy('id','DESC')->get();
            $work_requests = $this->giveClass($work_requests);

            return view('work_request.index', [
                'tasks' => $work_requests,
            ]);
        }

        else{
            return redirect()->route('income_work_requests');
        }

    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {

        $user = auth()->user();

        $new_work_request = new WorkRequest();

        $project_id = null;

        $new_work_request->type = 'Пользователь';
        $new_work_request->model = $request->model;

        if(in_array($request->model,['upd','free'])) {
            $model_id = null;
        } else $model_id = $request->model_id;

        $new_work_request->model_id = $model_id;

        $object = '';

        if ($request->model == 'project'){
            $project = Project::find($request->model_id);
            $object = 'Проект '.$project->name;
        }

        if ($request->model == 'container'){
            $container = Container::find($request->model_id);
            $object = 'Контейнер '.$container->name;
            $container->project_id != '' ? $project_id = $container->project_id : $project_id = null;
        }

        if ($request->model == 'container_group'){
            $container_group = ContainerGroup::find($request->model_id);
            $object = 'Группа контейнеров №'.$container_group->id. ' '. $container_group->name;
            $container_group->project_id != '' ? $project_id = $container_group->project_id : $project_id = null;
        }

        if ($request->model == 'invoice'){
            $invoice = Invoice::find($request->model_id);
            $invoice->project_id != '' ? $project_id = $invoice->project_id : $project_id = null;
            $company = '';

            if ($invoice->supplier_id != ''){
                $company = $invoice->supplier->name;
            }
            if ($invoice->client_id != ''){
                $company = $invoice->client->name;
            }

            $object = $invoice->direction.' №'.$invoice->id.' от '.$invoice->created_at.' для '.$company;
        }

        if ($request->model == 'upd'){
            $object = 'Список выбранных счетов';
            $new_work_request->object_array = serialize($request->model_id);
            foreach ($request->model_id as $invoice){
                $invoice = Invoice::find($invoice);
                $project_id = $invoice->project_id;
                break;
            }
        }

        if ($request->model == 'free'){
            $object = null;
        }

        $send_to = explode (':', $request->to_users);

        if ($send_to[0] == 'Пользователю'){
            $send_to_text = userInfo($send_to[1])->name;
        }
        else {
            $send_to_text = $send_to[0];
        }

        $to_users = explode(',',$send_to[1]);
        $responsible_user = $to_users;

        $send_to_additional_users = [];

        if(!is_null($request->additional_users)){
            foreach ($request->additional_users as $additional_user){
                $additional_user_item = explode (':', $additional_user);
                if($additional_user_item[0] == 'Пользователю'){
                    $send_to_additional_users [] = $additional_user_item[1];
                }
                else {
                    $send_to_additional_users = explode(',',$additional_user_item[1]);
                }

            }
        }

        $to_users = array_unique(array_merge($to_users, $send_to_additional_users));
        $new_work_request->object = $object;
        $new_work_request->name = $request->name;
        $new_work_request->text = $request->text;
        $new_work_request->from_user_id = $user->id;
        $new_work_request->send_to = $send_to_text;
        $new_work_request->deadline = $request->task_deadline;
        $new_work_request->can_change_deadline = $request->can_change_deadline;
        $new_work_request->check_work = $request->check_work;
        $new_work_request->additional_users = $send_to_additional_users;
        $new_work_request->to_users = array_map('intval', $to_users);
        $new_work_request->responsible_user = $responsible_user;
        $new_work_request->active = '1';
        $new_work_request->status = 'Ожидает выполнения';
        $new_work_request->project_id = $project_id;

        $new_work_request->save();

        if($request->hasFile('files')) {

            $folder = $new_work_request->id;

            $files = [];

            foreach ($request->file('files') as $file) {

                $filename = preg_replace('/[^\.\,\-\_\@\?\!\:\$ a-zA-Z0-9А-Яа-я()]/u','', $file->getClientOriginalName());
                $url = $file->storeAs('public/Файлы задач/' . $folder . '/' . $user->name, $filename);

                $files [] = [
                    'name' => $filename,
                    'url' => $url
                ];

            }

            WorkRequest::find($new_work_request->id)->update([
                'file' => serialize($files)
            ]);
        }



        foreach ($to_users as $user_id){
            $message = [
                'bg_class' =>'bg-success',
                'to' => $user_id,
                'from' => 'системы',
                'object_id' => $new_work_request->id,
                'message' => __('console.new_task').' №'.$new_work_request->id.' '.$new_work_request->text.' '. __('general.from') .' '.$user->name
            ];
            $message['link'] = 'task/'.$new_work_request->id;
            $message['text'] = $message['message'];

            $message['inline_keyboard'] = [
                'inline_keyboard' => [
                    [
                        ['text' => 'Открыть', 'url' => config('app.url').$message['link']],
                    ],
                ]
            ];

            $message['action'] = 'notification';
            $message['type'] = 'task';

            $notification_channel = getNotificationChannel($message['to']);

            if($notification_channel == 'Система'){
                event(new TaskDone($message));
            }
            elseif($notification_channel == 'Telegram'){
                event(new TelegramNotify($message));
            }
            else {
                event(new TaskDone($message));
                event(new TelegramNotify($message));
            }
        }

        return response()->json([
            'bg-class' => 'bg-success',
            'from' => 'Система',
            'message' => __('work_request.created_successfully')
        ]);
    }

    public function show($id)
    {
        $work_request = WorkRequest::findOrFail($id);
        $this->getUsersWorkRequests($work_request);
        $this->checkOverdue($work_request);

        return view('work_request.show',[
            'task' => $work_request
        ]);
    }

    public function edit($id)
    {
        return view('work_request.edit',[
            'work_request' => WorkRequest::findOrFail($id)
        ]);
    }

    public function update(Request $request, $id)
    {
        $work_request = WorkRequest::find($id);

        $user = auth()->user();

        $project_id = null;

        if(!in_array($work_request->model, ['supplier', 'client'])){
            if(in_array($request->model,['upd','free'])) {
                $model_id = null;
            } else $model_id = $request->model_id;

            $object = '';

            if ($request->model == 'project'){
                $project = Project::find($request->model_id);
                $object = 'Проект '.$project->name;
            }

            if ($request->model == 'container'){
                $container = Container::find($request->model_id);
                $object = 'Контейнер '.$container->name;
                $container->project_id != '' ? $project_id = $container->project_id : $project_id = null;
            }

            if ($request->model == 'invoice'){
                $invoice = Invoice::find($request->model_id);
                $invoice->project_id != '' ? $project_id = $invoice->project_id : $project_id = null;
                $company = '';

                if ($invoice->supplier_id != ''){
                    $company = $invoice->supplier->name;
                }
                if ($invoice->client_id != ''){
                    $company = $invoice->client->name;
                }

                $object = $invoice->direction.' №'.$invoice->id.' от '.$invoice->created_at.' для '.$company;
            }

            if ($request->model == 'upd'){
                $object = 'УПД для выбранных счетов';
                $work_request->object_array = serialize($request->model_id);
                foreach ($request->model_id as $invoice){
                    $invoice = Invoice::find($invoice);
                    $project_id = $invoice->project_id;
                    break;
                }
            }

            if ($request->model == 'free'){
                $object = null;
            }

            $work_request->object = $object;
            $work_request->model = $request->model;
            $work_request->model_id = $model_id;
            $work_request->project_id = $project_id;

        }
        $send_to = explode (':', $request->to_users_create_task_to_users);

        if ($send_to[0] == 'Пользователю'){
            $send_to_text = userInfo($send_to[1])->name;
        }
        else {
            $send_to_text = $send_to[0];
        }

        $to_users = explode(',',$send_to[1]);
        $responsible_user = $to_users;

        $send_to_additional_users = [];

        if(!is_null($request->additional_users)){
            foreach ($request->additional_users as $additional_user){
                $additional_user_item = explode (':', $additional_user);
                if($additional_user_item[0] == 'Пользователю'){
                    $send_to_additional_users [] = $additional_user_item[1];
                }
                else {
                    $send_to_additional_users = explode(',',$additional_user_item[1]);
                }

            }
        }

        $to_users = array_unique(array_merge($to_users, $send_to_additional_users));


        $work_request->name = $request->name;
        $work_request->text = $request->text;
        $work_request->from_user_id = $user->id;
        $work_request->send_to = $send_to_text;
        $work_request->deadline = $request->task_deadline;
        $work_request->can_change_deadline = $request->can_change_deadline;
        $work_request->check_work = $request->check_work;
        $work_request->additional_users = $send_to_additional_users;
        $work_request->to_users = array_map('intval', $to_users);
        $work_request->responsible_user = $responsible_user;

        $work_request->save();

        if($request->hasFile('files')) {

            $folder = $work_request->id;

            $files = [];

            foreach ($request->file('files') as $file) {

                $url = $file->storeAs('public/Файлы задач/' . $folder . '/' . $user->name, $file->getClientOriginalName());

                $files [] = [
                    'name' => $file->getClientOriginalName(),
                    'url' => $url
                ];

            }

            WorkRequest::find($work_request->id)->update([
                'file' => serialize($files)
            ]);
        }

        return redirect()->route('work_request.show', $work_request->id)->withSuccess(__('work_request.updated_successfully'));

    }

    public function destroy(WorkRequest $work_request)
    {
        $work_request->delete();
        return redirect()->back()->withSuccess(__('work_request.removed_successfully'));
    }

    public function handler(Request $request)
    {
        $work_request = WorkRequest::find($request->work_request_id);
        $comment = $request->comment;
        $user = auth()->user();
        $history = unserialize($work_request->history);

        if($request->action == 'add_chat_record'){
            if($comment != ''){
                $chat = unserialize($work_request->comment);
                $chat [] = [
                    'user' => $user->id,
                    'text' => $comment,
                    'answer_to' => $request->answer_to,
                    'file' => '',
                    'date' => Carbon::now()
                ];

                $work_request->update([
                    'comment' => serialize($chat)
                ]);

                if($request->notify_users != ''){

                    $text = $user->name. ' ' . __('work_request.send_message_in_work_request') . ' №'.$work_request->id.':'.PHP_EOL.'<b>'.$comment.'</b>';
                    $link = 'work_request/'.$work_request->id;

                    foreach ($request->notify_users as $user_id){

                        if(!in_array($user_id, $work_request->to_users)){
                            $to_users = $work_request->to_users;
                            $to_users [] = $user_id;
                            $to_users = array_unique(array_map('intval', $to_users));
                            $additional_users = $work_request->additional_users;
                            $additional_users [] = $user_id;
                            $additional_users = array_unique(array_map('intval', $additional_users));

                            $work_request->update([
                                'additional_users' => $additional_users,
                                'to_users' => $to_users
                            ]);
                        }

                        $message = [
                            'from' => 'Система',
                            'to' => $user_id,
                            'text' => $text,
                            'link' => $link,
                            'class' => 'bg-info'
                        ];

                        $message['inline_keyboard'] = [
                            'inline_keyboard' => [
                                [
                                    ['text' => 'Открыть', 'url' => config('app.url').$message['link']],
                                    ['text' => 'Ответить', 'callback_data' => 'answer:work_request:'.$work_request->id.':'.array_key_last($chat)]
                                ],
                            ]
                        ];

                        $message['action'] = 'answer:work_request:'.$work_request->id.':'.array_key_last($chat).':start_message';

                        $notification_channel = getNotificationChannel($message['to']);

                        if($notification_channel == 'Система'){
                            event(new NotificationReceived($message));
                        }
                        elseif($notification_channel == 'Telegram'){
                            event(new TelegramNotify($message));
                        }
                        else {
                            event(new NotificationReceived($message));
                            event(new TelegramNotify($message));
                        }

                    }

                }

                return response()->json([
                    'bg-class' => 'bg-success',
                    'from' => 'Система',
                    'message' => __('work_request.add_chat_record_successfully'),
                    'ajax' => view('work_request.ajax.chat',[
                        'task' => $work_request
                    ])->render(),
                    'div_id' => 'chat'
                ]);
            }
            else{
                return response()->json([
                    'bg-class' => 'bg-danger',
                    'from' => 'Система',
                    'message' => __('work_request.fill_text')
                ]);
            }

        }

        if($request->action == 'delete_chat_record'){

            $chat = unserialize($work_request->comment);

            if($chat[$request->message_id]['file'] != ''){
                foreach ($chat[$request->message_id]['file'] as $file){
                    Storage::delete($file['url']);
                }
            }

            unset($chat[$request->message_id]);

            $work_request->update([
                'comment' => serialize($chat)
            ]);

            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'message' => __('work_request.delete_chat_record_successfully'),
                'ajax' => view('work_request.ajax.chat',[
                    'task' => $work_request
                ])->render(),
                'div_id' => 'chat'
            ]);
        }

        if($request->action == 'get_task'){

            $history [] = [
                'user' => $user->id,
                'text' => 'Принят к выполнению',
                'date' => Carbon::now()
            ];

            if ($work_request->accepted_user_id == ''){
                $work_request->update([
                    'accepted_user_id' => $user->id,
                    'status' => 'Выполняется',
                    'history' => serialize($history)
                ]);

                $this->getUsersWorkRequests($work_request);
                return response()->json([
                    'bg-class' => 'bg-success',
                    'from' => 'Система',
                    'message' => __('work_request.get_task_successfully'),
                    'ajax' => view('work_request.ajax.main',[
                        'task' => $work_request
                    ])->render(),
                    'div_id' => 'main'
                ]);

            }
            else {
                $this->getUsersWorkRequests($work_request);
                return response()->json([
                    'bg-class' => 'bg-success',
                    'from' => 'Система',
                    'message' => __('work_request.get_task_already_accepted').' '.$work_request->accepted_user->name,
                    'ajax' => view('work_request.ajax.main',[
                        'task' => $work_request
                    ])->render(),
                    'div_id' => 'main'
                ]);

            }

        }

        if($request->action == 'add_user'){

            $added_user = User::find($request->to_users);

            $history [] = [
                'user' => $user->id,
                'text' => 'Добавлен пользователь в соисполнители '.$added_user->name,
                'date' => Carbon::now()
            ];

            $additional_users = $work_request->additional_users;
            $additional_users [] = $request->to_users;
            $additional_users = array_unique(array_map('intval', $additional_users));

            $to_users = $work_request->to_users;
            $to_users [] = $request->to_users;
            $to_users = array_unique(array_map('intval', $to_users));

            $work_request->update([
                'to_users' => $to_users,
                'additional_users' => $additional_users,
                'history' => serialize($history)
            ]);

            $this->getUsersWorkRequests($work_request);

            $text = __('work_request.you_was_added_successfully').' '.$work_request->id.' '.$work_request->text;
            $link = 'work_request/'.$work_request->id;

            $message = [
                'from' => 'Система',
                'to' => $request->to_users,
                'text' => $text,
                'link' => $link,
                'class' => 'bg-info'
            ];

            $message['inline_keyboard'] = [
                'inline_keyboard' => [
                    [
                        ['text' => 'Открыть', 'url' => config('app.url').$message['link']],
                    ],
                ]
            ];

            $message['action'] = 'notification';

            $notification_channel = getNotificationChannel($message['to']);

            if($notification_channel == 'Система'){
                event(new NotificationReceived($message));
            }
            elseif($notification_channel == 'Telegram'){
                event(new TelegramNotify($message));
            }
            else {
                event(new NotificationReceived($message));
                event(new TelegramNotify($message));
            }

            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'message' => __('work_request.add_user_successfully'),
                'ajax' => view('work_request.ajax.users',[
                    'task' => $work_request
                ])->render(),
                'div_id' => 'task_users'
            ]);

        }

        if($request->action == 'change_deadline'){

            $history [] = [
                'user' => $user->id,
                'text' => 'Дедлайн изменен на '.$request->deadline,
                'date' => Carbon::now()
            ];

            $work_request->update([
                'deadline' => $request->deadline,
                'history' => serialize($history)
            ]);
            $this->getUsersWorkRequests($work_request);
            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'message' => __('work_request.change_deadline_successfully'),
                'ajax' => view('work_request.ajax.main',[
                    'task' => $work_request
                ])->render(),
                'div_id' => 'main'
            ]);

        }

        if($request->action == 'reload_task'){

            $history [] = [
                'user' => $user->id,
                'text' => 'Задача была возобновлена',
                'date' => Carbon::now()
            ];

            $work_request->update([
                'status' => 'Выполняется',
                'active' => '1',
                'history' => serialize($history)
            ]);

            $message = [
                'bg_class' =>'bg-success',
                'to' => $work_request->accepted_user_id,
                'from' => 'системы',
                'object_id' => $work_request->id,
                'message' => __('general.work_request').' №'.$work_request->id.' '.$work_request->text.' ' . __('work_request.was_reloaded')
            ];
            $message['link'] = 'task/'.$work_request->id;
            $message['text'] = $message['message'];

            $message['inline_keyboard'] = [
                'inline_keyboard' => [
                    [
                        ['text' => 'Открыть', 'url' => config('app.url').$message['link']],
                    ],
                ]
            ];

            $message['action'] = 'notification';
            $message['type'] = 'task';

            $notification_channel = getNotificationChannel($message['to']);

            if($notification_channel == 'Система'){
                event(new TaskDone($message));
            }
            elseif($notification_channel == 'Telegram'){
                event(new TelegramNotify($message));
            }
            else {
                event(new TaskDone($message));
                event(new TelegramNotify($message));
            }

            $this->getUsersWorkRequests($work_request);
            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'message' => __('work_request.reload_task_successfully'),
                'ajax' => view('work_request.ajax.main',[
                    'task' => $work_request
                ])->render(),
                'div_id' => 'main'
            ]);

        }

        if($request->action == 'done_task'){

            if(!is_null($work_request->check_work)){

                $history [] = [
                    'user' => $user->id,
                    'text' => 'Отправлен на проверку',
                    'date' => Carbon::now()
                ];

                $work_request->update([
                    'active' => '1',
                    'status' => 'Отправлен на проверку',
                    'done' => Carbon::now(),
                    'history' => serialize($history)
                ]);

                if($work_request->type == 'Пользователь'){
                    $message = [
                        'bg_class' =>'bg-success',
                        'to' => $work_request->from_user_id,
                        'from' => 'Система',
                        'object_id' => $work_request->id,
                        'message' => __('general.work_request').' №'.$work_request->id.' '.$work_request->text.' ' . __('work_request.was_made_by_user') .' '. $work_request->accepted_user->name.' '. __('work_request.and_waiting_for_approval')
                    ];
                    $message['link'] = 'task/'.$work_request->id;
                    $message['text'] = $message['message'];

                    $message['inline_keyboard'] = [
                        'inline_keyboard' => [
                            [
                                ['text' => 'Открыть', 'url' => config('app.url').$message['link']],
                            ],
                        ]
                    ];

                    $message['action'] = 'notification';
                    $message['type'] = 'task';

                    $notification_channel = getNotificationChannel($message['to']);

                    if($notification_channel == 'Система'){
                        event(new TaskDone($message));
                    }
                    elseif($notification_channel == 'Telegram'){
                        event(new TelegramNotify($message));
                    }
                    else {
                        event(new TaskDone($message));
                        event(new TelegramNotify($message));
                    }
                }

                $this->getUsersWorkRequests($work_request);
                return response()->json([
                    'bg-class' => 'bg-success',
                    'from' => 'Система',
                    'message' => __('work_request.done_task_send_to_agree_successfully'),
                    'ajax' => view('work_request.ajax.main',[
                        'task' => $work_request
                    ])->render(),
                    'div_id' => 'main'
                ]);

            }
            else {

                $history [] = [
                    'user' => $user->id,
                    'text' => 'Выполнен',
                    'date' => Carbon::now()
                ];

                $work_request->update([
                    'active' => '0',
                    'status' => 'Выполнен',
                    'done' => Carbon::now(),
                    'history' => serialize($history)
                ]);

                if($work_request->type == 'Пользователь'){
                    $message = [
                        'bg_class' =>'bg-success',
                        'to' => $work_request->from_user_id,
                        'from' => 'Система',
                        'object_id' => $work_request->id,
                        'message' => __('general.work_request').' №'.$work_request->id.' '.$work_request->text.' ' . __('work_request.was_made_by_user') .' '. $work_request->accepted_user->name
                    ];
                    $message['link'] = 'task/'.$work_request->id;
                    $message['text'] = $message['message'];

                    $message['inline_keyboard'] = [
                        'inline_keyboard' => [
                            [
                                ['text' => 'Открыть', 'url' => config('app.url').$message['link']],
                            ],
                        ]
                    ];

                    $message['action'] = 'notification';
                    $message['type'] = 'task';

                    $notification_channel = getNotificationChannel($message['to']);

                    if($notification_channel == 'Система'){
                        event(new TaskDone($message));
                    }
                    elseif($notification_channel == 'Telegram'){
                        event(new TelegramNotify($message));
                    }
                    else {
                        event(new TaskDone($message));
                        event(new TelegramNotify($message));
                    }
                }
                $this->getUsersWorkRequests($work_request);
                return response()->json([
                    'bg-class' => 'bg-success',
                    'from' => 'Система',
                    'message' => __('work_request.done_task_successfully'),
                    'ajax' => view('work_request.ajax.main',[
                        'task' => $work_request
                    ])->render(),
                    'div_id' => 'main'
                ]);

            }

        }

        if($request->action == 'decline_task'){

            $history [] = [
                'user' => $user->id,
                'text' => 'Отказ от выполнения',
                'date' => Carbon::now()
            ];

            $work_request->update([
                'accepted_user_id' => null,
                'status' => 'Ожидает выполнения',
                'history' => serialize($history)
            ]);

            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'message' => __('work_request.decline_task_successfully')
            ]);
        }

        if($request->action == 'confirm_done_task'){

            $history [] = [
                'user' => $user->id,
                'text' => 'Выполнение принято',
                'date' => Carbon::now()
            ];

            $work_request->update([
                'active' => '0',
                'status' => 'Выполнен',
                'done' => Carbon::now(),
                'history' => serialize($history)
            ]);

            $this->getUsersWorkRequests($work_request);
            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'message' => __('work_request.confirm_done_task_successfully'),
                'ajax' => view('work_request.ajax.main',[
                    'task' => $work_request
                ])->render(),
                'div_id' => 'main'
            ]);

        }

        if($request->action == 'transfer_task'){

            $history [] = [
                'user' => $user->id,
                'text' => 'Перенаправлен пользователю '.userInfo($request->to_users)->name,
                'date' => Carbon::now()
            ];

            $additional_users = $work_request->additional_users;
            $responsible_user = array_map('intval', explode(',', $request->to_users));

            if (!empty($additional_users)){
                foreach ($responsible_user as $add_user){
                    array_push($additional_users, $add_user);
                }
                $to_users = array_unique(array_map('intval', $additional_users));
            }

            else $to_users = array_unique($responsible_user);


            $work_request->update([
                'send_to' => userInfo($request->to_users)->name,
                'responsible_user' => $responsible_user,
                'to_users' => array_map('intval', $to_users),
                'info' => 'пользователем '.$user->name,
                'status' => 'Ожидает выполнения',
                'accepted_user_id' => null,
                'history' => serialize($history)
            ]);

            foreach (explode(',', $request->to_users) as $user_id){
                $message = [
                    'bg_class' =>'bg-success',
                    'to' => $user_id,
                    'from' => 'системы',
                    'object_id' => $work_request->id,
                    'message' => __('work_request.you_got_redirected_task').' №'.$work_request->id.' '.$work_request->text.' ' . __('work_request.by_user') . ' '.$user->name
                ];
                $message['link'] = 'task/'.$work_request->id;
                $message['text'] = $message['message'];

                $message['inline_keyboard'] = [
                    'inline_keyboard' => [
                        [
                            ['text' => 'Открыть', 'url' => config('app.url').$message['link']],
                        ],
                    ]
                ];

                $message['action'] = 'notification';
                $message['type'] = 'task';

                $notification_channel = getNotificationChannel($message['to']);

                if($notification_channel == 'Система'){
                    event(new TaskDone($message));
                }
                elseif($notification_channel == 'Telegram'){
                    event(new TelegramNotify($message));
                }
                else {
                    event(new TaskDone($message));
                    event(new TelegramNotify($message));
                }
            }

            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'message' => __('work_request.transfer_task_successfully')
            ]);
        }

        if($request->action == 'delete_task'){

            $work_request->delete();

            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'message' => __('work_request.delete_task_successfully')
            ]);
        }

    }

    public function addFileToWorkRequest(Request $request){

        $work_request = WorkRequest::find($request->work_request_id);
        $comment = $request->comment;
        $user = auth()->user();
        if($request->hasFile('files')) {

            $folder = $work_request->id;

            $files = [];

            foreach ($request->file('files') as $file){

                $filename = preg_replace('/[^\.\,\-\_\@\?\!\:\$ a-zA-Z0-9А-Яа-я()]/u','', $file->getClientOriginalName());

                $url = $file->storeAs('public/Файлы запросов/'.$folder.'/'.$user->name, $filename);

                $files [] = [
                    'name' => $filename,
                    'url' => $url
                ];

            }

            $chat = unserialize($work_request->comment);
            $chat [] = [
                'user' => $user->id,
                'text' => $comment,
                'answer_to' => '',
                'file' => $files,
                'date' => Carbon::now()
            ];

            $work_request->update([
                'comment' => serialize($chat)
            ]);

            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'message' => __('work_request.upload_files_successfully'),
                'ajax' => view('work_request.ajax.files',[
                    'task' => $work_request
                ])->render(),
                'chat' => view('work_request.ajax.chat',[
                    'task' => $work_request
                ])->render()
            ]);
        }

        else {
            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'message' => __('general.first_choose_files'),
                'ajax' => view('work_request.ajax.files',[
                    'task' => $work_request
                ])->render()
            ]);
        }


    }

    public function incomeWorkRequest(){

        $income_tasks = WorkRequest::whereJsonContains('to_users' ,Auth::user()->id)
            ->where('active', '1')
            ->where('accepted_user_id', null)
            ->orderBy('created_at','desc')
            ->paginate(5, ['*'], 'income_tasks');
//        $income_tasks = null;
//
//        foreach ($my_tasks as $work_request){
//            $to_users_arr [] = $work_request->to_users;
//            if(in_array(Auth::user()->id, $work_request->to_users)){
//                $income_tasks [] = $work_request;
//            }
//        }
//
//        if (!Collection::hasMacro('paginate')) {
//
//            Collection::macro('paginate',
//                function ($perPage = 15, $page = null, $options = []) {
//                    $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
//                    return (new LengthAwarePaginator(
//                        $this->forPage($page, $perPage)->values()->all(), $this->count(), $perPage, $page, $options))
//                        ->withPath('');
//                });
//        }
//
//        $income_tasks = collect($income_tasks)->paginate(5);

        $accepted_tasks = WorkRequest::where('accepted_user_id', Auth::user()->id)
            ->where('active', '1')
            ->where('status', '<>', 'Отправлен на проверку')
            ->orderBy('created_at','desc')
            ->paginate(5, ['*'], 'accepted_tasks');

        $send_for_approval = WorkRequest::where('accepted_user_id', Auth::user()->id)
            ->where('active', '1')
            ->where('status', 'Отправлен на проверку')
            ->orderBy('created_at','desc')
            ->paginate(5, ['*'], 'accepted_tasks');

        $this->giveClass($accepted_tasks);
        $this->giveClass($income_tasks);
        $this->giveClass($send_for_approval);

        $users = User::whereHas('roles', function ($query) {
            $query->whereNotIn('name', ['super-admin']);
        })->get();

        return view('work_request.income', [
            'income_tasks' => $income_tasks,
            'accepted_tasks' => $accepted_tasks,
            'send_for_approval' => $send_for_approval,
            'users' => $users
        ]);

    }

    public function allIncomeWorkRequest(){

        $income_tasks = WorkRequest::whereJsonContains('to_users', Auth::user()->id)
            ->orderBy('created_at','desc')
            ->get();

//        $income_tasks = null;
//
//        foreach ($my_tasks as $work_request){
//            $to_users_arr [] = $work_request->to_users;
//            if(in_array(Auth::user()->id, $work_request->to_users)){
//                $income_tasks [] = $work_request;
//            }
//        }
//
//        $income_tasks = collect($income_tasks);

        $income_tasks = $this->giveClass($income_tasks);

        return view('work_request.all_income', [
            'all_income_tasks' => $income_tasks
        ]);

    }

    public function outcomeWorkRequest(){

        $outcome_tasks = WorkRequest::where('from_user_id', Auth::user()->id)
            ->orderBy('created_at','desc')
            ->get();

        $outcome_tasks = $this->giveClass($outcome_tasks);

        return view('work_request.outcome', [
            'outcome_tasks' => $outcome_tasks
        ]);

    }

    public function doneWorkRequest(){

        $done_tasks = WorkRequest::where('accepted_user_id', Auth::user()->id)
            ->whereIn('status', ['Выполнен', 'Отправлен на проверку'])
            ->orderBy('created_at','desc')
            ->get();

        $done_tasks = $this->giveClass($done_tasks);

        return view('work_request.done', [
            'done_tasks' => $done_tasks
        ]);

    }

    public function uploadUpd($id){

        $work_request = WorkRequest::findOrFail($id);

        if($work_request->model == 'upd'){
            $invoices_id = unserialize($work_request->object_array);
            $invoices = Invoice::whereIn('id', $invoices_id)->get();

            return view('work_request.upload_upd',[
                'invoices' => $invoices,
                'work_request' => $work_request
            ]);
        }

    }

    public function giveClass($work_requests){
        foreach ($work_requests as $work_request){
            switch($work_request->status){
                case 'Выполняется':
                    $work_request->class = 'primary';
                    break;
                case 'Ожидает выполнения':
                    $work_request->class = 'info';
                    break;
                case 'Выполнен':
                    $work_request->class = 'success';
                    break;
                case 'Отправлен на проверку':
                    $work_request->class = 'warning';
                    break;
                default:
                    $work_request->class = 'secondary';
            }
            if($work_request->deadline != '' && $work_request->deadline < Carbon::now() && $work_request->active == '1'){
                $work_request->overdue = true;
            }

            else $work_request->overdue = false;
        }
        return $work_requests;
    }

    public function getWorkRequestTable(WorkRequestFilter $filter, Request $request){

        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value

        $totalRecords = WorkRequest::filter($filter)->count();

        if($searchValue != ''){
            if($request->status != null && $request->status != 'Просрочена'){

                $records = WorkRequest::orderBy($columnName, $columnSortOrder)
                    ->where('status', $request->status)
                    ->where(function ($query) use ($searchValue) {
                        $query
                            ->orWhere('id', $searchValue)
                            ->orWhere('text', 'like', '%' . $searchValue . '%')
                            ->orWhere('name', 'like', '%' . $searchValue . '%')
                            ->orWhere('object', 'like', '%' . $searchValue . '%')
                            ->orWhere('comment', 'like', '%' . $searchValue . '%')
                            ->orWhere('send_to', 'like', '%' . $searchValue . '%');
                    })
                    ->orWhereHas('for_project', function ($q) use($searchValue)
                    {
                        $q->where('name', 'like', '%' . $searchValue . '%');
                    })
                    ->orWhereHas('invoice', function ($q) use($searchValue)
                    {
                        $q->where('amount', 'like', '%' . $searchValue . '%')->orWhere('amount_in_currency', 'like', '%' . $searchValue . '%');
                    })
                    ->select('work_requests.*')
                    ->skip($start)
                    ->take($rowperpage)
                    ->filter($filter)
                    ->get();

                $totalRecordswithFilter = $records->count();
            }

            elseif($request->status != null && $request->status == 'Просрочена'){

                $records = WorkRequest::orderBy($columnName, $columnSortOrder)
                    ->where('active', '1')
                    ->where(function ($query) {
                        $query
                            ->whereNotNull('deadline')
                            ->where('deadline', '<', Carbon::now());
                    })
                    ->where(function ($query) use ($searchValue) {
                        $query
                            ->orWhere('id', $searchValue)
                            ->orWhere('text', 'like', '%' . $searchValue . '%')
                            ->orWhere('name', 'like', '%' . $searchValue . '%')
                            ->orWhere('object', 'like', '%' . $searchValue . '%')
                            ->orWhere('comment', 'like', '%' . $searchValue . '%')
                            ->orWhere('send_to', 'like', '%' . $searchValue . '%');
                    })
                    ->orWhereHas('for_project', function ($q) use($searchValue)
                    {
                        $q->where('name', 'like', '%' . $searchValue . '%');
                    })
                    ->orWhereHas('invoice', function ($q) use($searchValue)
                    {
                        $q->where('amount', 'like', '%' . $searchValue . '%')->orWhere('amount_in_currency', 'like', '%' . $searchValue . '%');
                    })
                    ->select('work_requests.*')
                    ->skip($start)
                    ->take($rowperpage)
                    ->filter($filter)
                    ->get();

                $totalRecordswithFilter = $records->count();
            }
            else{

                $records = WorkRequest::orderBy($columnName, $columnSortOrder)
                    ->where('id', $searchValue)
                    ->orWhere('text', 'like', '%' . $searchValue . '%')
                    ->orWhere('name', 'like', '%' . $searchValue . '%')
                    ->orWhere('object', 'like', '%' . $searchValue . '%')
                    ->orWhere('comment', 'like', '%' . $searchValue . '%')
                    ->orWhere('send_to', 'like', '%' . $searchValue . '%')
                    ->orWhereHas('for_project', function ($q) use($searchValue)
                    {
                        $q->where('name', 'like', '%' . $searchValue . '%');
                    })
                    ->orWhereHas('invoice', function ($q) use($searchValue)
                    {
                        $q->where('amount', 'like', '%' . $searchValue . '%')->orWhere('amount_in_currency', 'like', '%' . $searchValue . '%');
                    })
                    ->select('work_requests.*')
                    ->skip($start)
                    ->take($rowperpage)
                    ->filter($filter)
                    ->get();

                $totalRecordswithFilter = $records->count();
            }

        }
        else {
            if($request->status != null && $request->status != 'Просрочена') {
                $totalRecordswithFilter = WorkRequest::filter($filter)->where('status', $request->status)->count();

                $records = WorkRequest::orderBy($columnName, $columnSortOrder)
                    ->where('status', $request->status)
                    ->select('work_requests.*')
                    ->skip($start)
                    ->take($rowperpage)
                    ->filter($filter)
                    ->get();
            }
            elseif($request->status != null && $request->status == 'Просрочена') {
                $totalRecordswithFilter = WorkRequest::filter($filter)
                    ->where('active', '1')
                    ->where(function ($query) {
                        $query
                            ->whereNotNull('deadline')
                            ->where('deadline', '<', Carbon::now());
                    })
                    ->count();

                $records = WorkRequest::orderBy($columnName, $columnSortOrder)
                    ->where('active', '1')
                    ->where(function ($query) {
                        $query
                            ->whereNotNull('deadline')
                            ->where('deadline', '<', Carbon::now());
                    })
                    ->select('work_requests.*')
                    ->skip($start)
                    ->take($rowperpage)
                    ->filter($filter)
                    ->get();
            }
            else {
                $totalRecordswithFilter = WorkRequest::filter($filter)->count();

                $records = WorkRequest::orderBy($columnName, $columnSortOrder)
                    ->select('work_requests.*')
                    ->skip($start)
                    ->take($rowperpage)
                    ->filter($filter)
                    ->get();
            }
        }


        $data_arr = array();

        $sno = $start + 1;

        $records = $this->giveClass($records);

        foreach ($records as $work_request) {

            $id = $work_request->id;

            $info = view('work_request.table.info', [
                'task' => $work_request
            ])->render();

            $users = view('work_request.table.users', [
                'task' => $work_request
            ])->render();

            $status = view('work_request.table.status', [
                'task' => $work_request
            ])->render();

            $time = view('work_request.table.time', [
                'task' => $work_request
            ])->render();

            if($request->filter == 'done'){
                $actions = view('work_request.table.done_actions', [
                    'task' => $work_request
                ])->render();
            }
            else {
                $actions = view('work_request.table.actions', [
                    'task' => $work_request
                ])->render();
            }

            $data_arr[] = array(
                "id" => $id,
                "info" => $info,
                "users" => $users,
                "status" => $status,
                "actions" => $actions
            );
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr,
            "filter" => $request->filter
        );

        //dd($response);

        echo json_encode($response);
        exit;
    }

    public function deleteRow($id){

        $work_request = WorkRequest::findOrFail($id);
        $number = $work_request->id;
        $work_request->delete();

        return response()->json([
            'bg-class' => 'bg-success',
            'from' => 'Система',
            'message' => __('work_request.delete_task_number_successfully', ['number' => $number])
        ]);
    }

    public function getUsersWorkRequests(WorkRequest $work_request){
        $users = User::whereHas('roles', function ($query) {
            $query->whereNotIn('name', ['super-admin']);
        })->get();

        $roles = Role::get()->whereNotIn('name', ['super-admin','user','special']);

        foreach ($roles as $role){
            if (in_array($role['ru_name'], ['Менеджер', 'Логист', 'Бухгалтер', 'Директор'])){
                $role_users ['Группа '. $role['ru_name'].'ы'] = User::role($role)->pluck('id')->toArray();
            }
            else {
                $role_users ['Группа '. $role['ru_name']] = User::role($role)->pluck('id')->toArray();
            }
        }

        $have_access = $work_request->to_users;
        is_null($work_request->from_user_id) ?: array_push($have_access, $work_request->from_user_id);

//        $additional_users = null;
//
//        if(!empty($work_request->additional_users)){
//            foreach($work_request->additional_users as $user) {
//                $additional_users [] = optional(userInfo($user))->name;
//            }
//            $additional_users = implode(', ', $additional_users);
//        }

        $work_request->user_roles = $role_users;
        $work_request->users = $users;
        $work_request->have_access = $have_access;
        //$work_request->additional_users = $additional_users;

    }

    public function checkOverdue(WorkRequest $work_request){

        if($work_request->deadline != '' && $work_request->deadline < Carbon::now() && $work_request->active == '1'){
            $work_request->overdue = true;
        }

        else $work_request->overdue = false;

    }

}
