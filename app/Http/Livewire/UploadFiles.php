<?php

namespace App\Http\Livewire;

use App\Models\Project;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class UploadFiles extends Component
{

    public $project = null;
    public $selectedRootFolder = null;
    public $subfolders = null;
    public $need_subfolder = false;
    public $path = null;

    protected $listeners = [
        'set:project_id' => 'getProject'
    ];

    public function getProject($id){
        $this->project = Project::find($id);
    }

    public function updatedSelectedRootFolder($folder){

        $this->subfolders = [];

        if(!in_array($folder,['Реестры','','Корневая папка проекта','Заявки с поставщиками']))
            $this->need_subfolder=true;
        else
            $this->need_subfolder=false;

        if ($this->need_subfolder){
            if($this->project->active == '1'){
                $path = 'public/Проекты/Активные проекты/'.$this->project->name.'/';
            }
            else {
                $path = 'public/Проекты/Завершенные проекты/'.$this->project->name.'/';
            }

            $subfolders = Storage::directories($path.$folder.'/');

            foreach ($subfolders as $folder_list){
                $this->subfolders[] = str_replace($path.$folder.'/', '', $folder_list);
            }

            //@dd($this->subfolders);
        }

    }

    public function render()
    {
        return view('livewire.upload-files');
    }
}
