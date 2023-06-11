<?php

namespace App\Http\Controllers\Datatables;

use App\Http\Controllers\Controller;
use App\Models\BankAccountBalance;
use App\Models\BankAccountPayment;
use Illuminate\Http\Request;

class BankAccountsTablesController extends Controller
{
    public function bankAccountsBalance(){
        return view('1c.bank_accounts_balances_table');
    }

    public function bankAccountsPayments(){
        return view('1c.bank_accounts_payments_table');
    }

    public function getBankAccountsBalanceTable(Request $request){
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
        $totalRecords = BankAccountBalance::all();

        $totalRecords = $totalRecords->count();

        if($searchValue != ''){
            $records = BankAccountBalance::orderBy($columnName, $columnSortOrder);
            $records = $records->where(function ($query) use ($searchValue) {
                foreach (['id', 'created_at'] as $item){
                    $query->orWhere($item, 'like', '%' . $searchValue . '%');
                }
            });

            $totalRecordswithFilter = $records->count();

            $records = $records->select('bank_account_balances.*')
                ->skip($start)
                ->take($rowperpage)
                ->get();
        }
        else {
            $records = BankAccountBalance::orderBy($columnName, $columnSortOrder);

            $totalRecordswithFilter = $records->count();

            $records = $records->select('bank_account_balances.*')
                ->skip($start)
                ->take($rowperpage)
                ->get();
        }

        $data_arr = array();

        $sno = $start + 1;

        foreach ($records as $balance) {

            $info = [];

            foreach ($balance->info as $company_balances){
                $info [] = $company_balances['company']. ', '. $company_balances['account_number'].': '.number_format($company_balances['amount'], 2, '.', ' ');
            }

            $data_arr[] = array(
                "id" => $balance->id,
                "date" => $balance->created_at->format('d.m.Y H:i'),
                "info" => implode('<br>', $info),
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

    public function getBankAccountsPaymentsTable(Request $request){

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
        $totalRecords = BankAccountPayment::all();

        $totalRecords = $totalRecords->count();

        if($searchValue != ''){
            $records = BankAccountPayment::orderBy($columnName, $columnSortOrder);
            $records = $records->where(function ($query) use ($searchValue) {
                foreach (['id', 'company', 'counterparty', 'amount', 'payment_type', 'created_at'] as $item){
                    $query->orWhere($item, 'like', '%' . $searchValue . '%');
                }
            });

            $totalRecordswithFilter = $records->count();

            $records = $records->select('bank_account_payments.*')
                ->skip($start)
                ->take($rowperpage)
                ->get();
        }
        else {
            $records = BankAccountPayment::orderBy($columnName, $columnSortOrder);

            $totalRecordswithFilter = $records->count();

            $records = $records->select('bank_account_payments.*')
                ->skip($start)
                ->take($rowperpage)
                ->get();
        }

        $data_arr = array();

        $sno = $start + 1;

        foreach ($records as $payment) {

            $data_arr[] = array(
                "id" => $payment->id,
                "date" => $payment->created_at->format('d.m.Y H:i'),
                "payment_type" => $payment->payment_type,
                "company" => $payment->company,
                "counterparty" => $payment->counterparty,
                "amount" => number_format($payment->amount, 2, '.', ' '),
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
