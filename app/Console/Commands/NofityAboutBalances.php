<?php

namespace App\Console\Commands;

use App\Events\NotificationReceived;
use App\Events\TelegramNotify;
use App\Models\BankAccountBalance;
use App\Models\User;
use Illuminate\Console\Command;

class NofityAboutBalances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:bank_accounts_balances';

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
        $bank_account_balances = BankAccountBalance::latest()->first();

        if(!is_null($bank_account_balances)){
            $companies = [];
            foreach ($bank_account_balances['info'] as $value){
                $companies [] = [
                    'name' => $value['company'],
                    'amount' => 0
                ];
            }
            $companies = array_unique($companies, SORT_REGULAR);

            foreach ($companies as $key => $company){
                foreach ($bank_account_balances['info'] as $value){
                    if($value['company'] == $company['name']){
                        $companies[$key]['amount'] += $value['amount'];
                    }
                }
            }
            $text = 'Состояние расчетных счетов: '. PHP_EOL;

            foreach ($companies as $company){
                $text .= $company['name'].': '.$company['amount']. PHP_EOL;
            }

            $notify_group = User::whereHas('roles', function ($query) {
                $query->where('name', 'director');
            })->get()->pluck('id');

            foreach ($notify_group as $user_id){
                $notification = [
                    'from' => 'Система',
                    'to' => $user_id,
                    'text' => $text,
                    'link' => '',
                    'class' => 'bg-success'
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
