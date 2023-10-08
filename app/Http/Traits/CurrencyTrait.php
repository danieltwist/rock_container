<?php

namespace App\Http\Traits;

use App\Models\CurrencyRate;

trait CurrencyTrait {

    public function getRates(){
        return CurrencyRate::orderBy('created_at', 'desc')->first();
    }

    public function getPriceInRub($amount, $currency){
        $currency_rates = $this->getRates();

        if($currency != 'RUB'){
            $amount_in_rubles = $amount*$currency_rates[strtolower($currency).'_divided'];
        }
        else {
            $amount_in_rubles = $amount;
        }

        return round($amount_in_rubles, 2);
    }

    public function getCurrencyRate($currency){
        $currency_rates = $this->getRates();

        if($currency != 'RUB'){
            $rate = $currency_rates[strtolower($currency).'_divided'];
        }
        else {
            $rate = 1;
        }

        return $rate;
    }
}
