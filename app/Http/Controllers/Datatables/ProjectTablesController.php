<?php

namespace App\Http\Controllers\Datatables;

use App\Filters\ProjectFilter;
use App\Http\Controllers\Controller;
use App\Http\Traits\FinanceTrait;
use App\Http\Traits\ProjectTrait;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            $range = explode(' - ', $range);
            $range_from = \Carbon\Carbon::parse($range[0])->format('Y-m-d');
            $range_to = \Carbon\Carbon::parse($range[1])->format('Y-m-d');

            $range = $range_from.' - '.$range_to;
        }

        $range = explode(' - ', $range);

        if(in_array($request->filter, ['finished', 'paid', 'done_unpaid'])){
            $projects = Project::filter($filter)
                ->whereDate('finished_at', '>=', $range[0])
                ->whereDate('finished_at', '<=', $range[1]);
        }
        elseif($request->filter == 'finished_paid_date'){
            $projects = Project::filter($filter)
                ->whereDate('paid_at', '>=', $range[0])
                ->whereDate('paid_at', '<=', $range[1]);
        }
        else {
            $projects = Project::filter($filter)
                ->whereDate('created_at', '>=', $range[0])
                ->whereDate('created_at', '<=', $range[1]);
        }

        if(!auth()->user()->can('view and access all projects') && !in_array(auth()->user()->getRoleNames()[0], ['director', 'accountant', 'super-admin'])){
            $projects->where(function ($query) {
                $query->where('user_id', auth()->user()->id)
                    ->orWhere('manager_id',  auth()->user()->id)
                    ->orWhere('logist_id',  auth()->user()->id)
                    ->orWhereNotNull('management_expenses')
                    ->orWhereJsonContains('access_to_project', auth()->user()->id);
            });
        }

        $projects = $projects->get();

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
                    ->whereDate('paid_at', '<=', $range[1]);
            }
            else {
                $withFilter
                    ->whereDate('created_at', '>=', $range[0])
                    ->whereDate('created_at', '<=', $range[1]);
            }

            $withFilter->filter($filter);

            if(!auth()->user()->can('view and access all projects') && !in_array(auth()->user()->getRoleNames()[0], ['director', 'accountant', 'super-admin'])){
                $withFilter->where(function ($query) {
                    $query->where('user_id', auth()->user()->id)
                        ->orWhere('manager_id',  auth()->user()->id)
                        ->orWhere('logist_id',  auth()->user()->id)
                        ->orWhereNotNull('management_expenses')
                        ->orWhereJsonContains('access_to_project', auth()->user()->id);
                });
            }

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

            if(!auth()->user()->can('view and access all projects') && !in_array(auth()->user()->getRoleNames()[0], ['director', 'accountant', 'super-admin'])){
                $records->where(function ($query) {
                    $query->where('user_id', auth()->user()->id)
                        ->orWhere('manager_id',  auth()->user()->id)
                        ->orWhere('logist_id',  auth()->user()->id)
                        ->orWhereNotNull('management_expenses')
                        ->orWhereJsonContains('access_to_project', auth()->user()->id);
                });
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

    public function getProjectTable(ProjectFilter $filter, Request $request){

        if($request->data_range != '' && $request->data_range !='Все'){
            $range = explode(' - ', $request->data_range);
            $range_from = \Carbon\Carbon::parse($range[0])->format('Y-m-d');
            $range_to = \Carbon\Carbon::parse($range[1])->format('Y-m-d');
        }
        else {
            $range_from = '2000-01-01';
            $range_to = '3000-01-01';
        }

        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value

        // Total records
        $totalRecords = Project::filter($filter);
        if($request->data_range != ''){
            $totalRecords->whereDate('finished_at', '>=', $range_from)
                ->whereDate('finished_at', '<=', $range_to);
        }
        if(!auth()->user()->can('view and access all projects') && !in_array(auth()->user()->getRoleNames()[0], ['director', 'accountant', 'super-admin'])){
            $totalRecords->where(function ($query) {
                $query->where('user_id', auth()->user()->id)
                    ->orWhere('manager_id',  auth()->user()->id)
                    ->orWhere('logist_id',  auth()->user()->id)
                    ->orWhereNotNull('management_expenses')
                    ->orWhereJsonContains('access_to_project', auth()->user()->id);
            });
        }
        $totalRecords = $totalRecords->count();
        if($searchValue != ''){
            $totalRecordswithFilter = Project::where('projects.name', 'like', '%' . $searchValue . '%')
                ->orWhere('projects.from', 'like', '%' . $searchValue . '%')
                ->orWhere('projects.to', 'like', '%' . $searchValue . '%')
                ->orWhere('projects.additional_info', 'like', '%' . $searchValue . '%')
                ->orWhere('projects.status', 'like', '%' . $searchValue . '%')
                ->orWhere('projects.created_at', 'like', '%' . $searchValue . '%')
                ->orWhere('projects.finished_at', 'like', '%' . $searchValue . '%')
                ->orWhereHas('client', function ($q) use($searchValue)
                {
                    $q->where('name', 'like', '%' . $searchValue . '%');
                })
                ->orWhereHas('user', function ($q) use($searchValue)
                {
                    $q->where('name', 'like', '%' . $searchValue . '%');
                });
            if($request->data_range != ''){
                $totalRecordswithFilter->whereDate('finished_at', '>=', $range_from)
                    ->whereDate('finished_at', '<=', $range_to);
            }
            if(!auth()->user()->can('view and access all projects') && !in_array(auth()->user()->getRoleNames()[0], ['director', 'accountant', 'super-admin'])){
                $totalRecordswithFilter->where(function ($query) {
                    $query->where('user_id', auth()->user()->id)
                        ->orWhere('manager_id',  auth()->user()->id)
                        ->orWhere('logist_id',  auth()->user()->id)
                        ->orWhereNotNull('management_expenses')
                        ->orWhereJsonContains('access_to_project', auth()->user()->id);
                });
            }
            $totalRecordswithFilter = $totalRecordswithFilter->filter($filter)
                ->count();

            // Fetch records
            $records = Project::where('projects.name', 'like', '%' . $searchValue . '%')
                ->orWhere('projects.from', 'like', '%' . $searchValue . '%')
                ->orWhere('projects.to', 'like', '%' . $searchValue . '%')
                ->orWhere('projects.additional_info', 'like', '%' . $searchValue . '%')
                ->orWhere('projects.status', 'like', '%' . $searchValue . '%')
                ->orWhere('projects.created_at', 'like', '%' . $searchValue . '%')
                ->orWhere('projects.finished_at', 'like', '%' . $searchValue . '%')
                ->orWhereHas('client', function ($q) use($searchValue)
                {
                    $q->where('name', 'like', '%' . $searchValue . '%');
                })
                ->orWhereHas('user', function ($q) use($searchValue)
                {
                    $q->where('name', 'like', '%' . $searchValue . '%');
                })
                ->orWhereHas('manager', function ($q) use($searchValue)
                {
                    $q->where('name', 'like', '%' . $searchValue . '%');
                })
                ->orWhereHas('logist', function ($q) use($searchValue)
                {
                    $q->where('name', 'like', '%' . $searchValue . '%');
                });

            if($request->data_range != ''){
                $records->whereDate('finished_at', '>=', $range_from)
                    ->whereDate('finished_at', '<=', $range_to);
            }

            if(!auth()->user()->can('view and access all projects') && !in_array(auth()->user()->getRoleNames()[0], ['director', 'accountant', 'super-admin'])){
                $records->where(function ($query) {
                    $query->where('user_id', auth()->user()->id)
                        ->orWhere('manager_id',  auth()->user()->id)
                        ->orWhere('logist_id',  auth()->user()->id)
                        ->orWhereNotNull('management_expenses')
                        ->orWhereJsonContains('access_to_project', auth()->user()->id);
                });
            }

            $records = $records
                ->filter($filter)
                ->orderBy($columnName, $columnSortOrder)
                ->select('projects.*')
                ->skip($start)
                ->take($rowperpage)
                ->get();
        }
        else {
            $totalRecordswithFilter = Project::filter($filter);
            if($request->data_range != ''){
                $totalRecordswithFilter->whereDate('finished_at', '>=', $range_from)
                    ->whereDate('finished_at', '<=', $range_to);
            }

            if(!auth()->user()->can('view and access all projects') && !in_array(auth()->user()->getRoleNames()[0], ['director', 'accountant', 'super-admin'])){
                $totalRecordswithFilter->where(function ($query) {
                    $query->where('user_id', auth()->user()->id)
                        ->orWhere('manager_id',  auth()->user()->id)
                        ->orWhere('logist_id',  auth()->user()->id)
                        ->orWhereNotNull('management_expenses')
                        ->orWhereJsonContains('access_to_project', auth()->user()->id);
                });
            }
            $totalRecordswithFilter = $totalRecordswithFilter->count();

            $records = Project::orderBy($columnName, $columnSortOrder);

            if($request->data_range != ''){
                $records->whereDate('finished_at', '>=', $range_from)
                    ->whereDate('finished_at', '<=', $range_to);
            }
            if(!auth()->user()->can('view and access all projects') && !in_array(auth()->user()->getRoleNames()[0], ['director', 'accountant', 'super-admin'])){
                $records->where(function ($query) {
                    $query->where('user_id', auth()->user()->id)
                        ->orWhere('manager_id',  auth()->user()->id)
                        ->orWhere('logist_id',  auth()->user()->id)
                        ->orWhereNotNull('management_expenses')
                        ->orWhereJsonContains('access_to_project', auth()->user()->id);
                });
            }

            $records = $records
                ->filter($filter)
                ->select('projects.*')
                ->skip($start)
                ->take($rowperpage)
                ->get();
        }


        $data_arr = array();

        $sno = $start + 1;

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
