<?php

namespace App\Console\Commands;

use App\Events\TaskDone;
use App\Events\TelegramNotify;
use App\Models\ContainerGroup;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Console\Command;

class FreightLocation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:notify_buyer_about_location';

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
     */
    public function handle()
    {
        $projects = Project::where('active','1')->get();

        foreach ($projects as $key => $value) {

            $container_groups = ContainerGroup::where('project_id', $value->id)->get();

            if($container_groups->isNotEmpty()){
                $forget = false;

                foreach ($container_groups as $group){
                    if ($group->border_date == ''){
                        $forget = true;
                        break;
                    }
                }

                $forget ?: $projects->forget($key);

            }
            else {
                $projects->forget($key);
            }

        }

        if($projects->isNotEmpty()) {

            foreach ($projects as $project) {

                $object = $project->name;

                $new_task = new Task();

                $new_task->type = 'Система';
                $new_task->model = 'project';
                $new_task->model_id = $project->id;
                $new_task->object = 'Проект '.$object;
                $new_task->send_to = userInfo($project->user_id)->name;
                $new_task->to_users = array_map('intval', explode(',', $project->user_id));
                $new_task->responsible_user = explode(',', $project->user_id);
                $new_task->text = 'Сообщите клиенту о местоположении груза';
                $new_task->status = 'Ожидает выполнения';
                $new_task->active = '1';

                $new_task->save();

                $message = [
                    'bg_class' =>'bg-success',
                    'to' => $project->user_id,
                    'from' => 'системы',
                    'object_id' => $new_task->id,
                    'message' => __('console.new_task').' №' . $new_task->id . ': ' . __('console.freight_location') .' '.$object
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
