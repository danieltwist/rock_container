<?php

namespace App\Console\Commands;

use App\Events\TaskDone;
use App\Events\TelegramNotify;
use App\Models\ContainerProject;
use App\Models\Task;
use App\Models\User;
use Illuminate\Console\Command;

class NeedProcessContainerProjects extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:need_process_container_projects';

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
        $container_projects = ContainerProject::whereIn('status', ['Добавлен вручную', 'Добавлен автоматически'])->get();

        $to_users = User::role('equipment')->pluck('id')->toArray();

        if($container_projects->count() > 0){

            $new_task = new Task();

            $new_task->type = 'Система';
            $new_task->model = 'container_project';
            $new_task->model_id = null;
            $new_task->object = 'Контейнерные проекты';
            $new_task->send_to = 'Группа Оборудование';
            $new_task->to_users = array_map('intval', $to_users);
            $new_task->responsible_user = $to_users;
            $new_task->text = 'Заполните контейнерные проекты';
            $new_task->status = 'Ожидает выполнения';
            $new_task->active = '1';

            $new_task->save();

            foreach ($to_users as $user){
                $message = [
                    'bg_class' =>'bg-success',
                    'to' => $user,
                    'from' => 'системы',
                    'object_id' => $new_task->id,
                    'message' => __('console.new_task').' №' . $new_task->id . ': ' . __('console.need_to_process_container_projects')
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
