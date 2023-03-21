<?php

namespace App\Http\Controllers\Invoice;

use App\Events\TaskDone;
use App\Events\TelegramNotify;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class MakeDraftInvoice extends Controller
{
    public function addDraft (Request $request){

        $invoice = Invoice::find($request->invoice_id);

        $request->contract != '' ? $contract_id = $request->contract : $contract_id = null;

        $invoice_draft_array = [
            'client_company_name' => $request->client_company_name,
            'client_company_requisites' => $request->client_company_requisites,
            'contract_id' => $contract_id,
            'cont_num' => $request->cont_num,
            'cont_date' => $request->cont_date,
            'invoice_services' => $request->invoice_services,
            'currency' => $request->currency,
            'price_1pc' => $request->price_1pc,
            'freight_amount' => $request->freight_amount,
            'price_in_currency' => $request->price_in_currency
        ];

        $invoice->update([
            'invoice_array' => serialize($invoice_draft_array),
            'status' => 'Создан черновик инвойса',
        ]);

        if($invoice->client_id != ''){
            $object = $invoice->direction.' №'.$invoice->id.' от '.$invoice->created_at.' от '.$invoice->client->name;
        }
        else $object = $invoice->direction.' №'.$invoice->id.' от '.$invoice->created_at.' от '.$invoice->supplier->name;

        $to_users = User::role('accountant')->pluck('id')->toArray();

        $new_task = new Task();

        $new_task->type = 'Система';
        $new_task->model = 'invoice';
        $new_task->model_id = $invoice->id;
        $new_task->object = $object;
        $new_task->send_to = 'Группа Бухгалтеры';
        $new_task->responsible_user = $to_users;
        $new_task->to_users = array_map('intval', $to_users);
        $new_task->text = 'Создан черновик инвойса, создайте инвойс';
        $new_task->status = 'Ожидает выполнения';
        $new_task->active = '1';

        $new_task->save();

        if($invoice->client_id != ''){
            $text = __('console.new_task').' №' . $new_task->id . ': ' . __('console.make_invoice_for') .' '.$object;
        }
        else $text = __('console.new_task').' №' . $new_task->id . ': ' . __('console.make_invoice_for') .' '.$object;

        foreach ($to_users as $user){
            $message = [
                'bg_class' =>'bg-success',
                'to' => $user,
                'from' => 'системы',
                'object_id' => $new_task->id,
                'message' => $text
            ];

            $message['link'] = 'task/'.$new_task->id;
            $message['text'] = $message['message'];

            $message['inline_keyboard'] = [
                'inline_keyboard' => [
                    [
                        ['text' => 'Открыть', 'url' => config('app.url').$message['link']],
                    ],
                ]
            ];

            $message['action'] = 'notification';
            $message['type'] = 'task';

            $notification_channel = getNotificationChannel($message['to']);

            if($notification_channel == 'Система'){
                event(new TaskDone($message));
            }
            elseif($notification_channel == 'Telegram'){
                event(new TelegramNotify($message));
            }
            else {
                event(new TaskDone($message));
                event(new TelegramNotify($message));
            }
        }
        return response()->json([
            'bg-class' => 'bg-success',
            'from' => 'Система',
            'message' => __('invoice.draft_created_successfully')
        ]);
    }
}
