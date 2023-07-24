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
                $result = simplexml_load_string(Storage::get($file));

                foreach ($result as $value){
                    $bank_account_payment = new BankAccountPayment();

                    $bank_account_payment->company = (string)$value->attributes()['Организация'];
                    $bank_account_payment->counterparty = (string)$value->attributes()['Контрагент'];
                    $bank_account_payment->amount = (string)$value->attributes()['СуммаДокумента'];
                    $bank_account_payment->payment_type = (string)$value->attributes()['ВидОперации'];

                    $bank_account_payment->save();
                }
                Storage::move($file, 'public/templates/1C/payments/processed/'.$this->generateRandomString().'_'.basename($file));
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
