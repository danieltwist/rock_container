<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Controller;
use App\Http\Traits\FinanceTrait;
use App\Models\Invoice;
use Illuminate\Http\Request;

class SellInvoiceCurrency extends Controller
{
    use FinanceTrait;
    public function sellCurrency(Request $request)
    {
        $invoice = Invoice::find($request->invoice_id);

        $invoice->update([
            'amount_sale_date' => $request->amount_sale_date,
            'rate_sale_date' => $request->rate_sale_date
        ]);

        $this->updateProjectFinance($invoice->project_id);

        return redirect()->back()->withSuccess(__('invoice.sell_currency_successfully'));
    }
}
