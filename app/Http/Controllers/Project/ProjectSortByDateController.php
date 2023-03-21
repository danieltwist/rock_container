<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Http\Traits\FinanceTrait;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectSortByDateController extends Controller
{
    use FinanceTrait;
    public function sortByDate(Request $request)
    {
        if($request->data_range !='Все'){
            $range = explode(' - ', $request->data_range);
        }
        else $range = explode(' - ', '2000-01-01 - 3000-01-01');
        $projects = [];
        switch ($request->type) {
            case 'Завершенные проекты':
                $projects = Project::whereDate('finished_at', '>=', $range[0])
                    ->whereDate('finished_at', '<=', $range[1])
                    ->where('active', '0')
                    ->where('status', '<>', 'Черновик')
                    ->where('paid', 'Оплачен')
                    ->orderBy('finished_at','desc')
                    ->get();
                break;
            case 'Все проекты':
                $projects = Project::whereDate('created_at', '>=', $range[0])
                    ->whereDate('created_at', '<=', $range[1])
                    ->orderBy('created_at','desc')
                    ->get();
                break;
            case 'Проекты в работе':
                $projects = Project::whereDate('created_at', '>=', $range[0])
                    ->whereDate('created_at', '<=', $range[1])
                    ->where('active', '1')
                    ->orderBy('created_at','desc')
                    ->get();
                break;
            case 'Черновики проектов':
                $projects = Project::whereDate('created_at', '>=', $range[0])
                    ->whereDate('created_at', '<=', $range[1])
                    ->where('status', 'Черновик')
                    ->orderBy('created_at','desc')
                    ->get();
                break;
            case 'Завершенные и неоплаченные проекты':
                $projects = Project::whereDate('finished_at', '>=', $range[0])
                    ->whereDate('finished_at', '<=', $range[1])
                    ->where('status', 'Завершен')
                    ->where('paid', 'Не оплачен')
                    ->orderBy('created_at','desc')
                    ->get();
                break;

        }

        foreach ($projects as $project){
            $project->finance = $this->getProjectFinance($project->id);
        }

        return view('project.layouts.projects_table_full_render',[
            'projects' => $projects
        ]);

    }
}
