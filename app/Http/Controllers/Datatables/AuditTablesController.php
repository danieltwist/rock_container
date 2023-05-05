<?php

namespace App\Http\Controllers\Datatables;

use App\Http\Controllers\Controller;
use App\Http\Traits\AuditTrait;
use App\Models\Application;
use App\Models\Project;
use Illuminate\Http\Request;
use OwenIt\Auditing\Models\Audit;

class AuditTablesController extends Controller
{
    use AuditTrait;

    public function getAuditTable(Request $request){

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

        $records = Audit::query();

        if(isset($request->user)){
            $records->where('user_id', $request->user);
        }

        $records = $records->orderBy($columnName, $columnSortOrder);

        $totalRecordswithFilter = $records->count();
        $totalRecords = $totalRecordswithFilter;

        $records = $records->select('audits.*')
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = array();

        $sno = $start + 1;

        foreach ($records as $audit) {

            $audit = $this->prepareForTable($audit);

            if($audit->skip) continue;

            $id = $audit->id;

            $date = $audit->created_at->format('d.m.Y H:i:s');

            $user = view('audit.table.user', [
                'audit' => $audit
            ])->render();

            $component = view('audit.table.component', [
                'audit' => $audit
            ])->render();

            $event = $audit->event;

            $before = view('audit.table.before', [
                'audit' => $audit
            ])->render();

            $after = view('audit.table.after', [
                'audit' => $audit
            ])->render();

            $data_arr[] = array(
                "id" => $id,
                "created_at" => $date,
                "user" => $user,
                "auditable_type" => $component,
                "event" => $event,
                "old_values" => $before,
                "new_values" => $after
            );

        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr,
        );


        echo json_encode($response);
        exit;
    }

    public function getComponentHistoryTable(Request $request){
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

        $records = Audit::query();

        if(isset($request->application)){
            $application_invoices = Application::withTrashed()->find($request->application)->invoices->pluck('id');
            $records->where('auditable_type', 'App\Models\Application')->where('auditable_id', $request->application);
            $records = $records->orWhere(function ($query) use ($application_invoices) {
                $query->where('auditable_type', 'App\Models\Invoice')->whereIN('auditable_id', $application_invoices);
            });
        }

        if(isset($request->user)){
            $records->where('user_id', $request->user);
        }

        if(isset($request->invoice)){
            $records->where('auditable_type', 'App\Models\Invoice')->where('auditable_id', $request->invoice);
        }

        if(isset($request->project)){
            $project_invoices = Project::withTrashed()->find($request->project)->invoices->pluck('id');
            $records->where('auditable_type', 'App\Models\Project')->where('auditable_id', $request->project);
            $records = $records->orWhere(function ($query) use ($project_invoices) {
                $query->where('auditable_type', 'App\Models\Invoice')->whereIN('auditable_id', $project_invoices);
            });
        }

        $records = $records->orderBy($columnName, $columnSortOrder);

        $totalRecordswithFilter = $records->count();
        $totalRecords = $totalRecordswithFilter;

        $records = $records->select('audits.*')
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = array();

        $sno = $start + 1;

        foreach ($records as $audit) {

            $audit = $this->prepareForTable($audit);

            if($audit->skip) continue;

            $id = $audit->id;

            $info = view('audit.table.info', [
                'audit' => $audit
            ])->render();

            $before = view('audit.table.before', [
                'audit' => $audit
            ])->render();

            $after = view('audit.table.after', [
                'audit' => $audit
            ])->render();

            $data_arr[] = array(
                "created_at" => $info,
                "old_values" => $before,
                "new_values" => $after
            );

        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr,
        );


        echo json_encode($response);
        exit;
    }
}
