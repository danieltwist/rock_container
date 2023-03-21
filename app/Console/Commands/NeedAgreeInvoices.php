<?php

namespace App\Console\Commands;

use App\Events\NotificationReceived;
use App\Events\TelegramNotify;
use App\Models\Invoice;
use App\Models\Notification;
use Illuminate\Console\Command;

class NeedAgreeInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:agree_invoices';

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
        $invoices = Invoice::where('status', 'Счет на согласовании')->get();

        $agree_1 = false;
        $agree_2 = false;

        foreach ($invoices as $invoice){

            if($invoice->agree_1 == ''){
                $agree_1 = true;
            }

            if($invoice->agree_2 == ''){
                $agree_2 = true;
            }

        }

        if($agree_1){
            $notification = [
                'from' => 'Система',
                'to' => 1,
                'text' => __('console.need_agree_invoices'),
                'link' => 'invoice?for_approval_urgent',
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

        if($agree_2){
            $notification = [
                'from' => 'Система',
                'to' => 21,
                'text' => 'Есть срочные счета, которые необходимо согласовать на оплату',
                'link' => 'invoice?for_approval_urgent',
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
