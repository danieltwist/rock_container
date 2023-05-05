<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;

class AgreeInvoicesController extends Controller
{
    public function agreeInvoicesSettings(){

        return view('settings.agree_invoice',[
            'agree_invoice_users' => unserialize(Setting::where('name', 'agree_invoice_users')->first()->toArray()['value']),
            'agree_invoice_users_count' => Setting::where('name', 'agree_invoice_users_count')->first()->toArray()['value'],
            'users' => User::whereHas('roles', function ($query) {
                $query->whereNotIn('name', ['super-admin']);
            })->get()
        ]);

    }

    public function updateAgreeInvoicesSettings(Request $request){

        if(count($request->agree_invoice_users) == $request->agree_invoice_users_count){

            Setting::where('name', 'agree_invoice_users_count')->update([
                'value' => $request->agree_invoice_users_count
            ]);

            Setting::where('name', 'agree_invoice_users')->update([
                'value' => serialize($request->agree_invoice_users)
            ]);

            return redirect()->back()->withSuccess(__('settings.agree_settings_successfully_saved'));
        }
        else {

            return redirect()->back()->withError(__('settings.users_not_match'));

        }

    }
}
