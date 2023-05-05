<?php

namespace App\Http\Traits;
use App\Models\Block;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

trait ProjectTrait {

    public function getProjectCompleteLevel($id)
    {
        $project = Project::where('id', $id)->withTrashed()->first();
        $blocks = Block::where('project_id', $project['id'])->get();

        $count = $blocks->count();

        $i = 0;
        foreach ($blocks as $block){
            if($block->status=='Завершен') $i++;
        }

        if ($count == 0) $complete = 0;

        else {
            $complete = round(($i / $count)*100);
        }

        return $complete;

    }

    public function getApplications($id){

        $project = Project::where('id', $id)->withTrashed()->first();

        $applications = $project->applications;

        $client_applications = [];

        foreach ($applications as $application){
            if($application->client_id != ''){
                $client_applications [] = $application;
            }
        }

        $applications = collect($client_applications);

        return $applications;
    }

    public function getFiles($id){

        $project = Project::where('id', $id)->withTrashed()->first();
        $files = [];

        if($project->active == '1'){
            $path = 'public/Проекты/Активные проекты/'.$project["name"].'/';
        }
        else {
            $path = 'public/Проекты/Завершенные проекты/'.$project["name"].'/';
        }

        if (!empty(Storage::Files($path))){
            $files['Корневая папка проекта'][] = array(
                'folder'=> '',
                'files'=> Storage::Files($path)
            );
        }

        $root_directories = Storage::directories($path);

        foreach ($root_directories as $root_folder){
            if (!empty(Storage::Files($root_folder))){
                $files[str_replace($path,'',$root_folder)][] = array(
                    'folder'=> '',
                    'files'=> Storage::Files($root_folder)
                );
            }
            foreach (Storage::allDirectories($root_folder) as $folder){

                if (!empty(Storage::Files($folder))){
                    $files[str_replace($path,'',$root_folder)][] = array(
                        'folder'=> str_replace($root_folder.'/','',$folder),
                        'files'=> Storage::Files($folder)
                    );
                }
            }

        }

        return $files;
    }

}
