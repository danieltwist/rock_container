<?php

namespace App\Http\Controllers\Datatables;

use App\Filters\ContainerFilter;
use App\Http\Controllers\Controller;
use App\Http\Traits\ContainerTrait;
use App\Models\Container;
use App\Models\ContainerGroup;
use App\Models\ContainerHistory;
use App\Models\ContainerUsageStatistic;
use App\Models\Country;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContainerTablesController extends Controller
{
    use ContainerTrait;

    public function getContainerGroupTable(Request $request){

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

        $group = ContainerGroup::findOrFail($request->group_id);
        $containers_arr = unserialize($group->containers);

        $totalRecords = Container::whereIn('id', $containers_arr)->count();

        if($searchValue != ''){
            $withFilter = Container::whereIn('id', $containers_arr)
                ->orderBy($columnName, $columnSortOrder)
                ->where('containers.id', $searchValue)
                ->orWhere('containers.name', 'like', '%' . $searchValue . '%')
                ->orWhere('containers.type', 'like', '%' . $searchValue . '%')
                ->orWhere('containers.start_date', 'like', '%' . $searchValue . '%')
                ->orWhere('containers.country', 'like', '%' . $searchValue . '%')
                ->orWhere('containers.city', 'like', '%' . $searchValue . '%')
                ->orWhere('containers.additional_info', 'like', '%' . $searchValue . '%')
                ->orWhere('containers.svv', 'like', '%' . $searchValue . '%')
                ->orWhereHas('project', function ($q) use($searchValue)
                {
                    $q->where('name', 'like', '%' . $searchValue . '%');
                })
                ->orWhereHas('supplier', function ($q) use($searchValue)
                {
                    $q->where('name', 'like', '%' . $searchValue . '%');
                })
                ->select('containers.*')
                ->skip($start)
                ->take($rowperpage);

            $totalRecordswithFilter = $withFilter->count();
            $records = $withFilter->get();
        }
        else {
            $totalRecordswithFilter = Container::whereIn('id', $containers_arr)->count();
            $records = Container::whereIn('id', $containers_arr)
                ->orderBy($columnName, $columnSortOrder)
                ->select('containers.*')
                ->skip($start)
                ->take($rowperpage)
                ->get();
        }


        $data_arr = array();

        $sno = $start + 1;

        foreach ($records as $container) {

            $container = Container::find($container->id);

            $container->usage_statistic = ContainerUsageStatistic::where('project_id', $group->project_id)->where('container_id', $container->id)->get();
            $container->usage_dates = $this->getContainerUsageDates($container->id);

            $class = '';

            if($container->problem_id != ''){
                $class = 'table-danger';
            }
            if ($container->usage_statistic->isNotEmpty()){
                $class = 'table-success';
            }

            $id = $container->id;

            $number = view('project.containers_table.number', [
                'container' => $container
            ])->render();

            $dates = view('project.containers_table.dates', [
                'container' => $container,
                'group' => $group
            ])->render();

            $usage = view('project.containers_table.usage', [
                'container' => $container
            ])->render();

            $place = view('project.containers_table.place', [
                'container' => $container
            ])->render();

            $return = view('project.containers_table.return', [
                'container' => $container,
                'group' => $group
            ])->render();

            $data_arr[] = array(
                "id" => $id,
                "class" => $class,
                "name" => $number,
                "dates" => $dates,
                "usage" => $usage,
                "place" => $place,
                "return" => $return
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

    public function extendedTable(){
        return view('container.index_extended', [
            'columns' => $this->columns,
        ]);
    }

    public function archiveTable(){
        return view('container.table_extended.archive_table', [
            'columns' => $this->columns,
        ]);
    }

    public function extendedTableGet(Request $request){
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

        $totalRecords = Container::query();
        if($request->filter != 'archive') $totalRecords->whereNull('archive');
        $totalRecords = $totalRecords->count();

        if($searchValue != ''){
            $probable_delimiters = array(", ", " ", ";", "\r\n", "\r", "\n", "\n,", ",");
            $used_delimiter = null;

            foreach ($probable_delimiters as $delimiter){
                if(strpos($searchValue, $delimiter) !== false ){
                    $used_delimiter = $delimiter;
                    break;
                }
            }
            $records = Container::query()->orderBy($columnName, $columnSortOrder);

            if(isset($request->application))
                $records->application($request->application);
            if(isset($request->containers_names)){
                if(!empty(unserialize($request->containers_names))){
                    $records->getByNames(unserialize($request->containers_names));
                }
            }

            $records = $records->where(function ($query) use ($searchValue, $used_delimiter) {
                foreach ($this->columns as $column){
                    if($column['id'] == 'name' && !is_null($used_delimiter)){
                        foreach (explode($used_delimiter, $searchValue) as $prefix){
                            $query->orWhere('name', 'like', '%' . $prefix . '%');
                        }
                    }
                    else
                        $query->orWhere($column['id'], 'like', '%' . $searchValue . '%');
                }
            });

            $records = $records->where(function ($query) use ($request) {
                foreach ($this->columns as $key => $value){
                    $searchValue = $request->columns[$key]['search']['value'];
                    if(!is_null($searchValue)) {
                        if(in_array($key, ["11", "12", "18", "31", "32", "46"])){
                            $query->where(function ($query) use ($searchValue, $value) {
                                foreach (explode(',', $searchValue) as $city){
                                    $query->orWhere('containers.'.$value['id'], 'like', '%' . $city . '%');
                                }
                            });
                        }
                        elseif($value['search_type'] == 'select') {
                            $query->whereIN('containers.'.$value['id'], explode(',', $searchValue));
                        }
                        elseif($value['search_type'] == 'input'){
                            $query->where('containers.'.$value['id'], 'like', '%' . $searchValue . '%');
                        }
                    }
                }
                if($request->filter != 'archive') $query->whereNull('archive');
            });

            $totalRecordswithFilter = $records->count();
            $id_list = $records->pluck('id');
            $prefix_list = $records->pluck('name');
            $records = $records->select('containers.*')
                ->skip($start)
                ->take($rowperpage)
                ->get();
        }
        else {
            $records = Container::orderBy($columnName, $columnSortOrder);
            if($request->filter != 'archive')
                $records->whereNull('archive');
//            if($request->filter == 'svv')
//                $records->svv();
            if(isset($request->application))
                $records->application($request->application);
            if(isset($request->containers_names)){
                if(!empty(unserialize($request->containers_names))){
                    $records->getByNames(unserialize($request->containers_names));
                }
            }

            foreach ($this->columns as $key => $value){
                $searchValue = $request->columns[$key]['search']['value'];
                if(!is_null($searchValue)) {
                    if(in_array($key, ["11", "12", "18", "31", "32", "46"])){
                        $records->where(function ($query) use ($searchValue, $value) {
                            foreach (explode(',', $searchValue) as $city){
                                $query->orWhere('containers.'.$value['id'], 'like', '%' . $city . '%');
                            }
                        });
                    }
                    elseif($value['search_type'] == 'select') {
                        $records->whereIN('containers.'.$value['id'], explode(',', $searchValue));
                    }
                    elseif($value['search_type'] == 'input'){
                        $records->where('containers.'.$value['id'], 'like', '%' . $searchValue . '%');
                    }
                }
            }

            $totalRecordswithFilter = $records->count();
            $id_list = $records->pluck('id');
            $prefix_list = $records->pluck('name');

            $records = $records->select('containers.*')
                ->skip($start)
                ->take($rowperpage)
                ->get();
        }

        $data_arr = array();

        $sno = $start + 1;

        foreach ($records as $container) {
            $surcharge_supplier = false;
            $surcharge_client = false;
            $surcharge_relocation = false;

            is_null(optional($container->application_supplier)->surcharge) || ($surcharge_supplier = true);
            is_null(optional($container->application_client)->surcharge) || ($surcharge_client = true);
            is_null(optional($container->application_relocation)->surcharge) || ($surcharge_relocation = true);

            $supplier_snp = $container->supplier_snp_after_range.$container->supplier_snp_currency;
            if(!is_null($container->supplier_snp_range)){
                $supplier_snp_range = [];
                foreach ($container->supplier_snp_range as $range){
                    $supplier_snp_range [] = $range['range'].': '.$range['price'].$container->supplier_snp_currency;
                }
                $supplier_snp_range [] = 'далее - '.$container->supplier_snp_after_range.$container->supplier_snp_currency;

                $supplier_snp = implode(', ', $supplier_snp_range);
            }

            $client_snp = $container->client_snp_after_range.$container->client_snp_currency;
            if(!is_null($container->client_snp_range)){
                $client_snp_range = [];
                foreach ($container->supplier_snp_range as $range){
                    $client_snp_range [] = $range['range'].': '.$range['price'].$container->client_snp_currency;
                }
                $client_snp_range [] = 'далее - '.$container->client_snp_after_range.$container->client_snp_currency;

                $client_snp = implode(', ', $client_snp_range);
            }

            $relocation_snp = $container->relocation_snp_after_range.$container->relocation_snp_currency;
            if(!is_null($container->relocation_snp_range)){
                $relocation_snp_range = [];
                foreach ($container->relocation_snp_range as $range){
                    $relocation_snp_range [] = $range['range'].': '.$range['price'].$container->relocation_snp_currency;
                }
                $relocation_snp_range [] = 'далее - '.$container->relocation_snp_after_range.$container->relocation_snp_currency;

                $relocation_snp = implode(', ', $relocation_snp_range);
            }

            $class = '';

            if(!is_null($container->removed)){
                $class = 'table-danger';
            }
            if(!is_null($container->processing)){
                if(auth()->user()->name != $container->processing){
                    $class = 'table-info';
                }
                else $class = 'table-warning';
            }

            if(!is_null($container->supplier_application_id)){
                $supplier_application = '<a href="'. route('application.show', $container->supplier_application_id) .'">'.$container->supplier_application_name.'</a>';
            }
            else {
                $supplier_application = '';
            }

            if(!is_null($container->owner_id)){
                $owner_name = [
                    'name' => $container->owner_name,
                    'id' => $container->owner_id
                ];
            }
            else {
                $owner_name = '';
            }

            if(!is_null($container->supplier_snp_total)){
                $supplier_snp_total = $container->supplier_snp_total.$container->supplier_snp_currency;
            }
            else {
                $supplier_snp_total = '';
            }

            if(!is_null($container->relocation_counterparty_name)){
                $relocation_counterparty_name = [
                    'name' => $container->relocation_counterparty_name,
                    'id' => $container->relocation_counterparty_id,
                    'type' => $container->relocation_counterparty_type,
                ];
            }
            else {
                $relocation_counterparty_name = '';
            }

            if(!is_null($container->relocation_application_name)){
                $relocation_application_name = '<a href="'. route('application.show', $container->relocation_application_id) .'">'.$container->relocation_application_name.'</a>';
            }
            else {
                $relocation_application_name = '';
            }

            if(!is_null($container->relocation_snp_total)){
                $relocation_snp_total = $container->relocation_snp_total.$container->relocation_snp_currency;
            }
            else {
                $relocation_snp_total = '';
            }

            if(!is_null($container->client_counterparty_name)){
                $client_counterparty_name = [
                    'name' => $container->client_counterparty_name,
                    'id' => $container->client_counterparty_id,
                ];
            }
            else {
                $client_counterparty_name = '';
            }

            if(!is_null($container->client_application_name)){
                $client_application_name = '<a href="'. route('application.show', $container->client_application_id) .'">'.$container->client_application_name.'</a>';
            }
            else {
                $client_application_name = '';
            }

            if(!is_null($container->client_snp_total)){
                $client_snp_total = $container->client_snp_total.$container->client_snp_currency;
            }
            else {
                $client_snp_total = '';
            }


            $data_arr[] = array(
                "id" => $container->id,
                "name" => '<a href="'. route('container.show', $container->id) .'">'.$container->name.'</a>',
                "status" => $container->status,
                "type" => $container->type,
                "owner_name" => $owner_name,
                "size" => $container->size,
                "supplier_application_name" => $supplier_application,
                "supplier_price_amount" => $surcharge_supplier ? '-'.$container->supplier_price_amount.$container->supplier_price_currency : $container->supplier_price_amount.$container->supplier_price_currency,
                "supplier_grace_period" => $container->supplier_grace_period,
                "supplier_snp_after_range" => $supplier_snp,
                "supplier_country" => $container->supplier_country,
                "supplier_city" => $container->supplier_city,
                "supplier_terminal" => $container->supplier_terminal,
                "supplier_date_get" => !is_null($container->supplier_date_get) ? $container->supplier_date_get->format('d.m.Y') : '',
                "supplier_date_start_using" => !is_null($container->supplier_date_start_using) ? $container->supplier_date_start_using->format('d.m.Y') : '',
                "supplier_days_using" => $container->supplier_days_using,
                "supplier_snp_total" => $supplier_snp_total,
                "supplier_place_of_delivery_country" => $container->supplier_place_of_delivery_country,
                "supplier_place_of_delivery_city" => $container->supplier_place_of_delivery_city,
                "svv" => $container->svv,
                "supplier_terminal_storage_amount" => $container->supplier_terminal_storage_amount.$container->supplier_terminal_storage_currency,
                "supplier_payer_tx" => $container->supplier_payer_tx,
                "supplier_renewal_reexport_costs_amount" => $container->supplier_renewal_reexport_costs_amount.$container->supplier_renewal_reexport_costs_currency,
                "supplier_repair_amount" => $container->supplier_repair_amount.$container->supplier_repair_currency,
                "supplier_repair_status" => $container->supplier_repair_status,
                "supplier_repair_confirmation" => $container->supplier_repair_confirmation,
                "supplier_date_return" => !is_null($container->supplier_date_return) ? $container->supplier_date_return->format('d.m.Y') : '',
                "relocation_counterparty_name" => $relocation_counterparty_name,
                "relocation_application_name" => $relocation_application_name,
                "relocation_price_amount" => $surcharge_relocation ? '-'.$container->relocation_price_amount.$container->relocation_price_currency : $container->relocation_price_amount.$container->relocation_price_currency,
                "relocation_date_send" => !is_null($container->relocation_date_send) ? $container->relocation_date_send->format('d.m.Y') : '',
                "relocation_date_arrival_to_terminal" => !is_null($container->relocation_date_arrival_to_terminal) ? $container->relocation_date_arrival_to_terminal->format('d.m.Y') : '',
                "relocation_place_of_delivery_city" => $container->relocation_place_of_delivery_city,
                "relocation_place_of_delivery_terminal" => $container->relocation_place_of_delivery_terminal,
                "relocation_delivery_time_days" => $container->relocation_delivery_time_days,
                "relocation_snp_after_range" => $relocation_snp,
                "relocation_snp_total" => $relocation_snp_total,
                "relocation_repair_amount" => $container->relocation_repair_amount.$container->relocation_repair_currency,
                "relocation_repair_status" => $container->relocation_repair_status,
                "relocation_repair_confirmation" => $container->relocation_repair_confirmation,
                "client_counterparty_name" => $client_counterparty_name,
                "client_application_name" => $client_application_name,
                "client_price_amount" => $surcharge_client ? '-'.$container->client_price_amount.$container->client_price_currency : $container->client_price_amount.$container->client_price_currency,
                "client_grace_period" => $container->client_grace_period,
                "client_snp_after_range" => $client_snp,
                "client_date_get" => !is_null($container->client_date_get) ? $container->client_date_get->format('d.m.Y') : '',
                "client_date_return" => !is_null($container->client_date_return) ? $container->client_date_return->format('d.m.Y') : '',
                "client_place_of_delivery_city" => $container->client_place_of_delivery_city,
                "client_days_using" => $container->client_days_using,
                "client_snp_total" => $client_snp_total,
                "client_repair_amount" => $container->client_repair_amount.$container->client_repair_currency,
                "client_repair_status" => $container->client_repair_status,
                "client_repair_confirmation" => $container->client_repair_confirmation,
                "client_smgs" => $container->client_smgs,
                "client_manual" => $container->client_manual,
                "client_location_request" => $container->client_location_request,
                "client_date_manual_request" => !is_null($container->client_date_manual_request) ? $container->client_date_manual_request->format('d.m.Y') : '',
                "client_return_act" => $container->client_return_act,
                "own_date_buy" => !is_null($container->own_date_buy) ? $container->own_date_buy->format('d.m.Y') : '',
                "own_date_sell" => !is_null($container->own_date_sell) ? $container->own_date_sell->format('d.m.Y') : '',
                "own_sale_price" => $container->own_sale_price,
                "own_buyer" => $container->own_buyer,
                "processing" => $container->processing,
                "removed" => $container->removed,
                "additional_info" => $container->additional_info,
                "class" => $class
            );
        }

        $collapsed_exist = false;

        foreach ($data_arr as $key => $value){
            foreach ($value as $value_key => $text){

                if(!in_array($value_key, ['name', 'owner_name', 'supplier_application_name', 'relocation_counterparty_name', 'relocation_application_name', 'client_counterparty_name', 'client_application_name'])) {
                    if (mb_strlen($text) > 25){
                        $data_arr[$key][$value_key] = view('container.table_extended.collapsed_text', [
                            'id' => $data_arr[$key]['id'].'_'.$value_key,
                            'text' => $text
                        ])->render();
                        $collapsed_exist = true;
                    }
                }
                if(in_array($value_key, ['owner_name', 'relocation_counterparty_name', 'client_counterparty_name'])){
                    if($text != ''){
                        switch ($value_key){
                            case 'owner_name':
                                $id = $data_arr[$key]['id'].'_'.$value_key;
                                $route = route('supplier.show', $data_arr[$key]['owner_name']['id']);
                                $link_text = $data_arr[$key]['owner_name']['name'];
                                break;
                            case 'relocation_counterparty_name':
                                $id = $data_arr[$key]['id'].'_'.$value_key;
                                $route = route($data_arr[$key]['relocation_counterparty_name']['type'].'.show', $data_arr[$key]['relocation_counterparty_name']['id']);
                                $link_text = $data_arr[$key]['relocation_counterparty_name']['name'];
                                break;
                            case 'client_counterparty_name':
                                $id = $data_arr[$key]['id'].'_'.$value_key;
                                $route = route('client.show', $data_arr[$key]['client_counterparty_name']['id']);
                                $link_text = $data_arr[$key]['client_counterparty_name']['name'];
                                break;
                        }

                        if (mb_strlen($link_text) > 30){
                            $data_arr[$key][$value_key] = view('container.table_extended.collapsed_text_with_link', [
                                'id' => $id,
                                'route' => $route,
                                'text' => $link_text
                            ])->render();
                            $collapsed_exist = true;
                        }
                        else {
                            $data_arr[$key][$value_key] = '<a href="'. $route .'">'.$link_text.'</a>';
                        }
                    }

                }
            }
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr,
            "id_list" => $id_list,
            "prefix_list" => $prefix_list,
            "collapsed_exist" => $collapsed_exist
        );


        echo json_encode($response);
        exit;

    }

    public function applicationArchiveTableGet(Request $request){
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

        $totalRecords = ContainerHistory::query();
        if(isset($request->application))
            $totalRecords->application($request->application);
        if(isset($request->history))
            $totalRecords->history($request->history);
        if(isset($request->containers_names))
            if(!empty(unserialize($request->containers_names))){
                $totalRecords->getByNames(unserialize($request->containers_names));
            }

        $totalRecords = $totalRecords->count();

        if($searchValue != ''){
            $records = ContainerHistory::orderBy($columnName, $columnSortOrder);
            if(isset($request->application))
                $records->application($request->application);
            if(isset($request->history))
                $records->history($request->history);
            if(isset($request->containers_names))
                if(!empty(unserialize($request->containers_names))){
                    $records->getByNames(unserialize($request->containers_names));
                }
            $records = $records->where(function ($query) use ($searchValue) {
                foreach ($this->columns as $column){
                    $query->orWhere($column['id'], 'like', '%' . $searchValue . '%');
                }
            });

            $totalRecordswithFilter = $records->count();
            $id_list = $records->pluck('id');
            $prefix_list = $records->pluck('name');
            $records = $records->select('container_histories.*')
                ->skip($start)
                ->take($rowperpage)
                ->get();
        }
        else {
            $records = ContainerHistory::orderBy($columnName, $columnSortOrder);

            if(isset($request->application))
                $records->application($request->application);
            if(isset($request->history))
                $records->history($request->history);
            if(isset($request->containers_names))
                if(!empty(unserialize($request->containers_names))){
                    $records->getByNames(unserialize($request->containers_names));
                }

            $totalRecordswithFilter = $records->count();
            $id_list = $records->pluck('id');
            $prefix_list = $records->pluck('name');

            $records = $records->select('container_histories.*')
                ->skip($start)
                ->take($rowperpage)
                ->get();
        }

        $data_arr = array();

        $sno = $start + 1;

        foreach ($records as $container) {

            $surcharge_supplier = false;
            $surcharge_client = false;
            $surcharge_relocation = false;

            is_null(optional($container->application_supplier)->surcharge) || ($surcharge_supplier = true);
            is_null(optional($container->application_client)->surcharge) || ($surcharge_client = true);
            is_null(optional($container->application_relocation)->surcharge) || ($surcharge_relocation = true);

            $supplier_snp = $container->supplier_snp_after_range.$container->supplier_snp_currency;
            if(!is_null($container->supplier_snp_range)){
                $supplier_snp_range = [];
                foreach ($container->supplier_snp_range as $range){
                    $supplier_snp_range [] = $range['range'].': '.$range['price'].$container->supplier_snp_currency;
                }
                $supplier_snp_range [] = 'далее - '.$container->supplier_snp_after_range.$container->supplier_snp_currency;

                $supplier_snp = implode(', ', $supplier_snp_range);
            }

            $client_snp = $container->client_snp_after_range.$container->client_snp_currency;
            if(!is_null($container->client_snp_range)){
                $client_snp_range = [];
                foreach ($container->supplier_snp_range as $range){
                    $client_snp_range [] = $range['range'].': '.$range['price'].$container->client_snp_currency;
                }
                $client_snp_range [] = 'далее - '.$container->client_snp_after_range.$container->client_snp_currency;

                $client_snp = implode(', ', $client_snp_range);
            }

            $relocation_snp = $container->relocation_snp_after_range.$container->relocation_snp_currency;
            if(!is_null($container->relocation_snp_range)){
                $relocation_snp_range = [];
                foreach ($container->relocation_snp_range as $range){
                    $relocation_snp_range [] = $range['range'].': '.$range['price'].$container->relocation_snp_currency;
                }
                $relocation_snp_range [] = 'далее - '.$container->relocation_snp_after_range.$container->relocation_snp_currency;

                $relocation_snp = implode(', ', $relocation_snp_range);
            }

            $class = '';

            if(!is_null($container->removed)){
                $class = 'table-danger';
            }
            if(!is_null($container->processing)){
                if(auth()->user()->name != $container->processing){
                    $class = 'table-info';
                }
                else $class = 'table-warning';
            }

            if(!is_null($container->supplier_application_id)){
                $supplier_application = '<a href="'. route('application.show', $container->supplier_application_id) .'">'.$container->supplier_application_name.'</a>';
            }
            else {
                $supplier_application = '';
            }

            if(!is_null($container->owner_id)){
                $owner_name = [
                    'name' => $container->owner_name,
                    'id' => $container->owner_id
                ];
            }
            else {
                $owner_name = '';
            }

            if(!is_null($container->supplier_snp_total)){
                $supplier_snp_total = $container->supplier_snp_total.$container->supplier_snp_currency;
            }
            else {
                $supplier_snp_total = '';
            }

            if(!is_null($container->relocation_counterparty_name)){
                $relocation_counterparty_name = [
                    'name' => $container->relocation_counterparty_name,
                    'id' => $container->relocation_counterparty_id,
                    'type' => $container->relocation_counterparty_type,
                ];
            }
            else {
                $relocation_counterparty_name = '';
            }

            if(!is_null($container->relocation_application_name)){
                $relocation_application_name = '<a href="'. route('application.show', $container->relocation_application_id) .'">'.$container->relocation_application_name.'</a>';
            }
            else {
                $relocation_application_name = '';
            }

            if(!is_null($container->relocation_snp_total)){
                $relocation_snp_total = $container->relocation_snp_total.$container->relocation_snp_currency;
            }
            else {
                $relocation_snp_total = '';
            }

            if(!is_null($container->client_counterparty_name)){
                $client_counterparty_name = [
                    'name' => $container->client_counterparty_name,
                    'id' => $container->client_counterparty_id,
                ];
            }
            else {
                $client_counterparty_name = '';
            }

            if(!is_null($container->client_application_name)){
                $client_application_name = '<a href="'. route('application.show', $container->client_application_id) .'">'.$container->client_application_name.'</a>';
            }
            else {
                $client_application_name = '';
            }

            if(!is_null($container->client_snp_total)){
                $client_snp_total = $container->client_snp_total.$container->client_snp_currency;
            }
            else {
                $client_snp_total = '';
            }


            $data_arr[] = array(
                "id" => $container->id,
                "name" => $container->name,
                "status" => $container->status,
                "type" => $container->type,
                "owner_name" => $container->owner_name,
                "size" => $container->size,
                "supplier_application_name" => $supplier_application,
                "supplier_price_amount" => $surcharge_supplier ? '-'.$container->supplier_price_amount.$container->supplier_price_currency : $container->supplier_price_amount.$container->supplier_price_currency,
                "supplier_grace_period" => $container->supplier_grace_period,
                "supplier_snp_after_range" => $supplier_snp,
                "supplier_country" => $container->supplier_country,
                "supplier_city" => $container->supplier_city,
                "supplier_terminal" => $container->supplier_terminal,
                "supplier_date_get" => !is_null($container->supplier_date_get) ? $container->supplier_date_get->format('d.m.Y') : '',
                "supplier_date_start_using" => !is_null($container->supplier_date_start_using) ? $container->supplier_date_start_using->format('d.m.Y') : '',
                "supplier_days_using" => $container->supplier_days_using,
                "supplier_snp_total" => $supplier_snp_total,
                "supplier_place_of_delivery_country" => $container->supplier_place_of_delivery_country,
                "supplier_place_of_delivery_city" => $container->supplier_place_of_delivery_city,
                "svv" => $container->svv,
                "supplier_terminal_storage_amount" => $container->supplier_terminal_storage_amount.$container->supplier_terminal_storage_currency,
                "supplier_payer_tx" => $container->supplier_payer_tx,
                "supplier_renewal_reexport_costs_amount" => $container->supplier_renewal_reexport_costs_amount.$container->supplier_renewal_reexport_costs_currency,
                "supplier_repair_amount" => $container->supplier_repair_amount.$container->supplier_repair_currency,
                "supplier_repair_status" => $container->supplier_repair_status,
                "supplier_repair_confirmation" => $container->supplier_repair_confirmation,
                "supplier_date_return" => !is_null($container->supplier_date_return) ? $container->supplier_date_return->format('d.m.Y') : '',
                "relocation_counterparty_name" => $container->relocation_counterparty_name,
                "relocation_application_name" => $relocation_application_name,
                "relocation_price_amount" => $surcharge_relocation ? '-'.$container->relocation_price_amount.$container->relocation_price_currency : $container->relocation_price_amount.$container->relocation_price_currency,
                "relocation_date_send" => !is_null($container->relocation_date_send) ? $container->relocation_date_send->format('d.m.Y') : '',
                "relocation_date_arrival_to_terminal" => !is_null($container->relocation_date_arrival_to_terminal) ? $container->relocation_date_arrival_to_terminal->format('d.m.Y') : '',
                "relocation_place_of_delivery_city" => $container->relocation_place_of_delivery_city,
                "relocation_place_of_delivery_terminal" => $container->relocation_place_of_delivery_terminal,
                "relocation_delivery_time_days" => $container->relocation_delivery_time_days,
                "relocation_snp_after_range" => $relocation_snp,
                "relocation_snp_total" => $relocation_snp_total,
                "relocation_repair_amount" => $container->relocation_repair_amount.$container->relocation_repair_currency,
                "relocation_repair_status" => $container->relocation_repair_status,
                "relocation_repair_confirmation" => $container->relocation_repair_confirmation,
                "client_counterparty_name" => $container->client_counterparty_name,
                "client_application_name" => $client_application_name,
                "client_price_amount" => $surcharge_client ? '-'.$container->client_price_amount.$container->client_price_currency : $container->client_price_amount.$container->client_price_currency,
                "client_grace_period" => $container->client_grace_period,
                "client_snp_after_range" => $client_snp,
                "client_date_get" => !is_null($container->client_date_get) ? $container->client_date_get->format('d.m.Y') : '',
                "client_date_return" => !is_null($container->client_date_return) ? $container->client_date_return->format('d.m.Y') : '',
                "client_place_of_delivery_city" => $container->client_place_of_delivery_city,
                "client_days_using" => $container->client_days_using,
                "client_snp_total" => $client_snp_total,
                "client_repair_amount" => $container->client_repair_amount.$container->client_repair_currency,
                "client_repair_status" => $container->client_repair_status,
                "client_repair_confirmation" => $container->client_repair_confirmation,
                "client_smgs" => $container->client_smgs,
                "client_manual" => $container->client_manual,
                "client_location_request" => $container->client_location_request,
                "client_date_manual_request" => !is_null($container->client_date_manual_request) ? $container->client_date_manual_request->format('d.m.Y') : '',
                "client_return_act" => $container->client_return_act,
                "own_date_buy" => !is_null($container->own_date_buy) ? $container->own_date_buy->format('d.m.Y') : '',
                "own_date_sell" => !is_null($container->own_date_sell) ? $container->own_date_sell->format('d.m.Y') : '',
                "own_sale_price" => $container->own_sale_price,
                "own_buyer" => $container->own_buyer,
                "processing" => $container->processing,
                "removed" => $container->removed,
                "additional_info" => $container->additional_info,
                "class" => $class
            );
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr,
            "id_list" => $id_list,
            "prefix_list" => $prefix_list
        );


        echo json_encode($response);
        exit;

    }

    public function extendedTableGetFilters(Request $request){

        $filters = [];

        $sql_filters = Container::select(["name", "owner_name", "supplier_application_name", "supplier_city", "supplier_terminal", "supplier_place_of_delivery_city", "relocation_counterparty_name", "relocation_application_name", "relocation_place_of_delivery_city", "relocation_place_of_delivery_terminal", "client_counterparty_name", "client_application_name", "client_place_of_delivery_city"]);
        if($request->filter != 'archive')
            $sql_filters->whereNull('archive');
        else{
            $sql_filters->whereNotNull('archive');
        }
        $sql_filters = $sql_filters->groupBy(["name", "owner_name", "supplier_application_name", "supplier_city", "supplier_terminal", "supplier_place_of_delivery_city", "relocation_counterparty_name", "relocation_application_name", "relocation_place_of_delivery_city", "relocation_place_of_delivery_terminal", "client_counterparty_name", "client_application_name", "client_place_of_delivery_city"])->distinct()->get()->toArray();

        if(!empty($sql_filters)){
            foreach ($this->columns as $key => $value){
                if(in_array($key, ["1", "4", "6", "11", "12", "18", "26", "27", "31", "32", "39", "40", "47"])){
                    foreach ($sql_filters as $row){
                        if(in_array($key, ["11", "12", "18", "31", "32", "47"])){
                            if(!is_null($row[$value['id']])) {
                                foreach (explode(', ', $row[$value['id']]) as $city){
                                    $filters[$key][] = $city;
                                }
                            }
                            else $filters[$key][] = '';
                        }
                        else {
                            if(!is_null($row[$value['id']])) {
                                $filters[$key][] = $row[$value['id']];
                            }
                            else $filters[$key][] = '';
                        }
                    }
                    $filters[$key] = array_values(array_unique($filters[$key]));
                    //dump($filters[$key]);
                }
                else {
                    switch ($key){
                        case '2':
                            $filters[$key] =  ['В пути', 'Перемещение', 'К выдаче', 'Заблокирован', 'Хранение'];
                            break;
                        case '3':
                            $filters[$key] =  ['Соб', 'Аренда', 'ОУ'];
                            break;
                        case '5':
                            $filters[$key] =  ['40HC', '20DC', '40OT', '20OT', '40DC', '40RF'];
                            break;
                        case '10':
                        case '17':
                            $filters[$key] = Country::all()->pluck('name');
                            break;
                        case '21':
                            $filters[$key] =  ['Собственник', 'РК'];
                            break;
                        case '24':
                        case '37':
                        case '51':
                            $filters[$key] =  ['Отремонтирован', 'Целый'];
                            break;
                        case '25':
                        case '28':
                        case '52':
                            $filters[$key] =  ['Нет', 'Да'];
                            break;
                        case '53':
                        case '57':
                            $filters[$key] =  ['Нет', 'Запрошено', 'Загружено'];
                            break;
                        case '58':
                            $filters[$key] =  ['Нет', 'Запрошено', 'Предоставлено'];
                            break;
                        default:
                            $filters[$key] = [];
                    }
                }
            }
        }
        else {
            foreach ($this->columns as $key => $value){
                $filters[$key] = [];
            }
        }

        return [
            'filters' => $filters,
            'columns' => $this->columns
        ];
    }

    public function extendedTableGetColumns(){
        return [
            'columns' => $this->columns
        ];
    }

    public function loadTableForFilter(Request $request){
        return [
            'view' => view('container.table_extended.filter.filter_table', [
                    'columns' => $this->columns,
                    'type' => $request->type
                ]
            )->render()
        ];
    }

}
