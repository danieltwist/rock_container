<?php

namespace App\Console\Commands;

use App\Events\TaskDone;
use App\Events\TelegramNotify;
use App\Http\Traits\ContainerTrait;
use App\Models\Container;
use App\Models\ContainerGroup;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class SVVProlong extends Command
{
    use ContainerTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:prolong_svv';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     * @throws \Exception
     */
    public function handle()
    {

        $containers = Container::whereNotNull('svv')->get();
        if($containers->isNotEmpty()){
            foreach ($containers as $container){
                $task_for_this_container = Task::where('model', 'container')
                    ->where('model_id', $container->id)
                    ->where('text', 'like', 'Необходимо продлить СВВ')
                    ->where('active','1')
                    ->get();

                if ($task_for_this_container->isEmpty()){
                    $container->usage_dates = $this->getContainerUsageDates($container->id);
                    $svv = new Carbon($container->usage_dates['svv_date']);
                    $svv_minus_ten_days = $svv->subDays(7)->format('Y-m-d');
                    if($svv_minus_ten_days <= Carbon::now()->format('Y-m-d')) {

                        $object = 'Контейнер '.$container->name;
                        if (!is_null($container->project_id)){
                            $to_users = explode(',', $container->project->user_id);
                            $send_to = userInfo($container->project->user_id)->name;
                            $users = null;
                        }
                        else {
                            $to_users = User::role('supply')->pluck('id')->toArray();
                            $send_to = 'Группа Снабжение';
                        }
                        $new_task = new Task();

                        $new_task->type = 'Система';
                        $new_task->model = 'container';
                        $new_task->model_id = $container->id;
                        $new_task->object = $object;
                        $new_task->send_to = $send_to;
                        $new_task->responsible_user = array_map('intval', $to_users);
                        $new_task->to_users = $to_users;
                        $new_task->text = 'Необходимо продлить СВВ';
                        $new_task->status = 'Ожидает выполнения';
                        $new_task->active = '1';

                        $new_task->save();

                        if(!empty($to_users)) {
                            foreach ($to_users as $user) {
                                $message = [
                                    'bg_class' => 'bg-success',
                                    'to' => $user,
                                    'from' => 'системы',
                                    'object_id' => $new_task->id,
                                    'message' => __('console.new_task').' №' . $new_task->id . ': ' . __('console.need_to_prolong_svv') .' '.$container->name
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
                        }

                    }
                }

            }

        }

        /*
        $groups = ContainerGroup::whereNotNull('border_date')->whereHas('project', function (Builder $query) {
            $query->where('active', '1');
        })->get();

        if ($groups->isNotEmpty()){

            foreach ($groups as $group){

                $task_for_this_group = Task::where('model', 'container_group')->where('model_id', $group->id)->where('text', 'like', 'Необходимо продлить СВВ')->get();

                if ($task_for_this_group->isEmpty()){

                    $containers_list = unserialize($group->containers);

                    $need_prolong = false;

                    foreach ($containers_list as $container){

                        $container_from_group = Container::find($container);

                        $container_from_group->usage_dates = $this->getContainerUsageDates($container_from_group->id);

                        $svv = new Carbon($container_from_group->usage_dates['svv_date']);
                        $svv_minus_ten_days = $svv->subDays(10)->format('Y-m-d');

                        if($svv_minus_ten_days <= Carbon::now()->format('Y-m-d')) {
                            $need_prolong = true;
                            break;
                        }

                    }

                    if($need_prolong) {

                        $project = Project::find($group->project_id);

                        $object = 'Группа контейнеров №'.$group->id.' '.$group->name.' для проекта '.$project->name;

                        $new_task = new Task();

                        $new_task->type = 'Система';
                        $new_task->model = 'container_group';
                        $new_task->model_id = $group->id;
                        $new_task->object = $object;
                        $new_task->send_to = 'Пользователю';
                        $new_task->to_users = $project->user_id;
                        $new_task->text = 'Необходимо продлить СВВ';
                        $new_task->status = 'Ожидает выполнения';
                        $new_task->active = '1';

                        $new_task->save();

                        $message = [
                            'bg_class' =>'bg-success',
                            'to' => $project->user_id,
                            'from' => 'системы',
                            'message' => 'Новая задача: Необходимо продлить СВВ для '.$object
                        ];

                        event(new TaskDone($message));

                    }
                }
            }
        }*/
    }
}
