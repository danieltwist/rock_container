<?php

namespace App\Console\Commands;

use App\Models\BankAccountPayment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GetBankAccountPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bank_accounts:get_payments';

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
        $updates = Storage::files('public/templates/1C/payments/');
        if(!is_null($updates)) {
            foreach ($updates as $file) {
                $xmlObject = simplexml_load_string(Storage::get($file));

                $jsonFormatData = json_encode($xmlObject);
                $result = json_decode($jsonFormatData, true);

                foreach ($result as $key => $value){
                    foreach ($value as $k => $v){
                        $bank_account_payment = new BankAccountPayment();

                        $bank_account_payment->company = $v['@attributes']['Организация'];
                        $bank_account_payment->counterparty = $v['@attributes']['Контрагент'];
                        $bank_account_payment->amount = $v['@attributes']['СуммаДокумента'];
                        $bank_account_payment->payment_type = $v['@attributes']['ВидОперации'];

                        $bank_account_payment->save();
                    }
                }
                Storage::move($file, 'public/templates/1C/payments/processed/'.basename($file));
            }
        }
    }
}
