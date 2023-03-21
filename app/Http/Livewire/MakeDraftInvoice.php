<?php

namespace App\Http\Livewire;

use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Project;
use Livewire\Component;

class MakeDraftInvoice extends Component
{

    public $invoice;
    public $project;
    public $contracts;

    protected $listeners = [
        'set:create_draft_invoice_id' => 'getInvoice'
    ];

    public function getInvoice($id){
        $this->invoice = Invoice::find($id);
        $this->project = Project::find($this->invoice->project_id);
        $this->contracts = Contract::where('client_id', $this->invoice->client_id)->get();
    }

    public function render()
    {
        return view('livewire.make-draft-invoice');
    }
}
