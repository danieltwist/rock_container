<?php

namespace App\Http\Controllers\Datatables;

use App\Http\Controllers\Controller;
use App\Http\Traits\FinanceTrait;
use App\Models\Invoice;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Filters\InvoiceFilter;

class InvoiceTablesController extends Controller
{
    use FinanceTrait;

    public function getInvoicesWithFilter(InvoiceFilter $filter, Request $request){

        if($request->data_range != '' && $request->data_range !='Все'){
            $range = explode(' - ', $request->data_range);
            $range_from = $range[0];
            $range_to = $range[1];
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
        $totalRecords = Invoice::filter($filter);
        if($request->direction != ''){
            $totalRecords->where('direction', $request->direction);
        }
        $totalRecords = $totalRecords->whereDate('created_at', '>=', $range_from)
            ->whereDate('created_at', '<=', $range_to)
            ->count();

        if($searchValue != ''){
            $withFilter = Invoice::orderBy($columnName, $columnSortOrder)
                ->orWhere(function ($query) use ($searchValue) {
                    $query->where('invoices.amount', 'like', '%' . $searchValue . '%')
                        ->orWhere('invoices.id', $searchValue)
                        ->orWhere('invoices.amount_actual', 'like', '%' . $searchValue . '%')
                        ->orWhere('invoices.amount_paid', 'like', '%' . $searchValue . '%')
                        ->orWhere('invoices.amount_in_currency', 'like', '%' . $searchValue . '%')
                        ->orWhere('invoices.amount_in_currency_actual', 'like', '%' . $searchValue . '%')
                        ->orWhere('invoices.director_comment', 'like', '%' . $searchValue . '%')
                        ->orWhere('invoices.manager_comment', 'like', '%' . $searchValue . '%')
                        ->orWhere('invoices.accountant_comment', 'like', '%' . $searchValue . '%')
                        ->orWhere('invoices.additional_info', 'like', '%' . $searchValue . '%')
                        ->orWhere('invoices.status', 'like', '%' . $searchValue . '%')
                        ->orWhere('invoices.agree_1', 'like', '%' . $searchValue . '%')
                        ->orWhere('invoices.agree_2', 'like', '%' . $searchValue . '%')
                        ->orWhere('invoices.created_at', 'like', '%' . $searchValue . '%')
                        ->orWhereHas('client', function ($q) use($searchValue)
                        {
                            $q->where('name', 'like', '%' . $searchValue . '%');
                        })
                        ->orWhereHas('supplier', function ($q) use($searchValue)
                        {
                            $q->where('name', 'like', '%' . $searchValue . '%');
                        })
                        ->orWhereHas('project', function ($q) use($searchValue)
                        {
                            $q->where('name', 'like', '%' . $searchValue . '%');
                        });
                });
                if($request->direction != ''){
                    $withFilter->where('direction', $request->direction);
                }
            $withFilter = $withFilter->filter($filter)
                ->whereDate('created_at', '>=', $range_from)
                ->whereDate('created_at', '<=', $range_to)
                ->select('invoices.*')
                ->skip($start)
                ->take($rowperpage);

            $totalRecordswithFilter = $withFilter->count();
            $records = $withFilter->get();
        }
        else {
            $totalRecordswithFilter = Invoice::filter($filter);
            if($request->direction != ''){
                $totalRecordswithFilter->where('direction', $request->direction);
            }
            $totalRecordswithFilter= $totalRecordswithFilter->whereDate('created_at', '>=', $range_from)
                ->whereDate('created_at', '<=', $range_to)
                ->count();

            $records = Invoice::filter($filter);
            if($request->direction != ''){
                $records->where('direction', $request->direction);
            }

            $records = $records->whereDate('created_at', '>=', $range_from)
                ->whereDate('created_at', '<=', $range_to)
                ->orderBy($columnName, $columnSortOrder)
                ->select('invoices.*')
                ->skip($start)
                ->take($rowperpage)
                ->get();
        }


        $data_arr = array();

        $sno = $start + 1;

        foreach ($records as $invoice) {

            $class = $this->giveInvoiceTableColClass($invoice);

            $id = $invoice->id;

            $info = view('invoice.table.info', [
                'invoice' => $invoice
            ])->render();

            $kontragent = view('invoice.table.kontragent', [
                'invoice' => $invoice
            ])->render();

            $amount = view('invoice.table.amount', [
                'invoice' => $invoice
            ])->render();

            $paid = view('invoice.table.paid', [
                'invoice' => $invoice
            ])->render();

            $status = view('invoice.table.status', [
                'invoice' => $invoice
            ])->render();

            $actions = view('invoice.table.actions', [
                'invoice' => $invoice
            ])->render();

            $data_arr[] = array(
                "id" => $id,
                "direction" => $info,
                "project_id" => $kontragent,
                "amount" => $amount,
                "amount_paid" => $paid,
                "status" => $status,
                "class" => $class,
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

    public function getInvoiceTableForProjectAnalytics(InvoiceFilter $filter, Request $request){

        if (in_array($request->data_range, ['','all','Все'])) {
            $range = '2000-01-01 - 3000-01-01';
        }
        else {
            $range = $request->data_range;
        }

        $range = explode(' - ', $range);

        if(!is_null($request->project_filter_array)){

            $project_filter = $request->project_filter_array;
            $projects = Project::query();

            if($project_filter['filter'] == 'active'){
                $projects->where('active', '1')->where('status', '<>', 'Черновик');
            }

            if($project_filter['filter'] == 'finished'){
                $projects->where('active', '0')->where('status', '<>', 'Черновик')->where('paid', 'Оплачен');
            }

            if($project_filter['filter'] == 'finished_paid_date'){
                $projects->where('active', '0')->where('status', '<>', 'Черновик')->where('paid', 'Оплачен')->whereNotNull('paid_at');
            }

            if($project_filter['filter'] == 'done_unpaid'){
                $projects->where('status', 'Завершен')->where('paid', 'Не оплачен');
            }

            if($project_filter['user_id'] != 'Все'){
                $projects->where('user_id', $project_filter['user_id']);
            }

            if($project_filter['manager_id'] != 'Все'){
                $projects->where('user_id', $project_filter['manager_id']);
            }

            if(in_array($project_filter['filter'], ['finished', 'paid'])){
                $projects->whereDate('finished_at', '>=', $range[0])->whereDate('finished_at', '<=', $range[1]);
            }
            elseif($project_filter['filter'] == 'finished_paid_date'){
                $projects->whereDate('paid_at', '>=', $range[0])->whereDate('paid_at', '<=', $range[1]);
            }
            else {
                $projects->whereDate('created_at', '>=', $range[0])->whereDate('created_at', '<=', $range[1]);
            }

            $projects = $projects->pluck('id')->toArray();
        }
        else {
            $projects = \App\Models\Project::whereDate('finished_at', '>=', $range[0])
                ->whereDate('finished_at', '<=', $range[1])
                ->where('active', '0')
                ->where('status', '<>', 'Черновик')
                ->where('paid', 'Оплачен')
                ->pluck('id')
                ->toArray();
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
        $totalRecords = Invoice::filter($filter)->whereIn('project_id', $projects)->count();

        if($searchValue != ''){
            $withFilter = Invoice::query()->orWhere(function ($query) use ($searchValue) {
                $query->where('invoices.amount', 'like', '%' . $searchValue . '%')
                    ->orWhere('invoices.id', $searchValue)
                    ->orWhere('invoices.amount_actual', 'like', '%' . $searchValue . '%')
                    ->orWhere('invoices.amount_paid', 'like', '%' . $searchValue . '%')
                    ->orWhere('invoices.amount_in_currency', 'like', '%' . $searchValue . '%')
                    ->orWhere('invoices.amount_in_currency_actual', 'like', '%' . $searchValue . '%')
                    ->orWhere('invoices.director_comment', 'like', '%' . $searchValue . '%')
                    ->orWhere('invoices.manager_comment', 'like', '%' . $searchValue . '%')
                    ->orWhere('invoices.accountant_comment', 'like', '%' . $searchValue . '%')
                    ->orWhere('invoices.additional_info', 'like', '%' . $searchValue . '%')
                    ->orWhere('invoices.status', 'like', '%' . $searchValue . '%')
                    ->orWhere('invoices.agree_1', 'like', '%' . $searchValue . '%')
                    ->orWhere('invoices.agree_2', 'like', '%' . $searchValue . '%')
                    ->orWhere('invoices.created_at', 'like', '%' . $searchValue . '%')
                    ->orWhereHas('client', function ($q) use ($searchValue) {
                        $q->where('name', 'like', '%' . $searchValue . '%');
                    })
                    ->orWhereHas('supplier', function ($q) use ($searchValue) {
                        $q->where('name', 'like', '%' . $searchValue . '%');
                    })
                    ->orWhereHas('project', function ($q) use ($searchValue) {
                        $q->where('name', 'like', '%' . $searchValue . '%');
                    });
                })
                ->whereIn('project_id', $projects)
                ->filter($filter);

            $totalRecordswithFilter = $withFilter->count();
            $records = $withFilter->get();

        }
        else {
            $totalRecordswithFilter = Invoice::filter($filter)
                ->whereIn('project_id', $projects)
                ->count();

            $records = Invoice::filter($filter)
                ->whereIn('project_id', $projects)
                ->orderBy($columnName, $columnSortOrder)
                ->select('invoices.*')
                ->skip($start)
                ->take($rowperpage)
                ->get();
        }


        $data_arr = array();

        $sno = $start + 1;

        foreach ($records as $invoice) {

            $class = $this->giveInvoiceTableColClass($invoice);

            $id = $invoice->id;

            $info = view('invoice.table.info', [
                'invoice' => $invoice
            ])->render();

            $kontragent = view('invoice.table.kontragent', [
                'invoice' => $invoice
            ])->render();

            $amount = view('invoice.table.amount', [
                'invoice' => $invoice
            ])->render();

            $paid = view('invoice.table.paid', [
                'invoice' => $invoice
            ])->render();

            $status = view('invoice.table.status', [
                'invoice' => $invoice
            ])->render();

            $actions = view('invoice.table.actions', [
                'invoice' => $invoice
            ])->render();

            $data_arr[] = array(
                "id" => $id,
                "direction" => $info,
                "project_id" => $kontragent,
                "amount" => $amount,
                "class" => $class,
                "amount_paid" => $paid,
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

    public function getInvoiceTable(InvoiceFilter $filter, Request $request){

        if($request->data_range != '' && $request->data_range !='Все'){
            $range = explode(' - ', $request->data_range);
            $range_from = $range[0];
            $range_to = $range[1];
        }
        else {
            $range_from = '2000-01-01';
            $range_to = '3000-01-01';
        }

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

        // Total records
        $totalRecords = Invoice::query();
        $totalRecords->whereDate('created_at', '>=', $range_from)
            ->whereDate('created_at', '<=', $range_to);

        if($request->invoice_status !=''){
            if($request->invoice_status == 'Убытки'){
                $totalRecords->whereNotNull('losess');
            }
            else {
                $totalRecords->where('status',$request->invoice_status);
            }

        }

        $totalRecords = $totalRecords->filter($filter)->count();

        if($searchValue != ''){
            $withFilter = Invoice::query();
            $withFilter->orWhere(function ($query) use ($searchValue) {
                    $query->where('invoices.amount', 'like', '%' . $searchValue . '%')
                    ->orWhere('invoices.id', $searchValue)
                    ->orWhere('invoices.amount_actual', 'like', '%' . $searchValue . '%')
                    ->orWhere('invoices.amount_paid', 'like', '%' . $searchValue . '%')
                    ->orWhere('invoices.amount_in_currency', 'like', '%' . $searchValue . '%')
                    ->orWhere('invoices.amount_in_currency_actual', 'like', '%' . $searchValue . '%')
                    ->orWhere('invoices.director_comment', 'like', '%' . $searchValue . '%')
                    ->orWhere('invoices.manager_comment', 'like', '%' . $searchValue . '%')
                    ->orWhere('invoices.accountant_comment', 'like', '%' . $searchValue . '%')
                    ->orWhere('invoices.additional_info', 'like', '%' . $searchValue . '%')
                    ->orWhere('invoices.status', 'like', '%' . $searchValue . '%')
                    ->orWhere('invoices.agree_1', 'like', '%' . $searchValue . '%')
                    ->orWhere('invoices.agree_2', 'like', '%' . $searchValue . '%')
                    ->orWhere('invoices.created_at', 'like', '%' . $searchValue . '%')
                    ->orWhereHas('client', function ($q) use($searchValue)
                    {
                        $q->where('name', 'like', '%' . $searchValue . '%');
                    })
                    ->orWhereHas('supplier', function ($q) use($searchValue)
                    {
                        $q->where('name', 'like', '%' . $searchValue . '%');
                    })
                    ->orWhereHas('project', function ($q) use($searchValue)
                    {
                        $q->where('name', 'like', '%' . $searchValue . '%');
                    });
                });

            if($request->invoice_status !=''){
                $withFilter->where('status', $request->invoice_status);
            }

            $withFilter->filter($filter)
                ->whereDate('created_at', '>=', $range_from)
                ->whereDate('created_at', '<=', $range_to);

            $totalRecordswithFilter = $withFilter->count();

            $withFilter->skip($start)->take($rowperpage);
            $records = $withFilter->get();

        }
        else {
            $totalRecordswithFilter = Invoice::query();
            $totalRecordswithFilter->whereDate('created_at', '>=', $range_from)
                ->whereDate('created_at', '<=', $range_to);
            if($request->invoice_status !=''){
                $totalRecordswithFilter->where('status',$request->invoice_status);
            }
            $totalRecordswithFilter = $totalRecordswithFilter->filter($filter)->count();

            $records = Invoice::query();
            $records = $records->whereDate('created_at', '>=', $range_from)
                ->whereDate('created_at', '<=', $range_to)
                ->orderBy($columnName, $columnSortOrder)
                ->select('invoices.*');

            if($request->invoice_status !=''){
                $records->where('status',$request->invoice_status);
            }

            $records = $records->filter($filter)
                ->skip($start)
                ->take($rowperpage)
                ->get();

        }

        $data_arr = array();

        $sno = $start + 1;

        foreach ($records as $invoice) {

            $class = $this->giveInvoiceTableColClass($invoice);

            $id = $invoice->id;

            $info = view('invoice.table.info', [
                'invoice' => $invoice
            ])->render();

            $kontragent = view('invoice.table.kontragent', [
                'invoice' => $invoice
            ])->render();

            $amount = view('invoice.table.amount', [
                'invoice' => $invoice
            ])->render();

            $paid = view('invoice.table.paid', [
                'invoice' => $invoice
            ])->render();

            $status = view('invoice.table.status', [
                'invoice' => $invoice
            ])->render();

            $actions = view('invoice.table.actions', [
                'invoice' => $invoice
            ])->render();

            $data_arr[] = array(
                "id" => $id,
                "class" => $class,
                "direction" => $info,
                "project_id" => $kontragent,
                "amount" => $amount,
                "amount_paid" => $paid,
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
