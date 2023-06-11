<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Controller;
use App\Models\BankAccountBalance;
use App\Models\BankAccountPayment;
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
                'bank_account_balances' => $bank_account_balances
        ])->render();
    }

}
