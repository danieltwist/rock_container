<?php

namespace App\Http\Controllers\Report;

use App\Filters\InvoiceFilter;
use App\Filters\ProjectFilter;
use App\Http\Controllers\Controller;
use App\Http\Traits\FinanceTrait;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Supplier;
use Carbon\Carbon;
use http\Client\Curl\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Jenssegers\Date\Date;

class ReportController extends Controller
{

    use FinanceTrait;

    public function clientSupplierSummaryIndex(){

        return view('report.client-supplier_summary',[
            'clients' => Client::all(),
            'suppliers' => Supplier::all()
        ]);

    }

    public function clientSupplierSummaryLoad(Request $request){

        $client = Client::find($request->client_id);
        $supplier = Supplier::find($request->supplier_id);
        if(!is_null($request->datarange) || !in_array($request->datarange, ['Все','',null] )){
            $range = explode(' - ', $request->datarange);
            $datarange = $request->datarange;
        }
        else {
            $datarange = '2000-01-01 - 3000-01-01';
            $range = explode(' - ', '2000-01-01 - 3000-01-01');
        }

        $projects_list = DB::table('invoices')
            ->selectRaw('project_id')
            ->groupBy('project_id')
            ->where('client_id', $client->id)
            ->orWhere('supplier_id', $supplier->id)
            ->get();

        foreach ($projects_list as $id){
            $project = Project::find($id->project_id);

            if(!is_null($project)){
                if($project->created_at <= $range[1] && $project->created_at >= $range[0]){
                    $invoices = Invoice::where('project_id', $project->id)->where(function ($query) use($client, $supplier) {
                        $query
                            ->where('client_id', $client->id)
                            ->orWhere('supplier_id', $supplier->id);
                    })->get();

                    $income = 0;
                    $outcome = 0;
                    $income_rub = 0;
                    $income_usd = 0;
                    $income_cny = 0;
                    $outcome_rub = 0;
                    $outcome_usd = 0;
                    $outcome_cny = 0;
                    $income_rub_paid = 0;
                    $income_usd_paid = 0;
                    $income_cny_paid = 0;
                    $outcome_rub_paid = 0;
                    $outcome_usd_paid = 0;
                    $outcome_cny_paid = 0;


                    foreach ($invoices as $invoice){
                        if ($invoice->direction == 'Доход'){
                            if ($invoice->amount_sale_date != '') {
                                $income += $invoice->amount_sale_date;
                            }
                            elseif($invoice->amount_paid != ''){
                                $income += $invoice->amount_paid;
                            }
                            elseif($invoice->amount_income_date != ''){
                                $income += $invoice->amount_income_date;
                            }
                            elseif($invoice->amount != $invoice->amount_actual && $invoice->amount_actual !=''){
                                $income += $invoice->amount_actual;
                            }
                            else {
                                $income += $invoice->amount;
                            }
                        }

                        if ($invoice->direction == 'Расход'){
                            if ($invoice->amount_income_date != '') {
                                $outcome += $invoice->amount_income_date;
                            }
                            elseif($invoice->amount_paid != ''){
                                $outcome += $invoice->amount_paid;
                            }
                            elseif($invoice->amount != $invoice->amount_actual && $invoice->amount_actual !=''){
                                $outcome += $invoice->amount_actual;
                            }
                            else {
                                $outcome += $invoice->amount;
                            }
                        }


                        if($invoice->currency == 'RUB'){
                            if ($invoice->direction == 'Доход'){
                                if ($invoice->amount_sale_date != '') {
                                    $income_rub += $invoice->amount_sale_date;
                                }
                                elseif($invoice->amount_paid != ''){
                                    $income_rub += $invoice->amount_paid;
                                    $income_rub_paid += $invoice->amount_paid;
                                }
                                elseif($invoice->amount_income_date != ''){
                                    $income_rub += $invoice->amount_income_date;
                                }
                                elseif($invoice->amount != $invoice->amount_actual && $invoice->amount_actual !=''){
                                    $income += $invoice->amount_actual;
                                }
                                else {
                                    $income_rub += $invoice->amount;
                                }
                            }
                            if ($invoice->direction == 'Расход'){
                                if ($invoice->amount_income_date != '') {
                                    $outcome_rub += $invoice->amount_income_date;
                                }
                                elseif($invoice->amount_paid != ''){
                                    $outcome += $invoice->amount_paid;
                                    $outcome_rub_paid += $invoice->amount_paid;
                                }
                                elseif($invoice->amount != $invoice->amount_actual && $invoice->amount_actual !=''){
                                    $outcome += $invoice->amount_actual;
                                }
                                else {
                                    $outcome_rub += $invoice->amount;
                                }
                            }
                        }

                        if($invoice->currency == 'USD'){
                            if ($invoice->direction == 'Доход'){
                                if($invoice->amount_in_currency_income_date != ''){
                                    $income_usd += $invoice->amount_in_currency_income_date;
                                    $income_usd_paid += $invoice->amount_in_currency_income_date;
                                }
                                elseif($invoice->amount_in_currency != $invoice->amount_in_currency_actual && $invoice->amount_in_currency_actual !=''){
                                    $income_usd += $invoice->amount_in_currency_actual;
                                }
                                else {
                                    $income_usd += $invoice->amount_in_currency;
                                }
                            }
                            if ($invoice->direction == 'Расход'){
                                if ($invoice->amount_in_currency_income_date != '') {
                                    $outcome_usd += $invoice->amount_in_currency_income_date;
                                    $outcome_usd_paid += $invoice->amount_in_currency_income_date;
                                }
                                elseif($invoice->amount_in_currency != $invoice->amount_in_currency_actual && $invoice->amount_in_currency_actual !=''){
                                    $outcome_usd += $invoice->amount_in_currency_actual;
                                }
                                else {
                                    $outcome_usd += $invoice->amount_in_currency;
                                }
                            }
                        }

                        if($invoice->currency == 'CNY'){
                            if ($invoice->direction == 'Доход'){
                                if($invoice->amount_in_currency_income_date != ''){
                                    $income_cny += $invoice->amount_in_currency_income_date;
                                    $income_cny_paid += $invoice->amount_in_currency_income_date;
                                }
                                elseif($invoice->amount_in_currency != $invoice->amount_in_currency_actual && $invoice->amount_in_currency_actual !=''){
                                    $income_cny += $invoice->amount_in_currency_actual;
                                }
                                else {
                                    $income_cny += $invoice->amount_in_currency;
                                }
                            }
                            if ($invoice->direction == 'Расход'){
                                if ($invoice->amount_in_currency_income_date != '') {
                                    $outcome_cny += $invoice->amount_in_currency_income_date;
                                    $outcome_cny_paid += $invoice->amount_in_currency_income_date;
                                }
                                elseif($invoice->amount_in_currency != $invoice->amount_in_currency_actual && $invoice->amount_in_currency_actual !=''){
                                    $outcome_cny += $invoice->amount_in_currency_actual;
                                }
                                else {
                                    $outcome_cny += $invoice->amount_in_currency;
                                }
                            }
                        }
                    }

                    $project->invoices = $invoices;
                    $project->income = $income;
                    $project->outcome = $outcome;
                    $project->income_rub = $income_rub;
                    $project->income_usd = $income_usd;
                    $project->income_cny = $income_cny;
                    $project->outcome_rub = $outcome_rub;
                    $project->outcome_usd = $outcome_usd;
                    $project->outcome_cny = $outcome_cny;
                    $project->income_rub_paid = $income_rub_paid;
                    $project->income_usd_paid = $income_usd_paid;
                    $project->income_cny_paid = $income_cny_paid;
                    $project->outcome_rub_paid = $outcome_rub_paid;
                    $project->outcome_usd_paid = $outcome_usd_paid;
                    $project->outcome_cny_paid = $outcome_cny_paid;

                    $projects [] = $project;
                }
            }
        }

        $total_income = 0;
        $total_outcome = 0;
        $total_income_rub = 0;
        $total_income_usd = 0;
        $total_income_cny = 0;
        $total_outcome_rub = 0;
        $total_outcome_usd = 0;
        $total_outcome_cny = 0;
        $total_income_rub_paid = 0;
        $total_income_usd_paid = 0;
        $total_income_cny_paid = 0;
        $total_outcome_rub_paid = 0;
        $total_outcome_usd_paid = 0;
        $total_outcome_cny_paid = 0;

        foreach ($projects as $project){
            $total_income += $project->income;
            $total_outcome += $project->outcome;
            $total_income_rub += $project->income_rub;
            $total_income_usd += $project->income_usd;
            $total_income_cny += $project->income_cny;
            $total_outcome_rub += $project->outcome_rub;
            $total_outcome_usd += $project->outcome_usd;
            $total_outcome_cny += $project->outcome_cny;
            $total_income_rub_paid += $project->income_rub_paid;
            $total_income_usd_paid += $project->income_usd_paid;
            $total_income_cny_paid += $project->income_cny_paid;
            $total_outcome_rub_paid += $project->outcome_rub_paid;
            $total_outcome_usd_paid += $project->outcome_usd_paid;
            $total_outcome_cny_paid += $project->outcome_cny_paid;
        }

        $results['total_income'] = $total_income;
        $results['total_outcome'] = $total_outcome;
        $results['total_income_rub'] = $total_income_rub;
        $results['total_income_usd'] = $total_income_usd;
        $results['total_income_cny'] = $total_income_cny;
        $results['total_outcome_rub'] = $total_outcome_rub;
        $results['total_outcome_usd'] = $total_outcome_usd;
        $results['total_outcome_cny'] = $total_outcome_cny;
        $results['total_income_rub_paid'] = $total_income_rub_paid;
        $results['total_income_usd_paid'] = $total_income_usd_paid;
        $results['total_income_cny_paid'] = $total_income_cny_paid;
        $results['total_outcome_rub_paid'] = $total_outcome_rub_paid;
        $results['total_outcome_usd_paid'] = $total_outcome_usd_paid;
        $results['total_outcome_cny_paid'] = $total_outcome_cny_paid;

        $results['total_difference'] = $total_income - $total_outcome;
        $results['total_difference_rub'] = $total_income_rub - $total_outcome_rub;
        $results['total_difference_usd'] = $total_income_usd - $total_outcome_usd;
        $results['total_difference_cny'] = $total_income_cny - $total_outcome_cny;

        return [
            'ajax' => view('report.ajax.client-supplier_summary_results',[
                'projects' => $projects,
                'results' => $results,
                'client_id' => $request->client_id,
                'supplier_id' => $request->supplier_id,
                'datarange' => $datarange
            ])->render(),
            'bg-class' => 'bg-success',
            'from' => 'Система',
            'message' => __('report.loaded_successfully'),
            'projects' => $projects,
        ];
    }

    public function clientSupplierSummaryLoadExportToExcel(Request $request){
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load("storage/templates/client_supplier_balance.xlsx");
        $sheet = $spreadsheet->getActiveSheet();
        setlocale(LC_TIME, 'ru_RU');
        $i=4;

        foreach ($this->clientSupplierSummaryLoad($request)['projects'] as $project){
            $sheet->setCellValue('A'.$i, $project->name.' - по проекту');
            $sheet->setCellValue('B'.$i, $project->income_usd);
            $sheet->setCellValue('C'.$i, $project->outcome_usd);
            $sheet->setCellValue('D'.$i, (int)$project->income_usd-(int)$project->outcome_usd);
            $sheet->setCellValue('E'.$i, $project->income_cny);
            $sheet->setCellValue('F'.$i, $project->outcome_cny);
            $sheet->setCellValue('G'.$i, (int)$project->income_cny-(int)$project->outcome_cny);
            $sheet->setCellValue('H'.$i, $project->income_rub);
            $sheet->setCellValue('I'.$i, $project->outcome_rub);
            $sheet->setCellValue('J'.$i, (int)$project->income_rub-(int)$project->outcome_rub);
            $i++;
            $sheet->setCellValue('A'.$i, $project->name.' - по факту');
            $sheet->setCellValue('B'.$i, $project->income_usd_paid);
            $sheet->setCellValue('C'.$i, $project->outcome_usd_paid);
            $sheet->setCellValue('D'.$i, (int)$project->income_usd_paid-(int)$project->outcome_usd_paid);
            $sheet->setCellValue('E'.$i, $project->income_cny_paid);
            $sheet->setCellValue('F'.$i, $project->outcome_cny_paid);
            $sheet->setCellValue('G'.$i, (int)$project->income_cny_paid-(int)$project->outcome_cny_paid);
            $sheet->setCellValue('H'.$i, $project->income_rub_paid);
            $sheet->setCellValue('I'.$i, $project->outcome_rub_paid);
            $sheet->setCellValue('J'.$i, (int)$project->income_rub_paid-(int)$project->outcome_rub_paid);
            $i++;
            $i++;
        }
        $client = Client::find($request->client_id);

        $filename = $client->name.'_выгрузка_'.$request->datarange.'.xlsx';

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
        $writer->save('storage/Отчеты/'.$filename);

        return response()->json([
            'bg-class' => 'bg-success',
            'from' => 'Система',
            'message' => __('report.clientSupplierSummaryLoadExportToExcel_successfully'),
            'url' => Storage::url('public/Отчеты/'.$filename)
        ]);


    }

    public function getCredit(){
        $projects = Project::whereIn('status',['В работе', 'Завершен'])
            ->where('paid', 'Не оплачен')
            ->pluck('id')
            ->toArray();

        $invoices = Invoice::where('direction','Расход')
            ->whereIn('project_id', $projects)
            ->where('status','<>','Оплачен')
            ->get();

        $projects_count = $invoices->unique('project_id')->count();

        $total_amount = 0;

        foreach ($invoices as $invoice){
            $total_amount += $this->getInvoiceAmount($invoice) - $this->getInvoicePaidAmount($invoice);
        }

        return view('report.credit',[
            'invoices_count' => $invoices->count(),
            'projects_count' => $projects_count,
            'total_amount' => $total_amount
        ]);

    }

    public function getDebit(){
        $projects = Project::whereIn('status',['В работе', 'Завершен'])
            //->whereDate('planned_payment_date', '<=', date('Y-m-d'))
            ->where('paid', 'Не оплачен')
            ->pluck('id')
            ->toArray();

        $invoices = Invoice::where('direction','Доход')
            ->where('status','<>','Оплачен')
            ->whereIn('project_id', $projects)
            ->get();

        $projects_count = $invoices->unique('project_id')->count();

        $total_amount = 0;

        foreach ($invoices as $invoice){
            $total_amount += $this->getInvoiceAmount($invoice) - $this->getInvoicePaidAmount($invoice);
        }

        return view('report.debit',[
            'invoices_count' => $invoices->count(),
            'projects_count' => $projects_count,
            'total_amount' => $total_amount
        ]);

    }

    public function getPotentialLosses(){
        $projects = Project::where('status','Завершен')
            ->where('paid', 'Оплачен')
            ->pluck('id')
            ->toArray();

        $invoices = Invoice::where('direction','Расход')
            ->whereNotNull('losses_amount')
            ->whereNull('losses_confirmed')
            ->whereIn('project_id', $projects)
            ->get();

        $projects_list = $invoices->unique('project_id')->pluck('project_id')->toArray();

        $projects = Project::whereIn('id', $projects_list)->get();

        foreach ($projects as $project){
            $project->finance = $this->getProjectFinance($project->id);
        }

        $total_amount = 0;

        foreach ($invoices as $invoice){
            $total_amount -= $invoice->losses_amount;
        }

        return view('report.potential_losses',[
            'invoices_count' => $invoices->count(),
            'projects_count' => count($projects_list),
            'projects' => $projects,
            'total_amount' => $total_amount
        ]);

    }

    public function getLosses(){
        $projects = Project::where('status','Завершен')
            ->where('paid', 'Оплачен')
            ->pluck('id')
            ->toArray();

        $invoices = Invoice::where('direction','Расход')
            ->whereNotNull('losses_confirmed')
            ->whereIn('project_id', $projects)
            ->get();

        $projects_list = $invoices->unique('project_id')->pluck('project_id')->toArray();

        $projects = Project::whereIn('id', $projects_list)->get();

        foreach ($projects as $project){
            $project->finance = $this->getProjectFinance($project->id);
        }

        $total_amount = 0;

        foreach ($invoices as $invoice){
            $total_amount -= $invoice->losses_amount;
        }

        return view('report.losses',[
            'invoices_count' => $invoices->count(),
            'projects_count' => count($projects_list),
            'projects' => $projects,
            'total_amount' => $total_amount
        ]);

    }

    public function getReportProject(Request $request, ProjectFilter $filter){

        if($request->report_type == 'this_year'){
            $range = Carbon::now()->year.'-01-01 - '.Carbon::today()->format('Y-m-d');
        }
        if($request->report_type == 'last_year'){
            $last_year = Carbon::now()->subYears(1)->format('Y');
            $range = $last_year.'-01-01 - '.$last_year.'-12-31';
        }
        if($request->report_type == 'date_range'){
            if (in_array($request->datarange, ['','all','Все'])) {
                $range = '2000-01-01 - 3000-01-01';
            }
            else {
                $range = $request->datarange;
            }
        }

        $range = explode(' - ', $range);

        if(in_array($request->filter, ['finished', 'paid', 'finished_this_month', 'done_unpaid'])){
            $projects = Project::filter($filter)
                ->whereDate('finished_at', '>=', $range[0])
                ->whereDate('finished_at', '<=', $range[1])
                ->whereNull('remove_from_stat')
                ->get();
        }
        elseif($request->filter == 'finished_paid_date'){
            $projects = Project::filter($filter)
                ->whereDate('paid_at', '>=', $range[0])
                ->whereDate('paid_at', '<=', $range[1])
                ->whereNull('remove_from_stat')
                ->get();
        }
        else {
            $projects = Project::filter($filter)
                ->whereDate('created_at', '>=', $range[0])
                ->whereDate('created_at', '<=', $range[1])
                ->whereNull('remove_from_stat')
                ->get();
        }

        $cost = 0;
        $price = 0;
        $profit = 0;
        $id_array = [];

        foreach ($projects as $project) {
            $project->finance = $this->getProjectFinance($project->id);
            $cost += $project->finance['cost'];
            $price += $project->finance['price'];
            $profit += $project->finance['profit'];

            $id_array [] = $project->id;
        }

        $in_invoices = Invoice::where('direction', 'Расход')->whereIn('project_id', $id_array)->get();
        foreach ($in_invoices as $invoice){
            $this->invoiceGiveClass($invoice);
        }
        $out_invoices = Invoice::where('direction', 'Доход')->whereIn('project_id', $id_array)->get();
        foreach ($out_invoices as $invoice){
            $this->invoiceGiveClass($invoice);
        }

        $sorting_type_project = '';
        $sorting_type_invoice = '';

        switch ($request->filter){
            case 'finished':
            $sorting_type_project .= 'Завершенные проекты';
            $sorting_type_invoice .= ' по завершенным проектам';
                break;
            case 'finished_paid_date':
            $sorting_type_project .= 'Оплаченные проекты';
            $sorting_type_invoice .= ' по оплаченным проектам';
                break;
            case 'done_unpaid':
                $sorting_type_project .= 'Завершенные неоплаченные проекты';
                $sorting_type_invoice .= ' по завершенным неоплаченным проектам';
                break;
            case 'active':
                $sorting_type_project .= 'Проекты в работе';
                $sorting_type_invoice .= ' по проектам в работе';
                break;
            case 'all':
                $sorting_type_project .= 'Все проекты';
                $sorting_type_invoice .= ' по всем проектам';
                break;
        }

        if ($request->report_type != 'date_range'){
            switch ($request->report_type){
                case 'this_year':
                    $sorting_type_project .= ' за этот год';
                    $sorting_type_invoice .= ' за этот год';
                    break;
                case 'last_year':
                    $sorting_type_project .= ' за прошлый год';
                    $sorting_type_invoice .= ' за прошлый год';
                    break;
            }
        }

        if ($request->user_id != 'Все'){
            $user = \App\Models\User::find($request->user_id);
            $sorting_type_project .= ', созданные пользователем '.$user->name;
            $sorting_type_invoice .= ', созданные пользователем '.$user->name;
        }

        if ($request->manager_id != 'Все'){
            $manager = \App\Models\User::find($request->manager_id);
            $sorting_type_project .= ', менеджер пользователь '.$manager->name;
            $sorting_type_invoice .= ', созданные пользователем '.$user->name;
        }

        $sorting_type_invoice_out = 'Исходящие счета'.$sorting_type_invoice;
        $sorting_type_invoice_in = 'Входящие счета'.$sorting_type_invoice;

        return view('report.project', [
            'project_count' => $projects->count(),
            'cost' => $cost,
            'price' => $price,
            'profit' => $profit,
            'filter_type' => $request->filter,
            'range' => implode(' - ', $range),
            'user_id' => $request->user_id,
            'manager_id' => $request->manager_id,
            'sorting_type_invoice_in' => $sorting_type_invoice_in,
            'sorting_type_invoice_out' => $sorting_type_invoice_out,
            'sorting_type_project' => $sorting_type_project,
        ]);
    }

    public function ReportProject(){
        return view('report.project_choose_report_type',[
            'users' => \App\Models\User::all()
        ]);
    }

    public function getReportUserInvoices(Request $request, InvoiceFilter $filter){

        if($request->report_type == 'this_year'){
            $range = Carbon::now()->year.'-01-01 - '.Carbon::today()->format('Y-m-d');
            $range_text = 'текущий год';
        }
        if($request->report_type == 'last_year'){
            $last_year = Carbon::now()->subYears(1)->format('Y');
            $range = $last_year.'-01-01 - '.$last_year.'-12-31';
            $range_text = 'прошлый год';
        }
        if($request->report_type == 'date_range'){
            if (in_array($request->datarange, ['','all','Все'])) {
                $range = '2000-01-01 - 3000-01-01';
                $range_text = 'все время';
            }
            else {
                $range = $request->datarange;
                $range_text = $request->datarange;
            }
        }

        $range = explode(' - ', $range);

        $invoices = Invoice::filter($filter)
            ->whereDate('created_at', '>=', $range[0])
            ->whereDate('created_at', '<=', $range[1])
            ->get();

        $in_invoices_count = Invoice::filter($filter)
            ->where('direction', 'Расход')
            ->whereDate('created_at', '>=', $range[0])
            ->whereDate('created_at', '<=', $range[1])
            ->count();

        $out_invoices_count = Invoice::filter($filter)
            ->where('direction', 'Доход')
            ->whereDate('created_at', '>=', $range[0])
            ->whereDate('created_at', '<=', $range[1])
            ->count();

        $cost = 0;
        $price = 0;

        foreach ($invoices as $invoice){
            if($invoice->direction == 'Расход'){
                $cost += $this->getInvoiceAmount($invoice);
            }
            if($invoice->direction == 'Доход'){
                $price += $this->getInvoiceAmount($invoice);
            }

        }

        return view('report.user_invoices', [
            'invoices_count' => $invoices->count(),
            'cost' => $cost,
            'price' => $price,
            'in_invoices_count' => $in_invoices_count,
            'out_invoices_count' => $out_invoices_count,
            'range' => implode(' - ', $range),
            'range_text' => $range_text,
            'user' => $request->user,
            'sorting_type_invoice_in' => 'Входящие счета, созданные пользователем '.$request->user,
            'sorting_type_invoice_out' => 'Исходящие счета, созданные пользователем '.$request->user,
        ]);

    }

    public function ReportUserInvoices(){
        return view('report.user_invoices_choose',[
            'users' => \App\Models\User::all()
        ]);
    }
}
