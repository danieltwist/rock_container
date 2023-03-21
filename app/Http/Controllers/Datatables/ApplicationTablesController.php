<?php

namespace App\Http\Controllers\Datatables;

use App\Filters\ApplicationFilter;
use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;

class ApplicationTablesController extends Controller
{
    public function getApplicationTable(ApplicationFilter $filter, Request $request){
        if (in_array($request->data_range, ['', 'all', 'Все'])) {
            $range = '2000-01-01 - 3000-01-01';
        }
        else {
            $range = $request->data_range;
        }

        $range = explode(' - ', $range);

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
        $totalRecords = Application::filter($filter)
            ->whereDate('created_at', '>=', $range[0])
            ->whereDate('created_at', '<=', $range[1]);

        $totalRecords = $totalRecords->count();

        if($searchValue != ''){
            $records = Application::filter($filter)->orderBy($columnName, $columnSortOrder);
            $records = $records->where(function ($query) use ($searchValue) {
                foreach (['id', 'name', 'counterparty_type', 'status', 'contract_info', 'client_name', 'supplier_name', 'containers_amount', 'containers', 'price_amount', 'send_from_country', 'send_from_city', 'send_to_country', 'send_to_city', 'place_of_delivery_country', 'place_of_delivery_city', 'grace_period', 'snp_after_range', 'additional_info'] as $item){
                    $query->orWhere($item, 'like', '%' . $searchValue . '%');
                }
            });

            $totalRecordswithFilter = $records->count();

            $records = $records->select('applications.*')
                ->skip($start)
                ->take($rowperpage)
                ->get();
        }
        else {
            $records = Application::filter($filter)->orderBy($columnName, $columnSortOrder);

            $totalRecordswithFilter = $records->count();

            //dd($filter);

            $records = $records->select('applications.*')
                ->skip($start)
                ->take($rowperpage)
                ->get();
        }

        $data_arr = array();

        $sno = $start + 1;

        foreach ($records as $application) {

            $id = $application->id;

            $name = view('application.table.name', [
                'application' => $application
            ])->render();

            $counterparty = view('application.table.counterparty', [
                'application' => $application
            ])->render();

            $places = view('application.table.places', [
                'application' => $application
            ])->render();

            $conditions = view('application.table.conditions', [
                'application' => $application
            ])->render();

            $containers = view('application.table.containers', [
                'application' => $application
            ])->render();

            $actions = view('application.table.actions', [
                'application' => $application
            ])->render();

            $data_arr[] = array(
                "id" => $id,
                "name" => $name,
                "counterparty_type" => $counterparty,
                "send_from_country" => $places,
                "price_amount" => $conditions,
                "containers_amount" => $containers,
                "created_at" => $actions
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
