<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InvoiceAgreeController extends Controller
{
    public function AgreeInvoice_rl_(Request $request){

        $invoice = Invoice::find($request->invoice_id);

        if(in_array(auth()->user()->id, ['1','21'])){

            $request->sub_status == 'Без дополнительного статуса' ? $sub_status = null : $sub_status = $request->sub_status;

            if(auth()->user()->id == '1'){
                if($request->status != 'Счет на согласовании'){
                    $invoice->agree_1 = serialize([
                        'status' => $request->status,
                        'date' => Carbon::now()
                    ]);
                    $invoice->sub_status = $sub_status;
                    $invoice->director_comment = $request->director_comment;
                }

                else {
                    $invoice->status = 'Счет на согласовании';
                    $invoice->agree_1 = null;
                    $invoice->sub_status = $sub_status;
                    $invoice->director_comment = $request->director_comment;
                }
            }

            if(auth()->user()->id == '21'){

                if($request->status != 'Счет на согласовании'){
                    $invoice->agree_2 = serialize([
                        'status' => $request->status,
                        'date' => Carbon::now()
                    ]);
                    $invoice->sub_status = $sub_status;
                    $invoice->director_comment = $request->director_comment;
                }

                else {
                    $invoice->status = 'Счет на согласовании';
                    $invoice->agree_2 = null;
                    $invoice->sub_status = $sub_status;
                    $invoice->director_comment = $request->director_comment;
                }

            }

            if ($invoice->agree_1 != '' && $invoice->agree_2 !=''){
                $invoice->status = 'Счет согласован на оплату';
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

    public function AgreeInvoice_ntc_(Request $request){

        $invoice = Invoice::find($request->invoice_id);

        if(in_array(auth()->user()->id, ['1','8'])){

            $request->sub_status == 'Без дополнительного статуса' ? $sub_status = null : $sub_status = $request->sub_status;

            if(auth()->user()->id == '1'){
                if($request->status != 'Счет на согласовании'){
                    $invoice->agree_1 = serialize([
                        'status' => $request->status,
                        'date' => Carbon::now()
                    ]);
                    $invoice->sub_status = $sub_status;
                    $invoice->director_comment = $request->director_comment;
                }

                else {
                    $invoice->status = 'Счет на согласовании';
                    $invoice->agree_1 = null;
                    $invoice->sub_status = $sub_status;
                    $invoice->director_comment = $request->director_comment;
                }
            }

            if(auth()->user()->id == '8'){

                if($request->status != 'Счет на согласовании'){
                    $invoice->agree_2 = serialize([
                        'status' => $request->status,
                        'date' => Carbon::now()
                    ]);
                    $invoice->sub_status = $sub_status;
                    $invoice->director_comment = $request->director_comment;
                }

                else {
                    $invoice->status = 'Счет на согласовании';
                    $invoice->agree_2 = null;
                    $invoice->sub_status = $sub_status;
                    $invoice->director_comment = $request->director_comment;
                }

            }

            if ($invoice->agree_1 != '' && $invoice->agree_2 !=''){
                $invoice->status = 'Счет согласован на оплату';
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

    public function AgreeInvoice_rc_(Request $request){

        $invoice = Invoice::find($request->invoice_id);

        if(in_array(auth()->user()->id, ['31','9'])){

            $request->sub_status == 'Без дополнительного статуса' ? $sub_status = null : $sub_status = $request->sub_status;

            if($request->status != 'Счет на согласовании'){
                $invoice->agree_1 = serialize([
                    'status' => $request->status,
                    'date' => Carbon::now()
                ]);
                $invoice->sub_status = $sub_status;
                $invoice->status = $request->status;
                $invoice->director_comment = $request->director_comment;
            }
            else {
                $invoice->status = 'Счет на согласовании';
                $invoice->agree_1 = null;
                $invoice->sub_status = $sub_status;
                $invoice->director_comment = $request->director_comment;
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

    public function AgreeInvoice_blc_(Request $request){

        $invoice = Invoice::find($request->invoice_id);

        if(in_array(auth()->user()->id, ['3','9'])){

            $request->sub_status == 'Без дополнительного статуса' ? $sub_status = null : $sub_status = $request->sub_status;

            if($request->status != 'Счет на согласовании'){
                $invoice->agree_1 = serialize([
                    'status' => $request->status,
                    'date' => Carbon::now()
                ]);
                $invoice->sub_status = $sub_status;
                $invoice->status = $request->status;
                $invoice->director_comment = $request->director_comment;
            }
            else {
                $invoice->status = 'Счет на согласовании';
                $invoice->agree_1 = null;
                $invoice->sub_status = $sub_status;
                $invoice->director_comment = $request->director_comment;
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
