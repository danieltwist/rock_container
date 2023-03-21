<?php

namespace App\Console\Commands;

use App\Events\NotificationReceived;
use App\Events\TaskDone;
use App\Events\TelegramNotify;
use App\Models\ContainerGroup;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Console\Command;

class AfterBorderDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:documents_after_border';

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
        $groups_with_border_date = ContainerGroup::whereNotNull('border_date')->get();

        if ($groups_with_border_date->isNotEmpty()){

            foreach ($groups_with_border_date as $group){

                $task_for_this_group = Task::where('model', 'container_group')->where('model_id', $group->id)->where('text', 'like', 'Запросите документы у клиента после пересечения границы')->get();

                if ($task_for_this_group->isEmpty()){

                    $project = Project::find($group->project_id);

                    $object = 'Группа контейнеров №'.$group->id.' '.$group->name.' для проекта '.$project->name;

                    $new_task = new Task();

                    $new_task->type = 'Система';
                    $new_task->model = 'container_group';
                    $new_task->model_id = $group->id;
                    $new_task->object = $object;
                    $new_task->send_to = userInfo($project->user_id)->name;
                    $new_task->to_users = array_map('intval', explode(',', $project->user_id));
                    $new_task->responsible_user = explode(',', $project->user_id);
                    $new_task->text = 'Запросите документы у клиента после пересечения границы';
                    $new_task->status = 'Ожидает выполнения';
                    $new_task->active = '1';

                    $new_task->save();

                    $message = [
                        'bg_class' =>'bg-success',
                        'to' => $project->user_id,
                        'from' => 'системы',
                        'object_id' => $new_task->id,
                        'message' => __('console.new_task').' №' . $new_task->id . ': ' . __('console.after_boarding_documents') . ' '. __('general.for') .' '.$object
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
