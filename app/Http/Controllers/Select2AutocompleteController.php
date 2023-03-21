<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class Select2AutocompleteController extends Controller
{
    public function dataAjax(Request $request)
    {
        $data = [];

        if($request->page == 'task_invoice'){
            if($request->has('q')){
                $search = $request->q;
                $data = Invoice::where('id','LIKE',"%$search%")
                    ->orWhere('id','LIKE',"%$search%")
                    ->orWhereHas('client', function ($q) use($search)
                    {
                        $q->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('supplier', function ($q) use($search)
                    {
                        $q->where('name', 'like', '%' . $search . '%');
                    })
                    ->orderBy('id','DESC')
                    ->get();

                foreach ($data as $invoice){
                    $text = $invoice->direction. ' №'. $invoice->id .' от '. $invoice->created_at .' для ';
                    if (!is_null($invoice->supplier_id)) $text .= optional($invoice->supplier)->name;
                    elseif (!is_null($invoice->client_id)) $text .= optional($invoice->client)->name;

                    $data [] = [
                        'id' => $invoice->id,
                        'text' => $text
                    ];
                }
            }
            return response()->json($data);
        }

    }


}
