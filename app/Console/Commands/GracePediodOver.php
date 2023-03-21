<?php

namespace App\Console\Commands;

use App\Events\TaskDone;
use App\Events\TelegramNotify;
use App\Http\Traits\ContainerTrait;
use App\Models\Container;
use App\Models\ContainerGroup;
use App\Models\Project;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GracePediodOver extends Command
{
    Use ContainerTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:grace_period_almost_over';

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
        $groups = ContainerGroup::whereNotNull('start_date')->get();

        if ($groups->isNotEmpty()){

            foreach ($groups as $group){

                $task_for_this_group = Task::where('model', 'container_group')->where('model_id', $group->id)->where('text', 'like', 'Сообщите клиенту о скором окончании СНП')->get();

                if ($task_for_this_group->isEmpty()){

                    $containers_list = unserialize($group->containers);

                    foreach ($containers_list as $container){

                        $container_from_group = Container::find($container);

                        if ($container_from_group->start_date !='') {
                            $container_from_group->usage_dates = $this->getContainerUsageDates($container_from_group->id);

                            $snp = new Carbon($container_from_group->usage_dates['end_grace_date']);
                            $snp_minus_five_days = $snp->subDays(5)->format('Y-m-d');

                            if($snp_minus_five_days <= Carbon::now()->format('Y-m-d')) {

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
                                $new_task->text = 'Сообщите клиенту о скором окончании СНП';
                                $new_task->status = 'Ожидает выполнения';
                                $new_task->active = '1';

                                $new_task->save();

                                $message = [
                                    'bg_class' =>'bg-success',
                                    'to' => $project->user_id,
                                    'from' => 'системы',
                                    'object_id' => $new_task->id,
                                    'message' => __('console.new_task').' №' . $new_task->id . ': ' . __('console.grace_period_over') .' '.$object
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
    }
}
