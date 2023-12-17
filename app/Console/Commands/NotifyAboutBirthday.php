<?php

namespace App\Console\Commands;

use App\Events\NotificationReceived;
use App\Events\TelegramNotify;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class NotifyAboutBirthday extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:birthday';

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

    public function handle()
    {
        $upcoming_birthdays = null;
        foreach (User::all() as $user){
            if(!is_null($user->birthday)){
                $birthday_minus_three_days = $user->birthday->subDays(3)->format(date('Y').'-m-d');

                if($birthday_minus_three_days == Carbon::now()->format('Y-m-d')) {
                    $upcoming_birthdays [] = [
                        'name' => $user->name,
                        'birthday' => $user->birthday->format('d.m.Y')
                    ];
                }
            }
        }

        if(!is_null($upcoming_birthdays)){
            $text = 'День рождения через 3 дня:'. PHP_EOL;
            foreach ($upcoming_birthdays as $birthday){
                $text .= $birthday['name'].' - '.$birthday['birthday']. PHP_EOL;
            }
            $notify_group = User::whereHas('roles', function ($query) {
                $query->where('name', 'director');
            })->get()->pluck('id');

            foreach ($notify_group as $user_id){
                $notification = [
                    'from' => 'Система',
                    'to' => $user_id,
                    'text' => $text,
                    'link' => 'user/'.$user_id.'/statistic',
                    'class' => 'bg-success'
                ];

                $notification['inline_keyboard'] = [
                    'inline_keyboard' => [
                        [
                            ['text' => 'Открыть', 'url' => config('app.url').$notification['link']],
                        ],
                    ]
                ];

                $notification['action'] = 'notification';
                $notification_channel = getNotificationChannel($user_id);

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
