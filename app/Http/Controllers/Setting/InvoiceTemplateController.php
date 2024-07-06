<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateInvoiceTemplateRequest;
use App\Models\InvoiceTemplate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class InvoiceTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        return view('settings.invoice_template')->with([
                'invoice_templates' => InvoiceTemplate::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        if ($request->hasFile('invoice_template')) {

            $folder = 'public/templates/invoice_templates';

            $file = renameBeforeUpload($request->invoice_template->getClientOriginalName());

            $filename = $request->invoice_template->storeAs($folder, 'invoice-template-'.Carbon::now()->format('Y-m-d-H-i-s').'_'.$file);

            $invoice_template = new InvoiceTemplate([
                'name' => $request->name,
                'file' => $filename,
                'info' => $request->additional_info
            ]);

            $invoice_template->save();

            return redirect()->back()->withSuccess('Шаблон бьл успешно добавлен');

        } else {
            return redirect()->back()->withError('Вы не выбрали файл');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\InvoiceTemplate  $invoiceTemplate
     * @return \Illuminate\Http\Response
     */
    public function show(InvoiceTemplate $invoiceTemplate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\InvoiceTemplate  $invoiceTemplate
     * @return \Illuminate\Http\Response
     */
    public function edit(InvoiceTemplate $invoiceTemplate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateInvoiceTemplateRequest  $request
     * @param  \App\Models\InvoiceTemplate  $invoiceTemplate
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateInvoiceTemplateRequest $request, InvoiceTemplate $invoiceTemplate)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\InvoiceTemplate  $invoiceTemplate
     * @return \Illuminate\Http\Response
     */
    public function destroy(InvoiceTemplate $invoiceTemplate)
    {
        Storage::delete($invoiceTemplate->file);

        $invoiceTemplate->delete();

        return redirect()->back()->withSuccess('Шаблон был успешно удален');
    }
}
