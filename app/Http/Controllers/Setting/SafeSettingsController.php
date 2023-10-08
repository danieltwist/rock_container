<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Setting;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SafeSettingsController extends Controller
{
    public function index(){

        $settings = Setting::where('name', 'safe')->first()->value;
        if(!is_null($settings)){
            $settings = unserialize($settings);
        }

        return view('settings/safe', [
            'client_id' => $settings['client_id'],
            'supplier_id' => $settings['supplier_id'],
            'balance_date' => $settings['balance_date'],
            'balance' => $settings['balance'],
            'clients' => Client::all(),
            'suppliers' => Supplier::all()
        ]);
    }

    public function updateSafeSettings(Request $request){

        $settings = [
            'client_id' => $request->client_id,
            'supplier_id' => $request->supplier_id,
            'balance_date' => \Carbon\Carbon::parse($request->balance_date)->format('Y-m-d'),
            'balance' => $request->balance,
        ];

        Setting::where('name', 'safe')->update([
            'value' => serialize($settings)
        ]);

        return redirect()->back()->withSuccess('Настройки сейфа были успешно сохранены');
    }
}
