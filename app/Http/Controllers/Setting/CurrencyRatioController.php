<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\CurrencyRate;
use App\Models\Setting;

class CurrencyRatioController extends Controller
{
    public function index(){

        $usd_ratio = Setting::where('name', 'usd_ratio')->first()->toArray()['value'];
        $cny_ratio = Setting::where('name', 'cny_ratio')->first()->toArray()['value'];

        return view('settings/currency', [
            'usd_ratio' => $usd_ratio,
            'cny_ratio' => $cny_ratio,
            'currency_rates' => CurrencyRate::orderBy('id', 'DESC')->limit(5)->get()
        ]);
    }

    public function updateRates(){

        \Artisan::call('get:rates');

        return redirect()->back()->withSuccess(__('settings.currency_rates_updated_successfully'));
    }
}
