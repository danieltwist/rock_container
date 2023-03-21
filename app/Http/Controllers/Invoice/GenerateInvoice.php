<?php

namespace App\Http\Controllers\Invoice;

use App\Events\TaskDone;
use App\Events\TelegramNotify;
use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\PhpWord;

class GenerateInvoice extends Controller
{
    public function makeInvoice(Request $request){

        if(!is_null($request->inv_num)){

            $invoice = Invoice::find($request->invoice_id);
            $folder = getFolderUploadInvoice($invoice, 'invoice');

            $filename = 'invoice_'.$request->invoice_id.'_'.Carbon::now()->format('Y-m-d').'.docx';

            if(config('app.prefix_view') == 'rl_'){
                if($request->template == 'lanta'){
                    switch ($request->currency){
                        case 'RUB':
                            $template_file = 'storage/templates/invoice_template_lanta_rub.docx';
                            break;
                        case 'CNY':
                            $template_file = 'storage/templates/invoice_template_lanta_cny.docx';
                            break;
                        case 'USD':
                            $template_file = 'storage/templates/invoice_template_lanta_usd.docx';
                            break;
                        default:
                            $template_file = 'storage/templates/invoice_template.docx';
                    }
                }
                else {
                    $template_file = 'storage/templates/invoice_template.docx';
                }
            }
            else {
                $template_file = 'storage/templates/invoice_template.docx';
            }

            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($template_file);
            $templateProcessor->setValue('inv_num', $request->inv_num);

            if($request->contract != ''){
                $contract = Contract::find($request->contract);
                $templateProcessor->setValue('cont_num', $contract->name);
                $templateProcessor->setValue('cont_date', $contract->date_start);

            }
            else {
                $templateProcessor->setValue('cont_num', $request->cont_num);
                $templateProcessor->setValue('cont_date', $request->cont_date);
            }

            $templateProcessor->setValue('today_date', $request->inv_date);
            $templateProcessor->setValue('client_company_name', $request->client_company_name);
            $templateProcessor->setValue('client_company_requisites', str_replace('&','&amp;',$request->client_company_requisites));
            $templateProcessor->setValue('invoice_services', $request->invoice_services);
            $templateProcessor->setValue('currency', $request->currency);
            $templateProcessor->setValue('price_1pc', $request->price_1pc);
            $templateProcessor->setValue('freight_amount', $request->freight_amount);
            $templateProcessor->setValue('price_in_currency', $request->price_in_currency);

            $templateProcessor->saveAs('storage/'.$filename);

            Storage::move('public/'.$filename, $folder.$filename);

            $invoice_file [] = [
                'filename' => $folder.$filename,
                'amount' => $request->price_in_currency.$request->currency,
                'date' => Carbon::now()->format('Y-m-d H:i:s'),
                'user' => Auth::user()->name
            ];

            $invoice->update([
                'invoice_file' => $invoice_file,
                'status' => 'Ожидается оплата',
            ]);

            if($invoice->client_id != ''){
                $object = $invoice->direction.' №'.$invoice->id.' от '.$invoice->created_at.' от '.$invoice->client->name;
            }
            else $object = $invoice->direction.' №'.$invoice->id.' от '.$invoice->created_at.' от '.$invoice->supplier->name;


            $new_task = new Task();

            $new_task->type = 'Система';
            $new_task->model = 'invoice';
            $new_task->model_id = $invoice->id;
            $new_task->object = $object;
            $new_task->send_to = userInfo($invoice->project->user_id)->name;
            $new_task->responsible_user = explode(',',$invoice->project->user_id);
            $new_task->to_users = array_map('intval', explode(',',$invoice->project->user_id));
            $new_task->text = 'Отправьте инвойс клиенту';
            $new_task->status = 'Ожидает выполнения';
            $new_task->active = '1';

            $new_task->save();

            if($invoice->client_id != ''){
                $text = __('console.new_task').' №' . $new_task->id . ': ' . __('console.send_invoice') .' '.$invoice->client->name;
            }
            else $text = __('console.new_task').' №' . $new_task->id . ': ' . __('console.send_invoice') .' '.$invoice->supplier->name;

            $message = [
                'bg_class' =>'bg-success',
                'to' => $invoice->project->user_id,
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

            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'message' => __('invoice.invoice_created_successfully')
            ]);

        }

        else{
            return response()->json([
                'bg-class' => 'bg-danger',
                'from' => 'Система',
                'message' => __('invoice.fill_invoice_number')
            ]);
        }

    }
}
