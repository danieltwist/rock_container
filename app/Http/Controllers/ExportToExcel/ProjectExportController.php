<?php

namespace App\Http\Controllers\ExportToExcel;

use App\Filters\ProjectFilter;
use App\Http\Controllers\Controller;
use App\Http\Traits\FinanceTrait;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Jenssegers\Date\Date;

class ProjectExportController extends Controller
{
    use FinanceTrait;

    public function exportProject($id)
    {
        $project = Project::find($id);
        $invoices = Invoice::where('project_id', $id)->get();

        $out_array = [];
        $in_array = [];
        $total_in = 0;
        $total_out = 0;

        foreach ($invoices as $invoice){
            if($invoice->client_id != '') {
                optional($invoice->client)->short != ''
                    ? $company_name = optional($invoice->client)->short
                    : $company_name = optional($invoice->client)->name;
            }
            else {
                optional($invoice->supplier)->short != ''
                    ? $company_name = optional($invoice->supplier)->short
                    : $company_name = optional($invoice->supplier)->name;
            }
            $amount = $this->getInvoiceAmountForReport($invoice)['planned_amount'];
            if ($invoice->direction == 'Расход'){
                $in_array [] = [
                    'company' => $company_name,
                    'id' => $invoice->id,
                    'details' => $invoice->additional_info,
                    'amount' => $amount,
                    'status' => $invoice->status
                ];
                $total_in += $amount;
            }

            if ($invoice->direction == 'Доход'){
                $out_array [] = [
                    'company' => $company_name,
                    'id' => $invoice->id,
                    'details' => $invoice->additional_info,
                    'amount' => $amount,
                    'status' => $invoice->status
                ];
                $total_out += $amount;
            }
        }

        $total = $total_out - $total_in;

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load("storage/templates/project_new_template.xlsx");
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('C2', $project->created_at->format('d.m.Y'))
            ->setCellValue('C1', $project->name)
            ->setCellValue('E1', $project->client->name)
            ->setCellValue('E2', $project->from.' - '.$project->to)
            ->setCellValue('E3', $total)
            ->setCellValue('E6', $total_out)
            ->setCellValue('C3', $project->additional_info);

        !empty($out_array) ? $rows = count($out_array) : $rows = 0;

        $i = 7;
        if($rows != 0){
            $sheet->insertNewRowBefore(8, $rows);
            foreach ($out_array as $string){
                $sheet->fromArray([$string], NULL, 'B'.$i);
                $i++;
            }
        }

        $k = $i+3;

        !empty($in_array) ? $rows = count($in_array) : $rows = 0;
        if($rows != 0) {
            $sheet->insertNewRowBefore($i+4, $rows);
            foreach ($in_array as $string){
                $sheet->fromArray([$string], NULL, 'B'.$k);
                $k++;
            }
            $sheet->setCellValue('E'.($i+2), $total_in);
        }

        if ($project->active == '1') {
            $folder = 'public/Проекты/Активные проекты/'.$project["name"].'/';
            $path = 'public/Проекты/Активные проекты/'.$project["name"].'/'.config('app.prefix_view').$project["name"].'_выгрузка.xlsx';
            $savepath = 'storage/Проекты/Активные проекты/'.$project["name"].'/';
        }
        else {
            $folder = 'public/Проекты/Завершенные проекты/'.$project["name"].'/';
            $path = 'public/Проекты/Завершенные проекты/'.$project["name"].'/'.config('app.prefix_view').$project["name"].'_выгрузка.xlsx';
            $savepath = 'storage/Проекты/Завершенные проекты/'.$project["name"].'/';
        }
        Storage::makeDirectory($folder);

        $filename = config('app.prefix_view').$project["name"].'_выгрузка.xlsx';

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
        $writer->save($savepath.$filename);

        return Storage::download($path);

    }

    public function exportProjectsList($projects, $parameters, $range_text)
    {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load("storage/templates/projects_list_with_parameters.xlsx");
        $sheet = $spreadsheet->getActiveSheet();
        setlocale(LC_TIME, 'ru_RU');

        $sheet->setCellValue('A1', config('app.company_name'));
        $sheet->setCellValue('B2', $range_text);
        $sheet->setCellValue('B3', $parameters['sorting_type']);

        $i=6;

        $price = 0;
        $cost = 0;
        $profit = 0;
        $containers_count = 0;
        $income_paid = 0;
        $income_unpaid = 0;
        $outcome_paid = 0;
        $outcome_unpaid = 0;
        $exchange_difference = 0;

        foreach ($projects as $project){
            $project->finance = $this->getProjectFinanceForReport($project->id);
            $start_date = new Date($project->created_at);
            $start_month = $start_date->format('F');

            if(!is_null($project->finished_at)){
                $finish_date = new Date($project->finished_at);
                $finish_month = $finish_date->format('F');
                $finish = explode(' ', $project->finished_at);
            }
            else {
                $finish_month = '';
                $finish = explode(' ', '   ');
            }

            if(!is_null($project->paid_at)){
                $paid_date = new Date($project->paid_at);
                $paid_month = $paid_date->format('F');
                $paid = explode(' ', $project->paid_at);
            }
            else {
                $paid_month = '';
                $paid = explode(' ', '   ');
            }

            optional($project->client)->short != ''
                ? $company = optional($project->client)->short
                : $company = optional($project->client)->name;
            $company_link = 'client/'.$project->client_id;

            $this_project_exchange_difference = $this->getProjectInvoiceDifference($project);

            $start = explode(' ', $project->created_at);

            $sheet->setCellValue('A'.$i, $project->id);
            $sheet->setCellValue('B'.$i, $project->name);
            $sheet->getCell('B'.$i)->getHyperlink()->setUrl(config('app.url').'project/'.$project->id);
            $sheet->setCellValue('C'.$i, $project->from);
            $sheet->setCellValue('D'.$i, $project->pogranperehod);
            $sheet->setCellValue('E'.$i, ucwords($start_month));
            $sheet->setCellValue('F'.$i, ucwords($finish_month));
            $sheet->setCellValue('G'.$i, ucwords($paid_month));

            $sheet->setCellValue('H'.$i, optional($project->logist)->name);
            if(!is_null(optional($project->logist)->name))
                $sheet->getCell('H'.$i)->getHyperlink()->setUrl(config('app.url').'user/'.$project->logist_id.'/statistic');

            $sheet->setCellValue('I'.$i, optional($project->manager)->name);
            if(!is_null(optional($project->manager)->name))
                $sheet->getCell('I'.$i)->getHyperlink()->setUrl(config('app.url').'user/'.$project->manager_id.'/statistic');

            $sheet->setCellValue('J'.$i, $start[0] != '' ? \Carbon\Carbon::parse($start[0])->format('d.m.Y') : '');
            $sheet->setCellValue('K'.$i, $finish[0] != '' ? \Carbon\Carbon::parse($finish[0])->format('d.m.Y') : '');
            $sheet->setCellValue('L'.$i, $paid[0] != '' ? \Carbon\Carbon::parse($paid[0])->format('d.m.Y') : '');

            $sheet->setCellValue('M'.$i, $company);
            if($company != '')
                $sheet->getCell('M'.$i)->getHyperlink()->setUrl(config('app.url').'client/'.$project->client_id);

            $sheet->setCellValue('N'.$i, $company);
            if($company != '')
                $sheet->getCell('N'.$i)->getHyperlink()->setUrl(config('app.url').'client/'.$project->client_id);

            $sheet->setCellValue('O'.$i, $project->freight_amount);
            $sheet->setCellValue('P'.$i, $project->finance['price']);
            $sheet->setCellValue('Q'.$i, $project->finance['income_paid']);
            $sheet->setCellValue('R'.$i, $project->finance['income_unpaid']);
            $sheet->setCellValue('S'.$i, $project->finance['cost']);
            $sheet->setCellValue('T'.$i, $project->finance['outcome_paid']);
            $sheet->setCellValue('U'.$i, $project->finance['outcome_unpaid']);
            $sheet->setCellValue('V'.$i, $project->finance['profit']);
            $sheet->setCellValue('W'.$i, $this_project_exchange_difference);

            if($project->finance['profit']<0){
                $sheet->getStyle('V'.$i)
                    ->getFont()
                    ->getColor()
                    ->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
            }
            $sheet->setCellValue('X'.$i, $project->additional_info);

            $price += $project->finance['price'];
            $cost += $project->finance['cost'];
            $profit += $project->finance['profit'];
            $income_paid += $project->finance['income_paid'];
            $income_unpaid += $project->finance['income_unpaid'];
            $outcome_paid += $project->finance['outcome_paid'];
            $outcome_unpaid += $project->finance['outcome_unpaid'];

            $containers_count += $project->expense->amount;
            $exchange_difference += $this_project_exchange_difference;

            $i++;
        }

        $sheet->setCellValue('P'.$i, 'Сумма');
        $sheet->setCellValue('S'.$i, 'Сумма');
        $sheet->setCellValue('V'.$i, 'Сумма');

        $sheet->getStyle('P'.$i.':X'.$i)->getFont()->setBold(true);
        $i++;

        $sheet->setCellValue('N'.$i,'ИТОГО');
        $sheet->setCellValue('O'.$i , $containers_count);
        $sheet->setCellValue('P'.$i , $price);
        $sheet->setCellValue('Q'.$i , $income_paid);
        $sheet->setCellValue('R'.$i , $income_unpaid);
        $sheet->setCellValue('S'.$i , $cost);
        $sheet->setCellValue('T'.$i , $outcome_paid);
        $sheet->setCellValue('U'.$i , $outcome_unpaid);
        $sheet->setCellValue('V'.$i , $profit);
        $sheet->setCellValue('W'.$i , $exchange_difference);


        $sheet->setCellValue('O4' , $containers_count);
        $sheet->setCellValue('P4' , $price);
        $sheet->setCellValue('Q4' , $income_paid);
        $sheet->setCellValue('R4' , $income_unpaid);
        $sheet->setCellValue('S4' , $cost);
        $sheet->setCellValue('T4' , $outcome_paid);
        $sheet->setCellValue('U4' , $outcome_unpaid);
        $sheet->setCellValue('V4' , $profit);
        $sheet->setCellValue('W4' , $exchange_difference);

        $sheet->getStyle('N4:X4')->getFont()->setBold(true);

        $sheet->getStyle('N'.$i.':X'.$i)->getFont()->setBold(true);

        if($profit<0){
            $sheet->getStyle('V'.$i)
                ->getFont()
                ->getColor()
                ->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
        }

        $filename = config('app.prefix_view').date('Y-m-d').'_'.$parameters['filename'].'.xlsx';

        if(!File::isDirectory('public/Проекты выгрузка')) Storage::makeDirectory('public/Проекты выгрузка');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
        $writer->save('storage/Проекты выгрузка/'.$filename);

        return 'public/Проекты выгрузка/'.$filename;

    }

    public function exportProjectsListWithFilter(Request $request, ProjectFilter $filter)
    {
        if ($request->data_range != '') {
            $range = explode(' - ', $request->data_range);
            $range[0] = \Carbon\Carbon::parse($range[0])->format('Y-m-d');
            $range[1]= \Carbon\Carbon::parse($range[1])->format('Y-m-d');
        }
        else {
            $range = explode(' - ', '2000-01-01 - 3000-01-01');
        }

        if(in_array($request->filter, ['finished', 'paid', 'finished_this_month', 'done_unpaid'])){
            $projects = Project::filter($filter)
                ->whereDate('finished_at', '>=', $range[0])
                ->whereDate('finished_at', '<=', $range[1])
                ->whereNull('remove_from_stat')
                ->get();

            if($request->filter == 'finished_this_month'){
                $range_text = 'Завершены в этом месяце';
            }
            else {
                $request->data_range != '' ? $range_text = 'Завершены с '.$range[0].' по '.$range[1] : $range_text = 'Все';
            }
        }
        elseif($request->filter == 'finished_paid_date'){
            $projects = Project::filter($filter)
                ->whereDate('paid_at', '>=', $range[0])
                ->whereDate('paid_at', '<=', $range[1])
                ->whereNull('remove_from_stat')
                ->get();

            $request->data_range != '' ? $range_text = 'Оплачены с '.$range[0].' по '.$range[1] : $range_text = 'Все';

        }
        else {
            $projects = Project::filter($filter)
                ->whereDate('created_at', '>=', $range[0])
                ->whereDate('created_at', '<=', $range[1])
                ->whereNull('remove_from_stat')
                ->get();

            $request->data_range != '' ? $range_text = 'Созданы с '.$range[0].' по '.$range[1] : $range_text = 'Все';
        }

        return Storage::download($this->exportProjectsList($projects, unserialize($request->parameters), $range_text));

    }

    public function exportCounterpartyProjectsList(Request $request)
    {
        $projects = [];

        if($request->type == 'client'){
            $all_projects = Project::where('status','<>','Черновик')
                ->whereNull('remove_from_stat')
                ->get();

            foreach ($all_projects as $project){
                $project->additional_clients != '' ? $additional_clients = unserialize($project->additional_clients) : $additional_clients = false;
                if($additional_clients){
                    if (in_array($request->client_id, $additional_clients)){
                        $projects [] = $project;
                    }
                }
                if($request->client_id == $project->client_id){
                    $projects [] = $project;
                }
            }

        }
        else {
            $projects_id = Invoice::select('project_id')->where('supplier_id', $request->supplier_id)->groupBy('project_id')->get()->toArray();

            foreach ($projects_id as $item){
                $projects [] = $item['project_id'];
            }

            $projects = Project::whereIn('id', $projects_id)
                ->whereNull('remove_from_stat')
                ->get();
        }

        $request->data_range != ''
            ? $range_text = 'Созданы с '.explode(' - ',$request->data_range)[0].' по '.explode(' - ',$request->data_range)[1]
            : $range_text = 'Все';

        return Storage::download($this->exportProjectsList($projects, unserialize($request->parameters), $range_text));

    }
}
