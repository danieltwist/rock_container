<?php

namespace App\Http\Livewire;

use App\Models\Container;
use App\Models\ContainerGroup;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\User;
use App\Models\WorkRequest;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class CreateWorkRequest extends Component
{
    public $projects = NULL;
    public $invoices = NULL;
    public $upd = NULL;
    public $upd_project_task = NULL;
    public $invoices_project_task = NULL;
    public $project_id;
    public $containers = NULL;
    public $container_groups = NULL;
    public $users;
    public $user_roles;
    public $text;
    public $send_to;
    public $selected_user = NULL;
    public $work_request_id = NULL;
    public $selectedModel = NULL;
    public $work_request = NULL;
    public $model = NULL;

    protected $listeners = [
        'set:model' => 'newWorkRequest',
        'set:work_request_id' => 'editWorkRequest'
    ];

    public function mount()
    {
        $this->users = User::whereHas('roles', function ($query) {
            $query->whereNotIn('name', ['super-admin']);
        })->get();;

        $roles = Role::get()->whereNotIn('name', ['super-admin','user','special']);

        foreach ($roles as $role){
            if(!empty(User::role($role)->pluck('id')->toArray())){
                if (in_array($role['ru_name'], ['Менеджер', 'Логист', 'Бухгалтер', 'Директор'])){
                    $role_users ['Группа '. $role['ru_name'].'ы'] = implode(',', User::role($role)->pluck('id')->toArray());
                }
                else {
                    $role_users ['Группа '. $role['ru_name']] = implode(',', User::role($role)->pluck('id')->toArray());
                }
            }

        }

        $this->user_roles = $role_users;

        if(!is_null($this->work_request_id)){
            $this->work_request = WorkRequest::find($this->work_request_id);

            if ($this->work_request->model == 'project'){
                $this->projects = Project::all();
                $this->invoices = null;
                $this->containers = null;
                $this->container_groups = null;
                $this->selectedModel = 'project';
            }
            if ($this->work_request->model == 'invoice'){
                $this->invoices = Invoice::where('id', $this->work_request->model_id)->get();;
                $this->projects = null;
                $this->containers = null;
                $this->container_groups = null;
                $this->selectedModel = 'invoice';
            }
            if ($this->work_request->model == 'container'){
                $this->containers = Container::all();
                $this->invoices = null;
                $this->projects = null;
                $this->container_groups = null;
                $this->selectedModel = 'container';
            }
            if ($this->work_request->model == 'container_group'){
                $this->container_groups = ContainerGroup::all();
                $this->invoices = null;
                $this->projects = null;
                $this->containers = null;
                $this->selectedModel = 'container_group';
            }
            if ($this->work_request->model == 'upd'){
                $this->upd = [];
                $this->projects = null;
                $this->containers = null;
                $this->container_groups = null;
                $this->invoices = null;
                $this->selectedModel = 'upd';
            }
            if ($this->work_request->model == 'free'){
                $this->upd = null;
                $this->projects = null;
                $this->containers = null;
                $this->container_groups = null;
                $this->invoices = null;
                $this->selectedModel = 'free';
            }
        }

        else {
            $this->projects = null;
            $this->invoices = null;
            $this->containers = null;
            $this->container_groups = null;

        }

    }

    public function render()
    {
        return view('livewire.create-work-request');
    }

    public function updatedSelectedModel($model)
    {
        if ($model == 'project'){
            $this->projects = Project::all();
            $this->invoices = null;
            $this->containers = null;
            $this->invoices_project_task = null;
            $this->upd = null;
            $this->upd_project_task = null;
        }
        if ($model == 'invoice'){
            $this->invoices = [];
            $this->projects = null;
            $this->containers = null;
            $this->invoices_project_task = null;
            $this->upd = null;
            $this->upd_project_task = null;
        }
        if ($model == 'container'){
            $this->containers = Container::all();
            $this->invoices = null;
            $this->projects = null;
            $this->invoices_project_task = null;
            $this->upd = null;
            $this->upd_project_task = null;
        }
        if ($model == 'container_group'){
            $this->container_groups = ContainerGroup::all();
            $this->invoices = null;
            $this->projects = null;
            $this->containers = null;
            $this->upd = null;
            $this->upd_project_task = null;
            $this->selectedModel = 'container_group';
        }
        if($model == 'upd'){
            $this->upd = [];
            $this->projects = null;
            $this->containers = null;
            $this->invoices = null;
            $this->invoices_project_task = null;
            $this->upd_project_task = null;
        }
        if($model == 'free'){
            $this->upd = null;
            $this->projects = null;
            $this->containers = null;
            $this->invoices = null;
            $this->invoices_project_task = null;
            $this->upd_project_task = null;
        }
    }

    public function newWorkRequest($model, $model_id, $text, $selected_user, $send_to){

        $this->selectedModel = $model;
        $this->send_to = $send_to;
        $this->updatedSelectedModel($model);

        $this->model_id_dynamic = $model_id;
        $this->text = $text;
        $this->selected_user = $selected_user;

        if ($model == 'invoice'){
            $this->invoices_project_task = Invoice::where('id', $model_id)->get();
            $this->projects = null;
            $this->container_groups = null;
            $this->upd = null;
            $this->upd_project_task = null;
            $this->invoices = null;
            $this->containers = null;
            $this->project_id = $model_id;
        }

        if($model == 'upd'){
            $this->invoices_project_task = null;
            $this->projects = null;
            $this->containers = null;
            $this->container_groups = null;
            $this->upd = null;
            $this->upd_project_task = Invoice::where('project_id', $model_id)->get();
            $this->invoices = null;
            $this->project_id = $model_id;
        }

    }


}
