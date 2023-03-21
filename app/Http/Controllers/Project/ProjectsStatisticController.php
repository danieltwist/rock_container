<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Http\Traits\FinanceTrait;
use App\Models\Invoice;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectsStatisticController extends Controller
{
    use FinanceTrait;

    public function getStatistic(Request $request)
    {
        if($request->data_range != '') {
            if ($request->data_range == 'all' || $request->data_range =='Все') {
                $projects = Project::where('active', '0')->get();
            }
            else {
                $range = explode(' - ', $request->data_range);

                $projects = Project::whereDate('finished_at', '>=', $range[0])
                    ->whereDate('finished_at', '<=', $range[1])
                    ->where('active', '0')
                    ->where('status', '<>', 'Черновик')
                    ->where('paid', 'Оплачен')
                    ->orderBy('finished_at','desc')
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
            return view('project.statistic.index', [
                'project_count' => $projects->count(),
                'cost' => $cost,
                'price' => $price,
                'profit' => $profit,
                'filter_type' => 'finished',
                'in_invoices' => $in_invoices,
                'out_invoices' => $out_invoices,
                'projects' => $projects,
                'id_array' => $id_array,
                'data_range' => $request->data_range
            ]);
        }
        else {
            return view('project.statistic.not_selected');
        }
    }


}
