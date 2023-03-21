<?php

namespace App\Http\Controllers\Task;

use App\Events\NotificationReceived;
use App\Events\TaskDone;
use App\Events\TelegramNotify;
use App\Filters\TaskFilter;
use App\Http\Controllers\Controller;
use App\Models\Container;
use App\Models\ContainerGroup;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class TaskController extends Controller
{
    public function index()
    {
        $role = Auth::user()->getRoleNames()[0];

        if(in_array($role,['super-admin','director'])){
            $tasks = Task::orderBy('id','DESC')->get();
            $tasks = $this->giveClass($tasks);

            return view('task.index', [
                'tasks' => $tasks,
            ]);
        }

        else{
            return redirect()->route('income_tasks');
        }

    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {

        $user = auth()->user();

        $new_task = new Task();

        $project_id = null;

        $new_task->type = 'Пользователь';
        $new_task->model = $request->model;

        if(in_array($request->model,['upd','free'])) {
            $model_id = null;
        } else $model_id = $request->model_id;

        $new_task->model_id = $model_id;

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
            $new_task->object_array = serialize($request->model_id);
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
        $new_task->object = $object;
        $new_task->name = $request->name;
        $new_task->text = $request->text;
        $new_task->from_user_id = $user->id;
        $new_task->send_to = $send_to_text;
        $new_task->deadline = $request->task_deadline;
        $new_task->can_change_deadline = $request->can_change_deadline;
        $new_task->check_work = $request->check_work;
        $new_task->additional_users = $send_to_additional_users;
        $new_task->to_users = array_map('intval', $to_users);
        $new_task->responsible_user = $responsible_user;
        $new_task->active = '1';
        $new_task->status = 'Ожидает выполнения';
        $new_task->project_id = $project_id;

        $new_task->save();

        if($request->hasFile('files')) {

            $folder = $new_task->id;

            $files = [];

            foreach ($request->file('files') as $file) {

                $filename = preg_replace('/[^\.\,\-\_\@\?\!\:\$ a-zA-Z0-9А-Яа-я()]/u','', $file->getClientOriginalName());
                $url = $file->storeAs('public/Файлы задач/' . $folder . '/' . $user->name, $filename);

                $files [] = [
                    'name' => $filename,
                    'url' => $url
                ];

            }

            Task::find($new_task->id)->update([
                'file' => serialize($files)
            ]);
        }



        foreach ($to_users as $user_id){
            $message = [
                'bg_class' =>'bg-success',
                'to' => $user_id,
                'from' => 'системы',
                'object_id' => $new_task->id,
                'message' => __('console.new_task').' №'.$new_task->id.' '.$new_task->text.' '. __('general.from') .' '.$user->name
            ];

            $message['link'] = 'task/'.$new_task->id;
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
            'message' => __('task.created_successfully')
        ]);
    }

    public function show($id)
    {
        $task = Task::findOrFail($id);
        $this->getUsersTask($task);
        $this->checkOverdue($task);

        return view('task.show',[
            'task' => $task
        ]);
    }

    public function edit($id)
    {
        $task = Task::find($id);

        return view('task.edit',[
            'task' => $task
        ]);
    }

    public function update(Request $request, $id)
    {
        $task = Task::find($id);

        $user = auth()->user();

        $project_id = null;

        if(!in_array($task->model, ['supplier', 'client'])){
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
                $task->object_array = serialize($request->model_id);
                foreach ($request->model_id as $invoice){
                    $invoice = Invoice::find($invoice);
                    $project_id = $invoice->project_id;
                    break;
                }
            }

            if ($request->model == 'free'){
                $object = null;
            }

            $task->object = $object;
            $task->model = $request->model;
            $task->model_id = $model_id;
            $task->project_id = $project_id;

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


        $task->name = $request->name;
        $task->text = $request->text;
        $task->from_user_id = $user->id;
        $task->send_to = $send_to_text;
        $task->deadline = $request->task_deadline;
        $task->can_change_deadline = $request->can_change_deadline;
        $task->check_work = $request->check_work;
        $task->additional_users = $send_to_additional_users;
        $task->to_users = array_map('intval', $to_users);
        $task->responsible_user = $responsible_user;

        $task->save();

        if($request->hasFile('files')) {

            $folder = $task->id;

            $files = [];

            foreach ($request->file('files') as $file) {

                $url = $file->storeAs('public/Файлы задач/' . $folder . '/' . $user->name, $file->getClientOriginalName());

                $files [] = [
                    'name' => $file->getClientOriginalName(),
                    'url' => $url
                ];

            }

            Task::find($task->id)->update([
                'file' => serialize($files)
            ]);
        }

        return redirect()->route('task.show', $task->id)->withSuccess(__('task.updated_successfully'));

    }

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->back()->withSuccess(__('task.removed_successfully'));
    }

    public function handler(Request $request)
    {
        $task = Task::find($request->task_id);

        $comment = $request->comment;
        $user = auth()->user();
        $history = unserialize($task->history);

        if($request->action == 'add_chat_record'){
            if($comment != ''){
                $chat = unserialize($task->comment);
                $chat [] = [
                    'user' => $user->id,
                    'text' => $comment,
                    'answer_to' => $request->answer_to,
                    'file' => '',
                    'date' => Carbon::now()
                ];

                $task->update([
                    'comment' => serialize($chat)
                ]);

                if($request->notify_users != ''){

                    $text = $user->name. ' ' . __('task.send_message_in_task').$task->id.':'.PHP_EOL.'<b>'.$comment.'</b>';
                    $link = 'task/'.$task->id;

                    foreach ($request->notify_users as $user_id){

                        if(!in_array($user_id, $task->to_users)){
                            $to_users = $task->to_users;
                            $to_users [] = $user_id;
                            $to_users = array_unique(array_map('intval', $to_users));
                            $additional_users = $task->additional_users;
                            $additional_users [] = $user_id;
                            $additional_users = array_unique(array_map('intval', $additional_users));

                            $task->update([
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
                                    ['text' => 'Ответить', 'callback_data' => 'answer:task:'.$task->id.':'.array_key_last($chat)]
                                ],
                            ]
                        ];

                        $message['action'] = 'answer:task:'.$task->id.':'.array_key_last($chat).':start_message';

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
                    'message' => __('task.add_chat_record_successfully'),
                    'ajax' => view('task.ajax.chat',[
                        'task' => $task
                    ])->render(),
                    'div_id' => 'chat'
                ]);
            }
            else{
                return response()->json([
                    'bg-class' => 'bg-danger',
                    'from' => 'Система',
                    'message' => __('task.fill_text')
                ]);
            }


        }

        if($request->action == 'delete_chat_record'){

            $chat = unserialize($task->comment);

            if($chat[$request->message_id]['file'] != ''){
                foreach ($chat[$request->message_id]['file'] as $file){
                    Storage::delete($file['url']);
                }
            }

            unset($chat[$request->message_id]);

            $task->update([
                'comment' => serialize($chat)
            ]);

            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'message' => __('task.delete_chat_record_successfully'),
                'ajax' => view('task.ajax.chat',[
                    'task' => $task
                ])->render(),
                'div_id' => 'chat'
            ]);
        }

        if($request->action == 'get_task'){

            $history [] = [
                'user' => $user->id,
                'text' => 'Принята к выполнению',
                'date' => Carbon::now()
            ];

            if ($task->accepted_user_id == ''){
                $task->update([
                    'accepted_user_id' => $user->id,
                    'status' => 'Выполняется',
                    'history' => serialize($history)
                ]);

                $this->getUsersTask($task);
                return response()->json([
                    'bg-class' => 'bg-success',
                    'from' => 'Система',
                    'message' => __('task.get_task_successfully'),
                    'ajax' => view('task.ajax.main',[
                        'task' => $task
                    ])->render(),
                    'div_id' => 'main'
                ]);

            }
            else {
                $this->getUsersTask($task);
                return response()->json([
                    'bg-class' => 'bg-success',
                    'from' => 'Система',
                    'message' => __('task.get_task_already_accepted').' '.$task->accepted_user->name,
                    'ajax' => view('task.ajax.main',[
                        'task' => $task
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

            $additional_users = $task->additional_users;
            $additional_users [] = $request->to_users;
            $additional_users = array_unique(array_map('intval', $additional_users));

            $to_users = $task->to_users;
            $to_users [] = $request->to_users;
            $to_users = array_unique(array_map('intval', $to_users));

            $task->update([
                'to_users' => $to_users,
                'additional_users' => $additional_users,
                'history' => serialize($history)
            ]);

            $this->getUsersTask($task);

            $text = __('task.you_was_added_successfully').' '.$task->id.' '.$task->text;
            $link = 'task/'.$task->id;

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
                'message' => __('task.add_user_successfully'),
                'ajax' => view('task.ajax.users',[
                    'task' => $task
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

            $task->update([
                'deadline' => $request->deadline,
                'history' => serialize($history)
            ]);
            $this->getUsersTask($task);
            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'message' => __('task.change_deadline_successfully'),
                'ajax' => view('task.ajax.main',[
                    'task' => $task
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

            $task->update([
                'status' => 'Выполняется',
                'active' => '1',
                'history' => serialize($history)
            ]);

            $message = [
                'bg_class' =>'bg-success',
                'to' => $task->accepted_user_id,
                'from' => 'системы',
                'object_id' => $task->id,
                'message' => __('general.task').' №'.$task->id.' '.$task->text.' ' . __('task.was_reloaded')
            ];

            $message['link'] = 'task/'.$task->id;
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

            $this->getUsersTask($task);

            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'message' => __('task.reload_task_successfully'),
                'ajax' => view('task.ajax.main',[
                    'task' => $task
                ])->render(),
                'div_id' => 'main'
            ]);

        }

        if($request->action == 'done_task'){

            if(!is_null($task->check_work)){

                $history [] = [
                    'user' => $user->id,
                    'text' => 'Отправлена на проверку',
                    'date' => Carbon::now()
                ];

                $task->update([
                    'active' => '1',
                    'status' => 'Отправлена на проверку',
                    'done' => Carbon::now(),
                    'history' => serialize($history)
                ]);

                if($task->type == 'Пользователь'){
                    $message = [
                        'bg_class' =>'bg-success',
                        'to' => $task->from_user_id,
                        'from' => 'Система',
                        'object_id' => $task->id,
                        'message' => __('general.task').' №'.$task->id.' '.$task->text.' ' . __('task.was_made_by_user') .' '. $task->accepted_user->name.' '. __('task.and_waiting_for_approval')
                    ];

                    $message['link'] = 'task/'.$task->id;
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

                $this->getUsersTask($task);
                return response()->json([
                    'bg-class' => 'bg-success',
                    'from' => 'Система',
                    'message' => __('task.done_task_send_to_agree_successfully'),
                    'ajax' => view('task.ajax.main',[
                        'task' => $task
                    ])->render(),
                    'div_id' => 'main'
                ]);

            }
            else {

                $history [] = [
                    'user' => $user->id,
                    'text' => 'Выполнена',
                    'date' => Carbon::now()
                ];

                $task->update([
                    'active' => '0',
                    'status' => 'Выполнена',
                    'done' => Carbon::now(),
                    'history' => serialize($history)
                ]);

                if($task->type == 'Пользователь'){
                    $message = [
                        'bg_class' =>'bg-success',
                        'to' => $task->from_user_id,
                        'from' => 'Система',
                        'object_id' => $task->id,
                        'message' => __('general.task').' №'.$task->id.' '.$task->text.' ' . __('task.was_made_by_user') .' '. $task->accepted_user->name
                    ];
                    $message['link'] = 'task/'.$task->id;
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
                $this->getUsersTask($task);
                return response()->json([
                    'bg-class' => 'bg-success',
                    'from' => 'Система',
                    'message' => __('task.done_task_successfully'),
                    'ajax' => view('task.ajax.main',[
                        'task' => $task
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

            $task->update([
                'accepted_user_id' => null,
                'status' => 'Ожидает выполнения',
                'history' => serialize($history)
            ]);

            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'message' => __('task.decline_task_successfully')
            ]);
        }

        if($request->action == 'confirm_done_task'){

            $history [] = [
                'user' => $user->id,
                'text' => 'Выполнение принято',
                'date' => Carbon::now()
            ];

            $task->update([
                'active' => '0',
                'status' => 'Выполнена',
                'done' => Carbon::now(),
                'history' => serialize($history)
            ]);

            $this->getUsersTask($task);
            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'message' => __('task.confirm_done_task_successfully'),
                'ajax' => view('task.ajax.main',[
                    'task' => $task
                ])->render(),
                'div_id' => 'main'
            ]);

        }

        if($request->action == 'transfer_task'){

            $history [] = [
                'user' => $user->id,
                'text' => 'Перенаправлена пользователю '.userInfo($request->to_users)->name,
                'date' => Carbon::now()
            ];

            $additional_users = $task->additional_users;
            $responsible_user = array_map('intval', explode(',', $request->to_users));

            if (!empty($additional_users)){
                foreach ($responsible_user as $add_user){
                    array_push($additional_users, $add_user);
                }
                $to_users = array_unique(array_map('intval', $additional_users));
            }

            else $to_users = array_unique($responsible_user);


            $task->update([
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
                    'object_id' => $task->id,
                    'message' => __('task.you_got_redirected_task').' №'.$task->id.' '.$task->text.' ' . __('task.by_user') . ' '.$user->name
                ];
                $message['link'] = 'task/'.$task->id;
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
                'message' => __('task.transfer_task_successfully')
            ]);
        }

        if($request->action == 'delete_task'){

            $task->delete();

            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'message' => __('task.delete_task_successfully')
            ]);
        }

    }

    public function addFileToTask(Request $request){

        $task = Task::find($request->task_id);
        $comment = $request->comment;
        $user = auth()->user();
        if($request->hasFile('files')) {

            $folder = $task->id;

            $files = [];

            foreach ($request->file('files') as $file){

                $filename = preg_replace('/[^\.\,\-\_\@\?\!\:\$ a-zA-Z0-9А-Яа-я()]/u','', $file->getClientOriginalName());

                $url = $file->storeAs('public/Файлы задач/'.$folder.'/'.$user->name, $filename);

                $files [] = [
                    'name' => $filename,
                    'url' => $url
                ];

            }

            $chat = unserialize($task->comment);
            $chat [] = [
                'user' => $user->id,
                'text' => $comment,
                'answer_to' => '',
                'file' => $files,
                'date' => Carbon::now()
            ];

            $task->update([
                'comment' => serialize($chat)
            ]);

            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'message' => __('task.upload_files_successfully'),
                'ajax' => view('task.ajax.files',[
                    'task' => $task
                ])->render(),
                'chat' => view('task.ajax.chat',[
                    'task' => $task
                ])->render()
            ]);
        }

        else {
            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'message' => __('general.first_choose_files'),
                'ajax' => view('task.ajax.files',[
                    'task' => $task
                ])->render()
            ]);
        }


    }

    public function incomeTask(){

        $income_tasks = Task::whereJsonContains('to_users' ,Auth::user()->id)
            ->where('active', '1')
            ->where('accepted_user_id', null)
            ->orderBy('created_at','desc')
            ->paginate(5, ['*'], 'income_tasks');
//        $income_tasks = null;
//
//        foreach ($my_tasks as $task){
//            $to_users_arr [] = $task->to_users;
//            if(in_array(Auth::user()->id, $task->to_users)){
//                $income_tasks [] = $task;
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

        $accepted_tasks = Task::where('accepted_user_id', Auth::user()->id)
            ->where('active', '1')
            ->where('status', '<>', 'Отправлена на проверку')
            ->orderBy('created_at','desc')
            ->paginate(5, ['*'], 'accepted_tasks');

        $send_for_approval = Task::where('accepted_user_id', Auth::user()->id)
            ->where('active', '1')
            ->where('status', 'Отправлена на проверку')
            ->orderBy('created_at','desc')
            ->paginate(5, ['*'], 'accepted_tasks');

        $this->giveClass($accepted_tasks);
        $this->giveClass($income_tasks);
        $this->giveClass($send_for_approval);

        $users = User::whereHas('roles', function ($query) {
            $query->whereNotIn('name', ['super-admin']);
        })->get();

        return view('task.income', [
            'income_tasks' => $income_tasks,
            'accepted_tasks' => $accepted_tasks,
            'send_for_approval' => $send_for_approval,
            'users' => $users
        ]);

    }

    public function allIncomeTask(){

        $income_tasks = Task::whereJsonContains('to_users', Auth::user()->id)
            ->orderBy('created_at','desc')
            ->get();

//        $income_tasks = null;
//
//        foreach ($my_tasks as $task){
//            $to_users_arr [] = $task->to_users;
//            if(in_array(Auth::user()->id, $task->to_users)){
//                $income_tasks [] = $task;
//            }
//        }
//
//        $income_tasks = collect($income_tasks);

        $income_tasks = $this->giveClass($income_tasks);

        return view('task.all_income', [
            'all_income_tasks' => $income_tasks
        ]);

    }

    public function outcomeTask(){

        $outcome_tasks = Task::where('from_user_id', Auth::user()->id)
            ->orderBy('created_at','desc')
            ->get();

        $outcome_tasks = $this->giveClass($outcome_tasks);

        return view('task.outcome', [
            'outcome_tasks' => $outcome_tasks
        ]);

    }

    public function doneTask(){

        $done_tasks = Task::where('accepted_user_id', Auth::user()->id)
            ->whereIn('status', ['Выполнена', 'Отправлена на проверку'])
            ->orderBy('created_at','desc')
            ->get();

        $done_tasks = $this->giveClass($done_tasks);

        return view('task.done', [
            'done_tasks' => $done_tasks
        ]);

    }

    public function uploadUpd($id){

        $task = Task::findOrFail($id);

        if($task->model == 'upd'){
            $invoices_id = unserialize($task->object_array);
            $invoices = Invoice::whereIn('id', $invoices_id)->get();

            return view('task.upload_upd',[
                'invoices' => $invoices,
                'task' => $task
            ]);
        }

    }

    public function giveClass($tasks){
        foreach ($tasks as $task){
            switch($task->status){
                case 'Выполняется':
                    $task->class = 'primary';
                    break;
                case 'Ожидает выполнения':
                    $task->class = 'info';
                    break;
                case 'Выполнена':
                    $task->class = 'success';
                    break;
                case 'Отправлена на проверку':
                    $task->class = 'warning';
                    break;
                default:
                    $task->class = 'secondary';
            }
            if($task->deadline != '' && $task->deadline < Carbon::now() && $task->active == '1'){
                $task->overdue = true;
            }

            else $task->overdue = false;
        }
        return $tasks;
    }

    public function getTaskTable(TaskFilter $filter, Request $request){

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

        $totalRecords = Task::filter($filter)->count();

        if($searchValue != ''){
            if($request->status != null && $request->status != 'Просрочена'){

                $records = Task::orderBy($columnName, $columnSortOrder)
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
                    ->select('tasks.*')
                    ->skip($start)
                    ->take($rowperpage)
                    ->filter($filter)
                    ->get();

                $totalRecordswithFilter = $records->count();
            }

            elseif($request->status != null && $request->status == 'Просрочена'){

                $records = Task::orderBy($columnName, $columnSortOrder)
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
                    ->select('tasks.*')
                    ->skip($start)
                    ->take($rowperpage)
                    ->filter($filter)
                    ->get();

                $totalRecordswithFilter = $records->count();
            }
            else{

                $records = Task::orderBy($columnName, $columnSortOrder)
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
                    ->select('tasks.*')
                    ->skip($start)
                    ->take($rowperpage)
                    ->filter($filter)
                    ->get();

                $totalRecordswithFilter = $records->count();
            }

        }
        else {
            if($request->status != null && $request->status != 'Просрочена') {
                $totalRecordswithFilter = Task::filter($filter)->where('status', $request->status)->count();

                $records = Task::orderBy($columnName, $columnSortOrder)
                    ->where('status', $request->status)
                    ->select('tasks.*')
                    ->skip($start)
                    ->take($rowperpage)
                    ->filter($filter)
                    ->get();
            }
            elseif($request->status != null && $request->status == 'Просрочена') {
                $totalRecordswithFilter = Task::filter($filter)
                    ->where('active', '1')
                    ->where(function ($query) {
                        $query
                            ->whereNotNull('deadline')
                            ->where('deadline', '<', Carbon::now());
                    })
                    ->count();

                $records = Task::orderBy($columnName, $columnSortOrder)
                    ->where('active', '1')
                    ->where(function ($query) {
                        $query
                            ->whereNotNull('deadline')
                            ->where('deadline', '<', Carbon::now());
                    })
                    ->select('tasks.*')
                    ->skip($start)
                    ->take($rowperpage)
                    ->filter($filter)
                    ->get();
            }
            else {
                $totalRecordswithFilter = Task::filter($filter)->count();

                $records = Task::orderBy($columnName, $columnSortOrder)
                    ->select('tasks.*')
                    ->skip($start)
                    ->take($rowperpage)
                    ->filter($filter)
                    ->get();
            }
        }


        $data_arr = array();

        $sno = $start + 1;

        $records = $this->giveClass($records);

        foreach ($records as $task) {

            $id = $task->id;

            $info = view('task.table.info', [
                'task' => $task
            ])->render();

            $users = view('task.table.users', [
                'task' => $task
            ])->render();

            $status = view('task.table.status', [
                'task' => $task
            ])->render();

            $time = view('task.table.time', [
                'task' => $task
            ])->render();

            if($request->filter == 'done'){
                $actions = view('task.table.done_actions', [
                    'task' => $task
                ])->render();
            }
            else {
                $actions = view('task.table.actions', [
                    'task' => $task
                ])->render();
            }

            $data_arr[] = array(
                "id" => $id,
                "info" => $info,
                "accepted_user_id" => $users,
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

        $task = Task::findOrFail($id);
        $number = $task->id;
        $task->delete();

        return response()->json([
            'bg-class' => 'bg-success',
            'from' => 'Система',
            'message' => __('task.delete_task_number_successfully', ['number' => $number])
        ]);
    }

    public function getUsersTask(Task $task){
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

        $have_access = $task->to_users;
        is_null($task->from_user_id) ?: array_push($have_access, $task->from_user_id);

//        $additional_users = null;
//
//        if(!empty($task->additional_users)){
//            foreach($task->additional_users as $user) {
//                $additional_users [] = optional(userInfo($user))->name;
//            }
//            $additional_users = implode(', ', $additional_users);
//        }

        $task->user_roles = $role_users;
        $task->users = $users;
        $task->have_access = $have_access;
        //$task->additional_users = $additional_users;

    }

    public function checkOverdue(Task $task){

        if($task->deadline != '' && $task->deadline < Carbon::now() && $task->active == '1'){
            $task->overdue = true;
        }

        else $task->overdue = false;

    }

}
