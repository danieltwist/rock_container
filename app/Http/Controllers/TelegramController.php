<?php

namespace App\Http\Controllers;

use App\Models\TelegramUpdate;
use Illuminate\Http\Request;

class TelegramController extends Controller
{

    public function showUpdates(){
        $updates = TelegramUpdate::all();

        foreach ($updates as $update) {
            if(!is_null($update->object) && isset($update->object['message'])){
                if($update->object['message']['text'] == '/start' && isset($update->object['message']['from']['username'])){
                    echo '<pre>';
                    print_r($update->object);
                    echo '</pre>';
                }

            }
        }

    }

    public function linkAccount(Request $request)
    {

        $user = \Auth::user();
        $user->update([
            'telegram_login' => $request->telegram_login
        ]);

        $linked = false;

        $updates = TelegramUpdate::all();

        foreach ($updates as $update) {
            if(!is_null($update->object) && isset($update->object['message'])){
                if($update->object['message']['text'] == '/start' && isset($update->object['message']['from']['username'])){
                    if ($update->object['message']['from']['username'] == auth()->user()->telegram_login) {
                        $user->update([
                            'telegram_chat_id' => $update->object['message']['chat']['id']
                        ]);
                        $linked = true;
                        break;
                    }
                }
            }
        }

        if ($linked)
            return redirect()->back()->withSuccess(__('user.telegram_linked_successfully'));
        else
            return redirect()->back()->withError(__('user.telegram_check_username_or_start_bot'));
    }

    public function unlinkAccount()
    {

        $user = \Auth::user();
        $user->update([
            'telegram_chat_id' => null
        ]);

        if ($user->notification_channel == 'Telegram') {
            $user->update([
                'notification_channel' => 'Система'
            ]);
        }

        return redirect()->back()->withSuccess(__('user.telegram_unlinked_successfully'));

    }

}
