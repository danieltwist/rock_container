<?php

namespace App\Console\Commands;

use App\Models\BankAccountBalance;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GetBankAccountBalances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bank_accounts:get_balances';

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
        $updates = Storage::files('public/templates/1C/balances/');
        if(!is_null($updates)) {
            foreach ($updates as $file) {
                $result = simplexml_load_string(Storage::get($file));

                $info = [];

                foreach ($result as $value){
                    $info [] = [
                        'account_type' => (string)$value->attributes()['Счет'],
                        'amount' => (string)$value->attributes()['Сумма'],
                        'account_number' => (string)$value->attributes()['БанковскийСчет'],
                        'company' => (string)$value->attributes()['Организация'],
                    ];
                }
                if(!empty($info)){
                    $bank_account_balance = new BankAccountBalance();
                    $bank_account_balance->info = $info;
                    $bank_account_balance->save();
                }
                Storage::move($file, 'public/templates/1C/balances/processed/'.$this->generateRandomString().'_'.basename($file));
            }
        }
    }

    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
