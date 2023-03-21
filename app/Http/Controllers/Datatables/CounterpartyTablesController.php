<?php

namespace App\Http\Controllers\Datatables;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Supplier;
use Illuminate\Http\Request;

class CounterpartyTablesController extends Controller
{
    public function getClientTable(Request $request){

        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length");

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column'];
        $columnName = $columnName_arr[$columnIndex]['data'];
        $columnSortOrder = $order_arr[0]['dir'];
        $searchValue = $search_arr['value'];

        $totalRecords = Client::query();
        if($request->country != '') {
            $totalRecords->where('country', $request->country);
        }
        $totalRecords = $totalRecords->count();

        if($searchValue != ''){
            $withFilter = Client::orderBy($columnName, $columnSortOrder)
                ->where('clients.id', $searchValue)
                ->orWhere('clients.name', 'like', '%' . $searchValue . '%')
                ->orWhere('clients.requisites', 'like', '%' . $searchValue . '%')
                ->orWhere('clients.inn', 'like', '%' . $searchValue . '%')
                ->orWhere('clients.country', 'like', '%' . $searchValue . '%')
                ->orWhere('clients.email', 'like', '%' . $searchValue . '%')
                ->orWhere('clients.additional_info', 'like', '%' . $searchValue . '%')
                ->orWhereHas('contracts', function ($q) use($searchValue)
                {
                    $q->where('name', 'like', '%' . $searchValue . '%');
                });
            if($request->country != '') {
                $withFilter->where('country', $request->country);
            }
            $withFilter->select('clients.*')
                ->skip($start)
                ->take($rowperpage);

            $totalRecordswithFilter = $withFilter->count();
            $records = $withFilter->get();
        }
        else {
            $totalRecordswithFilter = Client::query();
            if($request->country != '') {
                $totalRecordswithFilter->where('country', $request->country);
            }
            $totalRecordswithFilter = $totalRecordswithFilter->count();

            $records = Client::orderBy($columnName, $columnSortOrder);
            if($request->country != '') {
                $records->where('country', $request->country);
            }
            $records = $records->select('clients.*')
                ->skip($start)
                ->take($rowperpage)
                ->get();
        }


        $data_arr = array();

        $sno = $start + 1;

        foreach ($records as $client) {

            $id = $client->id;

            $name = view('client.table.name', [
                'client' => $client
            ])->render();

            $requisites = view('client.table.requisites', [
                'client' => $client
            ])->render();

            $info = view('client.table.info', [
                'client' => $client
            ])->render();

            $resources = view('client.table.resources', [
                'client' => $client
            ])->render();

            $actions = view('client.table.actions', [
                'client' => $client
            ])->render();

            $data_arr[] = array(
                "id" => $id,
                "name" => $name,
                "requisites" => $requisites,
                "info" => $info,
                "resources" => $resources,
                "actions" => $actions
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

    public function getSupplierTable(Request $request){

        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length");

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column'];
        $columnName = $columnName_arr[$columnIndex]['data'];
        $columnSortOrder = $order_arr[0]['dir'];
        $searchValue = $search_arr['value'];

        $totalRecords = Supplier::query();
        if($request->country != '') {
            $totalRecords->where('country', $request->country);
        }
        if($request->type != '') {
            $totalRecords->where('type','like', '%' . $request->type . '%');
        }
        $totalRecords = $totalRecords->count();

        if($searchValue != ''){
            $withFilter = Supplier::orderBy($columnName, $columnSortOrder)
                ->where('suppliers.id', $searchValue)
                ->orWhere('suppliers.name', 'like', '%' . $searchValue . '%')
                ->orWhere('suppliers.requisites', 'like', '%' . $searchValue . '%')
                ->orWhere('suppliers.inn', 'like', '%' . $searchValue . '%')
                ->orWhere('suppliers.country', 'like', '%' . $searchValue . '%')
                ->orWhere('suppliers.email', 'like', '%' . $searchValue . '%')
                ->orWhere('suppliers.additional_info', 'like', '%' . $searchValue . '%')
                ->orWhereHas('contracts', function ($q) use($searchValue)
                {
                    $q->where('name', 'like', '%' . $searchValue . '%');
                });
            if($request->country != '') {
                $withFilter->where('country', $request->country);
            }
            if($request->type != '') {
                $withFilter->where('type','like', '%' . $request->type . '%');
            }
            $withFilter->select('suppliers.*')
                ->skip($start)
                ->take($rowperpage);

            $totalRecordswithFilter = $withFilter->count();
            $records = $withFilter->get();
        }
        else {
            $totalRecordswithFilter = Supplier::query();
            if($request->country != '') {
                $totalRecordswithFilter->where('country', $request->country);
            }
            if($request->type != '') {
                $totalRecordswithFilter->where('type','like', '%' . $request->type . '%');
            }
            $totalRecordswithFilter = $totalRecordswithFilter->count();

            $records = supplier::orderBy($columnName, $columnSortOrder);
            if($request->country != '') {
                $records->where('country', $request->country);
            }
            if($request->type != '') {
                $records->where('type','like', '%' . $request->type . '%');
            }
            $records = $records->select('suppliers.*')
                ->skip($start)
                ->take($rowperpage)
                ->get();
        }


        $data_arr = array();

        $sno = $start + 1;

        foreach ($records as $supplier) {

            $id = $supplier->id;

            $name = view('supplier.table.name', [
                'supplier' => $supplier
            ])->render();

            $requisites = view('supplier.table.requisites', [
                'supplier' => $supplier
            ])->render();

            $info = view('supplier.table.info', [
                'supplier' => $supplier
            ])->render();

            $resources = view('supplier.table.resources', [
                'supplier' => $supplier
            ])->render();

            $actions = view('supplier.table.actions', [
                'supplier' => $supplier
            ])->render();

            $data_arr[] = array(
                "id" => $id,
                "name" => $name,
                "requisites" => $requisites,
                "info" => $info,
                "resources" => $resources,
                "actions" => $actions
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
