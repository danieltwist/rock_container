<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Controller;
use App\Models\BankAccountBalance;
use App\Models\BankAccountPayment;
use App\Models\Invoice;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BankAccountsController extends Controller
{
    public function getBankAccountsBalance(){
        $bank_account_balances = BankAccountBalance::latest()->first();
        if(!is_null($bank_account_balances)){
            $companies = [];
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
            }

            $bank_account_balances->companies = $companies;
        }

        return view('1c.bank_account_balances_window', [
            'bank_account_balances' => $bank_account_balances,
            'safe_balance' => $this->getSafeBalance()
        ])->render();
    }

    public function getSafeBalance(){

        $safe_balance = null;

        $settings = Setting::where('name', 'safe')->first()->value;
        if(!is_null($settings)){
            $settings = unserialize($settings);
            $ingoing_invoices = Invoice::where('client_id', $settings['client_id'])
                ->whereIn('status', ['Оплачен', 'Частично оплачен'])
                ->where('created_at', '>=', $settings['balance_date'])
                ->sum('amount_income_date');
            $outgoing_invoices = Invoice::where('supplier_id', $settings['supplier_id'])
                ->whereIn('status', ['Оплачен', 'Частично оплачен'])
                ->where('created_at', '>=', $settings['balance_date'])
                ->sum('amount_income_date');
            $safe_balance = $settings['balance'] + $ingoing_invoices - $outgoing_invoices;
        }

        return $safe_balance;
    }

}
