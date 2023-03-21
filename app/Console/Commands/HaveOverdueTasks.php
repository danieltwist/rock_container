<?php

namespace App\Console\Commands;

use App\Events\NotificationReceived;
use App\Events\TelegramNotify;
use App\Models\Notification;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Console\Command;

class HaveOverdueTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:have_overdue_task';

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
        $tasks = Task::select('accepted_user_id')
            ->whereNotNull('accepted_user_id')
            ->where('active', '!=', '0')
            ->where(function ($query) {
            $query
                ->whereNotNull('deadline')
                ->where('deadline', '<', Carbon::now());
        })->groupBy('accepted_user_id')->get()->toArray();

        foreach ($tasks as $task){

            $notification = [
                'from' => 'Система',
                'to' => $task['accepted_user_id'],
                'text' => __('console.have_overdue_tasks'),
                'link' => 'task/income',
                'class' => 'bg-danger'
            ];

            $notification['inline_keyboard'] = [
                'inline_keyboard' => [
                    [
                        ['text' => 'Открыть', 'url' => config('app.url').$notification['link']],
                    ],
                ]
            ];

            $notification['action'] = 'notification';

            $notification_channel = getNotificationChannel($task['accepted_user_id']);

            if($notification_channel == 'Система'){
                event(new NotificationReceived($notification));
            }
            elseif($notification_channel == 'Telegram'){
                event(new TelegramNotify($notification));
            }
            else {
                event(new NotificationReceived($notification));
                event(new TelegramNotify($notification));
            }

        }
    }
}
