<?php

namespace App\Console\Commands;

use App\Events\NotificationReceived;
use App\Events\TelegramNotify;
use App\Models\Container;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Console\Command;

class ThereAreUnusedContainers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:there_are_unused_containers';

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
        $containers_count = Container::whereNull('project_id')->whereNull('archive')->count();

        $to_users = User::role('equipment')->pluck('id')->toArray();

        if($containers_count > 0){

            foreach ($to_users as $user_id){

                $notification = [
                    'from' => 'Система',
                    'to' => $user_id,
                    'text' => __('console.there_are_unused_containers'),
                    'link' => 'container?free',
                    'class' => 'bg-info'
                ];

                $notification['inline_keyboard'] = [
                    'inline_keyboard' => [
                        [
                            ['text' => 'Открыть', 'url' => config('app.url').$notification['link']],
                        ],
                    ]
                ];

                $notification['action'] = 'notification';

                $notification_channel = getNotificationChannel($notification['to']);

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
}
