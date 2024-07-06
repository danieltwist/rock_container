<?php

namespace App\Console\Commands;

use App\Events\NotificationReceived;
use App\Events\TelegramNotify;
use App\Http\Controllers\Notification\NotificationController;
use App\Models\Notification;
use App\Models\Task;
use App\Models\User;
use Illuminate\Console\Command;

class NeedDoSomeTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:have_tasks';

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

        $tasks = Task::select('accepted_user_id')->whereNotNull('accepted_user_id')->where('active', '!=', '0')->groupBy('accepted_user_id')->get()->toArray();

        foreach ($tasks as $task){
            $user_role = User::find($task['accepted_user_id'])->getRoleNames()[0];
            if($user_role != 'director') {
                $notification = [
                    'from' => 'Система',
                    'to' => $task['accepted_user_id'],
                    'text' => __('console.need_to_do_some_tasks'),
                    'link' => 'task/income',
                    'class' => 'bg-info'
                ];

                $notification['inline_keyboard'] = [
                    'inline_keyboard' => [
                        [
                            ['text' => 'Открыть', 'url' => config('app.url') . $notification['link']],
                        ],
                    ]
                ];

                $notification['action'] = 'notification';

                $notification_channel = getNotificationChannel($notification['to']);

                if ($notification_channel == 'Система') {
                    event(new NotificationReceived($notification));
                } elseif ($notification_channel == 'Telegram') {
                    event(new TelegramNotify($notification));
                } else {
                    event(new NotificationReceived($notification));
                    event(new TelegramNotify($notification));
                }
            }

        }

    }
}
