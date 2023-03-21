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
        foreach ($invoices as $invoice){

            $invoice->supplier_id != '' ? $company_name = optional($invoice->supplier)->name : $company_name = optional($invoice->client)->name;

            if ($invoice->direction == 'Расход'){

                //$invoice->amount_income_date == '' ? $amount = $invoice->amount : $amount = $invoice->amount_income_date;

                $amount = $this->getInvoiceAmountForReport($invoice)['planned_amount'];

                $in_array [] = [
                    'company' => $company_name,
                    'id' => $invoice->id,
                    'details' => $invoice->additional_info,
                    'amount' => $amount
                ];
            }

            if ($invoice->direction == 'Доход'){
//                if($invoice->status == 'Оплачен'){
//                    if ($invoice->amount_sale_date != '') {
//                        $amount = $invoice->amount_sale_date;
//                    }
//                    else{
//                        $amount = $invoice->amount_income_date;
//                    }
//                }
//                else {
//                    if($invoice->amount_actual != ''){
//                        $amount = $invoice->amount_actual;
//                    }
//                    else {
//                        $amount = $invoice->amount;
//                    }
//                }

                $amount = $this->getInvoiceAmountForReport($invoice)['planned_amount'];

                $out_array [] = [
                    'company' => $company_name,
                    'id' => $invoice->id,
                    'amount' => $amount
                ];
            }
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load("storage/templates/project_template.xlsx");
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('B2', $project->created_at)
            ->setCellValue('F2', $project->name)
            ->setCellValue('C4', $project->client->name)
            ->setCellValue('C5', $project->from.' - '.$project->to)
            ->setCellValue('C6', $project->additional_info);

        $sheet->insertNewRowBefore(13, max(count($in_array), count($out_array)));

        $i = 12;
        foreach ($in_array as $string){
            $sheet->fromArray([$string], NULL, 'D'.$i);
            $i++;
        }

        $k = 12;
        foreach ($out_array as $string){
            $sheet->fromArray([$string], NULL, 'A'.$k);
            $k++;
        }

        if ($project->active == '1') {
            $folder = 'public/Проекты/Активные проекты/'.$project["name"].'/';
            $path = 'public/Проекты/Активные проекты/'.$project["name"].'/'.$project["name"].'_выгрузка.xlsx';
            $savepath = 'storage/Проекты/Активные проекты/'.$project["name"].'/';
        }
        else {
            $folder = 'public/Проекты/Завершенные проекты/'.$project["name"].'/';
            $path = 'public/Проекты/Завершенные проекты/'.$project["name"].'/'.$project["name"].'_выгрузка.xlsx';
            $savepath = 'storage/Проекты/Завершенные проекты/'.$project["name"].'/';
        }
        Storage::makeDirectory($folder);

        $filename = $project["name"].'_выгрузка.xlsx';

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
        $writer->save($savepath.$filename);

        return Storage::download($path);

    }

    public function exportProjectsList($projects, $parameters, $range_text)
    {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load("storage/templates/projects_list_with_parameters.xlsx");
        $sheet = $spreadsheet->getActiveSheet();
        setlocale(LC_TIME, 'ru_RU');

        $sheet->setCellValue('B2', $range_text);
        $sheet->setCellValue('B3', $parameters['sorting_type']);

        $i=5;

        $price = 0;
        $cost = 0;
        $profit = 0;
        $containers_count = 0;
        $income_paid = 0;
        $income_unpaid = 0;
        $outcome_paid = 0;
        $outcome_unpaid = 0;

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

            $sheet->setCellValue('J'.$i, $start[0]);
            $sheet->setCellValue('K'.$i, $finish[0]);
            $sheet->setCellValue('L'.$i, $paid[0]);

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
            if($project->finance['profit']<0){
                $sheet->getStyle('V'.$i)
                    ->getFont()
                    ->getColor()
                    ->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
            }
            $sheet->setCellValue('W'.$i, $project->additional_info);

            $price += $project->finance['price'];
            $cost += $project->finance['cost'];
            $profit += $project->finance['profit'];
            $income_paid += $project->finance['income_paid'];
            $income_unpaid += $project->finance['income_unpaid'];
            $outcome_paid += $project->finance['outcome_paid'];
            $outcome_unpaid += $project->finance['outcome_unpaid'];

            $containers_count += $project->expense->amount;

            $i++;
        }

        $sheet->setCellValue('P'.$i, 'Сумма');
        $sheet->setCellValue('S'.$i, 'Сумма');
        $sheet->setCellValue('V'.$i, 'Сумма');

        $sheet->getStyle('P'.$i.':W'.$i)->getFont()->setBold(true);
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

        $sheet->getStyle('N'.$i.':W'.$i)->getFont()->setBold(true);

        if($profit<0){
            $sheet->getStyle('V'.$i)
                ->getFont()
                ->getColor()
                ->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
        }

        $filename = date('Y-m-d').'_'.$parameters['filename'].'.xlsx';

        if(!File::isDirectory('public/Проекты выгрузка')) Storage::makeDirectory('public/Проекты выгрузка');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
        $writer->save('storage/Проекты выгрузка/'.$filename);

        return 'public/Проекты выгрузка/'.$filename;

    }

    public function exportProjectsListWithFilter(Request $request, ProjectFilter $filter)
    {
        if ($request->data_range != '') {
            $range = explode(' - ', $request->data_range);
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
