<?php

namespace App\Http\Livewire;

use App\Models\Contract;
use App\Models\Invoice;
use App\Models\InvoiceTemplate;
use App\Models\Project;
use Livewire\Component;

class MakeInvoice extends Component
{

    public $invoice;
    public $project;
    public $contracts;
    public $client_company_name;
    public $client_company_requisites;
    public $contract_id;
    public $cont_num;
    public $cont_date;
    public $invoice_services;
    public $currency;
    public $price_1pc;
    public $freight_amount;
    public $price_in_currency;
    public $invoice_templates;

    protected $listeners = [
        'set:create_invoice_id' => 'getInvoice'
    ];

    public function getInvoice($id){
        $this->invoice = Invoice::find($id);
        $this->project = Project::find($this->invoice->project_id);
        $this->contracts = Contract::where('client_id', $this->invoice->client_id)->get();
        $this->invoice_templates = InvoiceTemplate::all();

        $invoice_array = unserialize($this->invoice->invoice_array);

        $this->client_company_name = $invoice_array['client_company_name'];
        $this->client_company_requisites = $invoice_array['client_company_requisites'];
        $this->contract_id = $invoice_array['contract_id'];
        $this->cont_num = $invoice_array['cont_num'];
        $this->cont_date = $invoice_array['cont_date'];
        $this->invoice_services = $invoice_array['invoice_services'];
        $this->currency = $invoice_array['currency'];
        $this->price_1pc = $invoice_array['price_1pc'];
        $this->freight_amount = $invoice_array['freight_amount'];
        $this->price_in_currency = $invoice_array['price_in_currency'];
    }

    public function render()
    {
        return view('livewire.make-invoice');
    }
}
