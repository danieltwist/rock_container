<?php

namespace App\Console\Commands;

use App\Events\TaskDone;
use App\Events\TelegramNotify;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Console\Command;

class NeedUploadApplicationFromClient extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:upload_application_from_client';

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
        $active_projects = Project::where('active', '1')->where('status', '<>', 'Черновик')->get();

        if ($active_projects->isNotEmpty()){
            foreach ($active_projects as $project){
                if ($project->applications->isEmpty()){

                    $object = 'Проект '.$project->name;

                    $new_task = new Task();

                    $new_task->type = 'Система';
                    $new_task->model = 'project';
                    $new_task->model_id = $project->id;
                    $new_task->object = $object;
                    $new_task->send_to = userInfo($project->user_id)->name;
                    $new_task->responsible_user = explode(',',$project->user_id);
                    $new_task->to_users = array_map('intval', explode(',',$project->user_id));
                    $new_task->text = 'Загрузите заявку от клиента';
                    $new_task->status = 'Ожидает выполнения';
                    $new_task->active = '1';

                    $new_task->save();

                    $message = [
                        'bg_class' =>'bg-success',
                        'to' => $project->user_id,
                        'from' => 'системы',
                        'object_id' => $new_task->id,
                        'message' => __('console.new_task').' №' . $new_task->id . ': ' . __('console.need_to_upload_application_from_client') .' '.$project->name
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
