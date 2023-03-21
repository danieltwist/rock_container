<?php

namespace App\Http\Controllers\Datatables;

use App\Filters\ProjectFilter;
use App\Http\Controllers\Controller;
use App\Http\Traits\FinanceTrait;
use App\Http\Traits\ProjectTrait;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectTablesController extends Controller
{
    use FinanceTrait;
    use ProjectTrait;

    public function getProjectsWithFilter(ProjectFilter $filter, Request $request){

        if (in_array($request->data_range, ['','all','Все'])) {
            $range = '2000-01-01 - 3000-01-01';
        }
        else {
            $range = $request->data_range;
        }

        $range = explode(' - ', $range);

        if(in_array($request->filter, ['finished', 'paid', 'done_unpaid'])){
            $projects = Project::filter($filter)
                ->whereDate('finished_at', '>=', $range[0])
                ->whereDate('finished_at', '<=', $range[1])
                ->get();
        }
        elseif($request->filter == 'finished_paid_date'){
            $projects = Project::filter($filter)
                ->whereDate('paid_at', '>=', $range[0])
                ->whereDate('paid_at', '<=', $range[1])
                ->get();
        }
        else {
            $projects = Project::filter($filter)
                ->whereDate('created_at', '>=', $range[0])
                ->whereDate('created_at', '<=', $range[1])
                ->get();
        }

        foreach ($projects as $project) {
            $project->finance = $this->getProjectFinance($project->id);
        }

        $draw = $request->get('draw');
        $start = $request->get('start');
        $rowperpage = $request->get('length');

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column'];
        $columnName = $columnName_arr[$columnIndex]['data'];
        $columnSortOrder = $order_arr[0]['dir'];
        $searchValue = $search_arr['value'];

        $totalRecords = $projects->count();

        if($searchValue != ''){
            $withFilter = Project::query()->orWhere(function ($query) use ($searchValue, $range) {
                $query->where('projects.name', 'like', '%' . $searchValue . '%')
                    ->orWhere('projects.from', 'like', '%' . $searchValue . '%')
                    ->orWhere('projects.to', 'like', '%' . $searchValue . '%')
                    ->orWhere('projects.additional_info', 'like', '%' . $searchValue . '%')
                    ->orWhere('projects.status', 'like', '%' . $searchValue . '%')
                    ->orWhere('projects.created_at', 'like', '%' . $searchValue . '%')
                    ->orWhere('projects.finished_at', 'like', '%' . $searchValue . '%')
                    ->orWhereHas('client', function ($q) use ($searchValue) {
                        $q->where('name', 'like', '%' . $searchValue . '%');
                    })
                    ->orWhereHas('user', function ($q) use ($searchValue) {
                        $q->where('name', 'like', '%' . $searchValue . '%');
                    });
                });

                if(in_array($request->filter, ['finished', 'paid', 'done_unpaid'])){
                    $withFilter
                        ->whereDate('finished_at', '>=', $range[0])
                        ->whereDate('finished_at', '<=', $range[1]);
                }
                elseif($request->filter == 'finished_paid_date'){
                    $withFilter
                        ->whereDate('paid_at', '>=', $range[0])
                        ->whereDate('paid_at', '<=', $range[1])
                        ->get();
                }
                else {
                    $withFilter
                        ->whereDate('created_at', '>=', $range[0])
                        ->whereDate('created_at', '<=', $range[1]);
                }

                $withFilter->filter($filter);

                $totalRecordswithFilter = $withFilter->count();


            $records = $withFilter
                ->orderBy($columnName, $columnSortOrder)
                ->select('projects.*')
                ->skip($start)
                ->take($rowperpage)
                ->get();
        }
        else {
            $totalRecordswithFilter = Project::query();

            if(in_array($request->filter, ['finished', 'paid', 'done_unpaid'])){
                $totalRecordswithFilter
                    ->whereDate('finished_at', '>=', $range[0])
                    ->whereDate('finished_at', '<=', $range[1]);
            }
            elseif($request->filter == 'finished_paid_date'){
                $totalRecordswithFilter
                    ->whereDate('paid_at', '>=', $range[0])
                    ->whereDate('paid_at', '<=', $range[1])
                    ->get();
            }
            else {
                $totalRecordswithFilter
                    ->whereDate('created_at', '>=', $range[0])
                    ->whereDate('created_at', '<=', $range[1]);
            }

            $totalRecordswithFilter->filter($filter);
            $totalRecordswithFilter = $totalRecordswithFilter->count();

            $records = Project::orderBy($columnName, $columnSortOrder);

            if(in_array($request->filter, ['finished', 'paid', 'done_unpaid'])){
                $records
                    ->whereDate('finished_at', '>=', $range[0])
                    ->whereDate('finished_at', '<=', $range[1]);
            }
            elseif($request->filter == 'finished_paid_date'){
                $records
                    ->whereDate('paid_at', '>=', $range[0])
                    ->whereDate('paid_at', '<=', $range[1])
                    ->get();
            }
            else {
                $records
                    ->whereDate('created_at', '>=', $range[0])
                    ->whereDate('created_at', '<=', $range[1]);
            }

            $records = $records
                ->filter($filter)
                ->select('projects.*')
                ->skip($start)
                ->take($rowperpage)
                ->get();
        }

        $data_arr = array();

        foreach ($records as $project) {

            $project->finance = $this->getProjectFinance($project['id']);
            $project->complete_level = $this->getProjectCompleteLevel($project['id']);

            $id = $project->id;

            $name = view('project.table.name', [
                'project' => $project
            ])->render();

            $info = view('project.table.info', [
                'project' => $project
            ])->render();

            $finance = view('project.table.finance', [
                'project' => $project
            ])->render();

            $status = view('project.table.status', [
                'project' => $project
            ])->render();

            $actions = view('project.table.actions', [
                'project' => $project
            ])->render();

            $data_arr[] = array(
                "id" => $id,
                "name" => $name,
                "client_id" => $info,
                "freight_amount" => $finance,
                "status" => $status,
                "created_at" => $actions
            );
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );


        echo json_encode($response);
        exit;

    }

}
