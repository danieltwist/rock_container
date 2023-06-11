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
                $xmlObject = simplexml_load_string(Storage::get($file));

                $jsonFormatData = json_encode($xmlObject);
                $result = json_decode($jsonFormatData, true);

                $info = [];

                foreach ($result as $key => $value){
                    foreach ($value as $k => $v){
                        $info [] = [
                            'account_type' => $v['@attributes']['Счет'],
                            'amount' => $v['@attributes']['Сумма'],
                            'account_number' => $v['@attributes']['БанковскийСчет'],
                            'company' => $v['@attributes']['Организация'],
                        ];
                    }
                }
                if(!empty($info)){
                    $bank_account_balance = new BankAccountBalance();
                    $bank_account_balance->info = $info;
                    $bank_account_balance->save();
                }
                Storage::move($file, 'public/templates/1C/balances/processed/'.basename($file));
            }
        }
    }
}
