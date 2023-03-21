<?php

namespace App\Console\Commands;

use App\Events\TaskDone;
use App\Events\TelegramNotify;
use App\Models\Project;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Console\Command;

class NotifyClientAboutPayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:notify_client_about_payment';

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
        $unpaid_projects = Project::where('paid', 'Не оплачен')->get();

        if ($unpaid_projects->isNotEmpty()) {
            foreach ($unpaid_projects as $project) {
                if (!is_null($project->planned_payment_date)) {
                    $planned_payment_date = new Carbon($project->planned_payment_date);
                    $planned_payment_date_minus_five_days = $planned_payment_date->subDays(5)->format('Y-m-d');

                    if ($planned_payment_date_minus_five_days <= Carbon::now()->format('Y-m-d')) {

                        $task_for_this_project = Task::where('model', 'project')
                            ->where('model_id', $project->id)
                            ->where('text', 'like', 'Поторопите клиента к оплате')
                            ->where('active', '1')
                            ->get();

                        if ($task_for_this_project->isEmpty()) {

                            $need_new_task = false;

                            foreach ($project->invoices as $invoice){
                                if ($invoice->direction == 'Доход' && $invoice->status != 'Оплачен'){
                                    $need_new_task = true;
                                    break;
                                }
                            }

                            if($need_new_task){
                                $object = 'Проект ' . $project->name;

                                $new_task = new Task();
                                $new_task->type = 'Система';
                                $new_task->model = 'project';
                                $new_task->model_id = $project->id;
                                $new_task->object = $object;
                                $new_task->send_to = userInfo($project->user_id)->name;
                                $new_task->responsible_user = explode(',',$project->user_id);
                                $new_task->to_users = array_map('intval', explode(',',$project->user_id));
                                $new_task->text = 'Поторопите клиента к оплате';
                                $new_task->status = 'Ожидает выполнения';
                                $new_task->active = '1';

                                $new_task->save();

                                $message = [
                                    'bg_class' =>'bg-success',
                                    'to' => $project->user_id,
                                    'from' => 'системы',
                                    'object_id' => $new_task->id,
                                    'message' => __('console.new_task').' №' . $new_task->id . ': ' . __('console.notify_client_about_payment') .' '.$project->name
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
