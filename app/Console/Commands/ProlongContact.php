<?php

namespace App\Console\Commands;

use App\Events\TaskDone;
use App\Events\TelegramNotify;
use App\Models\Contract;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ProlongContact extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:prolong_contract';

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
        $today_add_month = Carbon::now()->addMonth()->format('Y-m-d');

        $contracts = Contract::where('date_period', '<', $today_add_month)->get();

        $to_users = User::role('logist')->pluck('id')->toArray();

        if ($contracts->isNotEmpty()){

            foreach ($contracts as $contract){

                if(!is_null($contract->client) || !is_null($contract->supplier)){

                    if ($contract->type == 'Клиент'){
                        $object = 'Договор №'.$contract->name.' с '.optional($contract->client)->name;
                        $model = 'client';
                        $model_id = $contract->client->id;
                    }
                    elseif ($contract->type == 'Поставщик'){
                        $object = 'Договор №'.$contract->name.' с '.optional($contract->supplier)->name;
                        $model = 'supplier';
                        $model_id = $contract->supplier->id;
                    }

                    $task_for_this_contract = Task::where('model', $model)->where('model_id', $model_id)->where('text', 'like', 'Необходимо продлить договор')->get();

                    if ($task_for_this_contract->isEmpty()){

                        $new_task = new Task();

                        $new_task->type = 'Система';
                        $new_task->model = $model;
                        $new_task->model_id = $model_id;
                        $new_task->object = $object;
                        $new_task->send_to = 'Группа Логисты';
                        $new_task->to_users = array_map('intval', $to_users);
                        $new_task->responsible_user = $to_users;
                        $new_task->text = 'Необходимо продлить договор';
                        $new_task->status = 'Ожидает выполнения';
                        $new_task->active = '1';

                        $new_task->save();

                        foreach ($to_users as $user){
                            $message = [
                                'bg_class' =>'bg-success',
                                'to' => $user,
                                'from' => 'системы',
                                'object_id' => $new_task->id,
                                'message' => __('console.new_task').' №' . $new_task->id . ': ' . __('console.need_to_prolong') .' '.$object
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
