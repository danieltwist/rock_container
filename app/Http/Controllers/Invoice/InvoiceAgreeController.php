<?php

namespace App\Http\Controllers\Invoice;

use App\Events\NotificationReceived;
use App\Events\TelegramNotify;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InvoiceAgreeController extends Controller
{
    protected $agree_invoice_users;
    protected $agree_invoice_users_count;

    public function __construct(){
        $this->agree_invoice_users = unserialize(Setting::where('name', 'agree_invoice_users')->first()->toArray()['value']);
        $this->agree_invoice_users_count = Setting::where('name', 'agree_invoice_users_count')->first()->toArray()['value'];
    }

    public function AgreeInvoice(Request $request){

        $invoice = Invoice::find($request->invoice_id);

        if($this->agree_invoice_users_count == '1'){
            if(in_array(auth()->user()->id, $this->agree_invoice_users)){

                $request->sub_status == 'Без дополнительного статуса' ? $sub_status = null : $sub_status = $request->sub_status;

                if($request->status != 'Счет на согласовании'){
                    $invoice->agree_1 = serialize([
                        'status' => $request->status,
                        'date' => Carbon::now(),
                        'user_id' => auth()->user()->id
                    ]);
                    $invoice->sub_status = $sub_status;
                    $invoice->status = $request->status;
                    $invoice->director_comment = $request->director_comment;
                    $invoice->agreement_date = Carbon::now();

                    if($request->sub_status == 'Срочно') {
                        $this->notifyAccountant($invoice);
                    }
                }
                else {
                    $invoice->status = 'Счет на согласовании';
                    $invoice->agree_1 = null;
                    $invoice->sub_status = $sub_status;
                    $invoice->director_comment = $request->director_comment;
                    $invoice->agreement_date = null;
                }

                $invoice->save();
                return response()->json([
                    'bg-class' => 'bg-success',
                    'from' => 'Система',
                    'message' => __('invoice.invoice_status_updated_successfully')
                ]);
            }

            else{
                return response()->json([
                    'bg-class' => 'bg-danger',
                    'from' => 'Система',
                    'message' => __('invoice.you_are_not_in_the_list')
                ]);

            }
        }

        if($this->agree_invoice_users_count == '2'){
            if(in_array(auth()->user()->id, $this->agree_invoice_users)){

                $agreed_by = auth()->user()->name;

                $request->sub_status == 'Без дополнительного статуса' ? $sub_status = null : $sub_status = $request->sub_status;

                if(auth()->user()->id == $this->agree_invoice_users[0]){
                    if($request->status != 'Счет на согласовании'){
                        $invoice->agree_1 = serialize([
                            'status' => $request->status,
                            'date' => Carbon::now(),
                            'user_id' => auth()->user()->id
                        ]);
                        $invoice->sub_status = $sub_status;
                        $invoice->director_comment = $request->director_comment;

                        if(is_null($invoice->agree_2)){
                            $this->notifySecondPerson($invoice, $this->agree_invoice_users[1], $agreed_by);
                            if($request->sub_status == 'Срочно') {
                                $this->notifyAccountant($invoice);
                            }
                        }
                    }
                    else {
                        $invoice->status = 'Счет на согласовании';
                        $invoice->agree_1 = null;
                        $invoice->sub_status = $sub_status;
                        $invoice->director_comment = $request->director_comment;
                        $invoice->agreement_date = null;
                    }
                }

                if(auth()->user()->id == $this->agree_invoice_users[1]){
                    if($request->status != 'Счет на согласовании'){
                        $invoice->agree_2 = serialize([
                            'status' => $request->status,
                            'date' => Carbon::now(),
                            'user_id' => auth()->user()->id
                        ]);
                        $invoice->sub_status = $sub_status;
                        $invoice->director_comment = $request->director_comment;

                        if(is_null($invoice->agree_1)){
                            $this->notifySecondPerson($invoice, $this->agree_invoice_users[0], $agreed_by);
                            if($request->sub_status == 'Срочно') {
                                $this->notifyAccountant($invoice);
                            }
                        }
                    }
                    else {
                        $invoice->status = 'Счет на согласовании';
                        $invoice->agree_2 = null;
                        $invoice->sub_status = $sub_status;
                        $invoice->director_comment = $request->director_comment;
                        $invoice->agreement_date = null;
                    }
                }

                if ($invoice->agree_1 != '' && $invoice->agree_2 !=''){
                    $invoice->status = 'Счет согласован на оплату';
                    $invoice->agreement_date = Carbon::now();
                }

                $invoice->save();

                return response()->json([
                    'bg-class' => 'bg-success',
                    'from' => 'Система',
                    'message' => __('invoice.invoice_status_updated_successfully')
                ]);
            }

            else{
                return response()->json([
                    'bg-class' => 'bg-danger',
                    'from' => 'Система',
                    'message' => __('invoice.you_are_not_in_the_list')
                ]);

            }
        }

    }

    public function notifyAccountant(Invoice $invoice){

        $accountant_group = User::whereHas('roles', function ($query) {
            $query->where('name', 'accountant');
        })->get()->pluck('id');


        foreach ($accountant_group as $user_id){
            $notification = [
                'from' => 'Система',
                'to' => $user_id,
                'text' => 'Счет №'.$invoice->id.' был согласован на оплату, оплатите',
                'link' => 'invoice/'.$invoice->id,
                'class' => 'bg-success'
            ];

            $notification['inline_keyboard'] = [
                'inline_keyboard' => [
                    [
                        ['text' => 'Открыть', 'url' => config('app.url').$notification['link']],
                    ],
                ]
            ];

            $notification['action'] = 'notification';

            $notification_channel = getNotificationChannel($user_id);

            if($notification_channel == 'Система'){
                event(new NotificationReceived($notification));
            }
            elseif($notification_channel == 'Telegram'){
                event(new TelegramNotify($notification));
            }
            else {
                event(new NotificationReceived($notification));
                event(new TelegramNotify($notification));
            }
        }
    }

    public function notifySecondPerson(Invoice $invoice, $user_id, $agreed_by){

        $notification = [
            'from' => 'Система',
            'to' => $user_id,
            'text' => 'Счет №'.$invoice->id.' был согласован на оплату пользователем '.$agreed_by.', согласуйте со своей стороны',
            'link' => 'invoice/'.$invoice->id,
            'class' => 'bg-success'
        ];

        $notification['inline_keyboard'] = [
            'inline_keyboard' => [
                [
                    ['text' => 'Открыть', 'url' => config('app.url').$notification['link']],
                ],
            ]
        ];

        $notification['action'] = 'notification';

        $notification_channel = getNotificationChannel($user_id);

        if($notification_channel == 'Система'){
            event(new NotificationReceived($notification));
        }
        elseif($notification_channel == 'Telegram'){
            event(new TelegramNotify($notification));
        }
        else {
            event(new NotificationReceived($notification));
            event(new TelegramNotify($notification));
        }

    }

}
