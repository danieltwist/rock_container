<?php

namespace App\Http\Controllers\ExportToExcel;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Filters\InvoiceFilter;

class InvoiceExportController extends Controller
{

    public function exportToExcel($filename, $invoices){

        $array = [];

        foreach ($invoices as $invoice){

            $project = optional($invoice->project)->name;

            $company = '';

            if($invoice->client_id !='') {
                optional($invoice->client)->short != ''
                    ? $company = optional($invoice->client)->short
                    : $company = optional($invoice->client)->name;
                $company_link = 'client/'.$invoice->client_id;
            }

            if($invoice->supplier_id !='') {
                optional($invoice->supplier)->short != ''
                    ? $company = optional($invoice->supplier)->short
                    : $company = optional($invoice->supplier)->name;
                $company_link = 'supplier/'.$invoice->supplier_id;
            }

            $array [] = [
                'id' => $invoice->id,
                'direction' => $invoice->direction,
                'project' =>$project,
                'amount' => $invoice->amount,
                'amount_income_date' => $invoice->amount_income_date,
                'amount_paid' => $invoice->amount_paid,
                'company' => $company,
                'status' => $invoice->status
            ];

            $url [] = [
                'invoice' => 'invoice/'.$invoice->id,
                'project' => 'project/'.$invoice->project_id,
                'company' => $company_link
            ];

        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load("storage/templates/invoices_export_template.xlsx");
        $sheet = $spreadsheet->getActiveSheet();

        $i = 2;
        $k = 0;
        //dd($array);
        foreach ($array as $string){
            $sheet->fromArray([$string], NULL, 'A'.$i);
            $sheet->getCell('A'.$i)->getHyperlink()->setUrl(config('app.url').$url[$k]['invoice']);
            $sheet->getCell('C'.$i)->getHyperlink()->setUrl(config('app.url').$url[$k]['project']);
            $sheet->getCell('G'.$i)->getHyperlink()->setUrl(config('app.url').$url[$k]['company']);
            $i++;
            $k++;
        }

        $folder = 'public/Счета выгрузка/';
        $path = 'public/Счета выгрузка/'.$filename;
        $savepath = 'storage/Счета выгрузка/';

        Storage::makeDirectory($folder);

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
        $writer->save($savepath.$filename);

        return $path;

    }

    public function exportToExcelNewTemplate($filename, $sorting_type, $dates, $invoices){

        $array = [];

        foreach ($invoices as $invoice){

            $project = optional($invoice->project)->name;

            $company = '';

            if($invoice->client_id !='') {
                optional($invoice->client)->short != ''
                    ? $company = optional($invoice->client)->short
                    : $company = optional($invoice->client)->name;
                $company_link = 'client/'.$invoice->client_id;
            }

            if($invoice->supplier_id !='') {
                optional($invoice->supplier)->short != ''
                    ? $company = optional($invoice->supplier)->short
                    : $company = optional($invoice->supplier)->name;
                $company_link = 'supplier/'.$invoice->supplier_id;
            }

            if($invoice->currency == 'RUB') {
                if(!is_null($invoice->amount_actual)){
                    $amount_in_currency = $invoice->amount_actual;
                }
                else
                {
                    $amount_in_currency = $invoice->amount;
                }

                $amount_paid_in_currency = (float)$invoice->amount_income_date;

            }
            else{
                if(!is_null($invoice->amount_in_currency_actual)){
                    $amount_in_currency = $invoice->amount_in_currency_actual;
                }
                else {
                    $amount_in_currency = $invoice->amount_in_currency;
                }

                !is_null($invoice->amount_in_currency_income_date)
                    ? $amount_paid_in_currency = (float)$invoice->amount_in_currency_income_date
                    : $amount_paid_in_currency = 0;

            }

            !is_null($invoice->amount_actual)
                ? $amount = $invoice->amount_actual
                : $amount = $invoice->amount;

            !is_null($invoice->amount_income_date)
                ? $amount_paid = (float)$invoice->amount_income_date
                : $amount_paid = 0;

            $array [] = [
                'date' => explode(' ', $invoice->created_at)[0],
                'direction' => $invoice->direction,
                'id' => $invoice->id,
                'project' =>$project,
                'company' => $company,
                'deadline' => $invoice->deadline,
                'amount_in_currency' => $amount_in_currency,
                'amount_paid_in_currency' => $amount_paid_in_currency,
                'amount_balance_in_currency' => (float)$amount_in_currency - $amount_paid_in_currency,
                'currency' => $invoice->currency,
                'amount' => $amount,
                'amount_paid' => $amount_paid,
                'amount_balance' => (float)$amount - $amount_paid,
                'status' => $invoice->status,
                'info' => str_replace('=', 'символ равно', $invoice->additional_info),
            ];

            $url [] = [
                'invoice' => 'invoice/'.$invoice->id,
                'project' => 'project/'.$invoice->project_id,
                'company' => $company_link
            ];

        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load("storage/templates/invoice_export_with_parameters_template.xlsx");
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('B2', $dates);
        $sheet->setCellValue('B3', $sorting_type);

        $sheet->getStyle('A4:O4')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('c6efce');

        $i = 5;
        $k = 0;

        $total_amount = 0;
        $total_amount_paid = 0;
        $total_amount_balance = 0;

        foreach ($array as $string){
            $sheet->fromArray([$string], NULL, 'A'.$i);

            $sheet->getCell('C'.$i)->getHyperlink()->setUrl(config('app.url').$url[$k]['invoice']);
            $sheet->getCell('D'.$i)->getHyperlink()->setUrl(config('app.url').$url[$k]['project']);
            $sheet->getCell('E'.$i)->getHyperlink()->setUrl(config('app.url').$url[$k]['company']);

            $total_amount += $string['amount'];
            $total_amount_paid += $string['amount_paid'];
            $total_amount_balance += $string['amount_balance'];

            $i++;
            $k++;
        }

        $sheet->setCellValue('A'.$i, 'ИТОГО');
        $sheet->setCellValue('K'.$i, $total_amount);
        $sheet->setCellValue('L'.$i, $total_amount_paid);
        $sheet->setCellValue('M'.$i, $total_amount_balance);

        $sheet->getStyle('A'.$i.':O'.$i)
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('c6efce');

        $sheet->getStyle('A'.$i.':N'.$i)
            ->getFont()
            ->setBold(true);

        $sheet->getStyle('K'.$i.':N'.$i)
            ->getNumberFormat()
            ->setFormatCode('#,##0.00_-"₽"');

        $folder = 'public/Счета выгрузка/';
        $path = 'public/Счета выгрузка/'.$filename;
        $savepath = 'storage/Счета выгрузка/';

        Storage::makeDirectory($folder);

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
        $writer->save($savepath.$filename);

        return $path;

    }

    public function exportLosses(Request $request)
    {
        if ($request->filter == 'losses'){
            $projects = Project::where('status','Завершен')
                ->where('paid', 'Оплачен')
                ->pluck('id')
                ->toArray();

            $invoices = Invoice::where('direction','Расход')
                ->whereNotNull('losses_confirmed')
                ->whereIn('project_id', $projects)
                ->get();
        }
        else {
            $projects = \App\Models\Project::where('status','Завершен')
                ->where('paid', 'Оплачен')
                ->pluck('id')
                ->toArray();

            $invoices = Invoice::where('direction','Расход')
                ->whereNotNull('losses_amount')
                ->whereNull('losses_confirmed')
                ->whereIn('project_id', $projects)
                ->get();
        }

        $parameters = unserialize($request->parameters);
        $filename = preg_replace ("/[^a-zA-ZА-Яа-я0-9.,_:;?!\s]/u","",date('Y-m-d').'_'.$parameters['filename'].'_выгрузка.xlsx');

        $array = [];

        foreach ($invoices as $invoice){

            $project = optional($invoice->project)->name;

            $company = '';

            if($invoice->client_id !='') {
                optional($invoice->client)->short != ''
                    ? $company = optional($invoice->client)->short
                    : $company = optional($invoice->client)->name;
                $company_link = 'client/'.$invoice->client_id;
            }

            if($invoice->supplier_id !='') {
                optional($invoice->supplier)->short != ''
                    ? $company = optional($invoice->supplier)->short
                    : $company = optional($invoice->supplier)->name;
                $company_link = 'supplier/'.$invoice->supplier_id;
            }

            if($invoice->currency == 'RUB') {
                if(!is_null($invoice->amount_actual)){
                    $amount_in_currency = $invoice->amount_actual;
                }
                else
                {
                    $amount_in_currency = $invoice->amount;
                }

                $amount_paid_in_currency = (float)$invoice->amount_income_date;

            }
            else{
                if(!is_null($invoice->amount_in_currency_actual)){
                    $amount_in_currency = $invoice->amount_in_currency_actual;
                }
                else {
                    $amount_in_currency = $invoice->amount_in_currency;
                }

                !is_null($invoice->amount_in_currency_income_date)
                    ? $amount_paid_in_currency = (float)$invoice->amount_in_currency_income_date
                    : $amount_paid_in_currency = 0;

            }

            !is_null($invoice->amount_actual)
                ? $amount = $invoice->amount_actual
                : $amount = $invoice->amount;

            !is_null($invoice->amount_income_date)
                ? $amount_paid = (float)$invoice->amount_income_date
                : $amount_paid = 0;

            $array [] = [
                'date' => explode(' ', $invoice->created_at)[0],
                'direction' => $invoice->direction,
                'id' => $invoice->id,
                'project' =>$project,
                'company' => $company,
                'deadline' => $invoice->deadline,
                'amount_in_currency' => $amount_in_currency,
                'amount_paid_in_currency' => $amount_paid_in_currency,
                'amount_balance_in_currency' => (float)$amount_in_currency - $amount_paid_in_currency,
                'currency' => $invoice->currency,
                'amount' => $amount,
                'amount_paid' => $amount_paid,
                'losses_amount' => abs($invoice->losses_amount),
                'status' => $invoice->status,
                'info' => str_replace('=', 'символ равно', $invoice->additional_info),
            ];

            $url [] = [
                'invoice' => 'invoice/'.$invoice->id,
                'project' => 'project/'.$invoice->project_id,
                'company' => $company_link
            ];

        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load("storage/templates/invoice_export_with_parameters_losses_template.xlsx");
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('B2', 'Все');
        $sheet->setCellValue('B3', $parameters['sorting_type']);

        $sheet->getStyle('A4:04')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('c6efce');

        $i = 5;
        $k = 0;

        $total_amount = 0;
        $total_amount_paid = 0;
        $total_amount_balance = 0;

        foreach ($array as $string){
            $sheet->fromArray([$string], NULL, 'A'.$i);

            $sheet->getCell('C'.$i)->getHyperlink()->setUrl(config('app.url').$url[$k]['invoice']);
            $sheet->getCell('D'.$i)->getHyperlink()->setUrl(config('app.url').$url[$k]['project']);
            $sheet->getCell('E'.$i)->getHyperlink()->setUrl(config('app.url').$url[$k]['company']);

            $total_amount += $string['amount'];
            $total_amount_paid += $string['amount_paid'];
            $total_amount_balance += $string['losses_amount'];

            $i++;
            $k++;
        }

        $sheet->setCellValue('A'.$i, 'ИТОГО');
        $sheet->setCellValue('K'.$i, $total_amount);
        $sheet->setCellValue('L'.$i, $total_amount_paid);
        $sheet->setCellValue('M'.$i, $total_amount_balance);

        $sheet->getStyle('A'.$i.':O'.$i)
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('c6efce');

        $sheet->getStyle('A'.$i.':N'.$i)
            ->getFont()
            ->setBold(true);

        $sheet->getStyle('K'.$i.':N'.$i)
            ->getNumberFormat()
            ->setFormatCode('#,##0.00_-"₽"');

        $folder = 'public/Счета выгрузка/';
        $path = 'public/Счета выгрузка/'.$filename;
        $savepath = 'storage/Счета выгрузка/';

        Storage::makeDirectory($folder);

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
        $writer->save($savepath.$filename);

        return Storage::download($path);

    }

    public function exportInvoicesWithFilter(InvoiceFilter $filter, Request $request)
    {
        if (in_array($request->data_range, ['','all','Все'])) {
            $range = '2000-01-01 - 3000-01-01';
            $range_text = 'Все';
        }
        else {
            $range = $request->data_range;
            $range_text = $request->data_range;
        }

        $range = explode(' - ', $range);

        $parameters = unserialize($request->parameters);
        $filename = preg_replace ("/[^a-zA-ZА-Яа-я0-9.,_:;?!\s]/u","",date('Y-m-d').'_'.$parameters['filename'].'_выгрузка.xlsx');

        $sorting_type = $parameters['sorting_type'];

        if($request->status != ''){
            $sorting_type .= ' со статусом '.$request->status;
        }

        $invoices = Invoice::filter($filter)
            ->whereDate('created_at', '>=', $range[0])
            ->whereDate('created_at', '<=', $range[1])
            ->get();

        return Storage::download($this->exportToExcelNewTemplate($filename, $sorting_type, $range_text, $invoices));

    }

    public function exportProjectReportInvoices(InvoiceFilter $filter, Request $request){

        if (in_array($request->data_range, ['','all','Все'])) {
            $range = '2000-01-01 - 3000-01-01';
            $range_text = 'Все';
        }
        else {
            $range = $request->data_range;
            $range_text = $request->data_range;
        }

        $range = explode(' - ', $range);

        if(!is_null($request->project_filter_array)){

            $project_filter = unserialize($request->project_filter_array);
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

        $invoices = Invoice::filter($filter)->whereIn('project_id', $projects)->get();

        $parameters = unserialize($request->parameters);
        $filename = preg_replace ("/[^a-zA-ZА-Яа-я0-9.,_:;?!\s]/u","",date('Y-m-d').'_'.$parameters['filename'].'.xlsx');

        return Storage::download($this->exportToExcelNewTemplate($filename, $parameters['sorting_type'], $range_text, $invoices));

    }

}
