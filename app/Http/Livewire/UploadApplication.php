<?php

namespace App\Http\Livewire;

use App\Models\Client;
use App\Models\Project;
use Livewire\Component;

class UploadApplication extends Component
{
    public $project = null;
    public $selectedClientId = null;
    public $selectedContractId = null;
    public $clients = null;
    public $contracts = null;
    public $client_object = null;

    protected $listeners = [
        'set:project_id' => 'getProject'
    ];

    public function getProject($id){
        $this->project = Project::find($id);
        foreach ($this->project->all_clients() as $id){
            $client = Client::find($id);
            if(!is_null($client)){
                $this->clients [] = $client;
            }
        }

    }

    public function updatedSelectedClientId($id){
        if($id != ''){
            $this->selectedClient = Client::find($id);
            $this->contracts = $this->selectedClient->contracts;
            if($this->selectedClient->contracts->isEmpty()) $this->contracts = null;
        }
        else $this->contracts = null;

        $this->selectedContractId = null;
    }

    public function updatedSelectedContractId($id){

        if($id != ''){
            $this->selectedContractId = $id;
        }
        else {
            $this->selectedContractId = null;
        }
    }

    public function render()
    {
        return view('livewire.upload-application');
    }
}
