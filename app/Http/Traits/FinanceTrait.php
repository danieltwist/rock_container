<?php

namespace App\Http\Traits;
use App\Models\CurrencyRate;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\ProjectExpense;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait FinanceTrait {

    public function updateProjectFinance($id){

        $project = Project::find($id);

        if(!empty($project)){
            $invoices = $project->invoices;

            $income = 0;
            $income_total = 0;
            $outcome = 0;
            $outcome_total = 0;

/// 04072022
//            foreach ($invoices as $invoice){
//                if ($invoice->direction == 'Доход'){
//                    if ($invoice->amount_sale_date != '') {
//                        $income += $invoice->amount_sale_date;
//                    }
//                    elseif($invoice->amount_income_date != ''){
//                        $income += $invoice->amount_income_date;
//                    }
//                    elseif($invoice->amount_actual != ''){
//                        $income += $invoice->amount_actual;
//                    }
//                    else {
//                        $income += $invoice->amount;
//                    }
//                    $income_total += $invoice->amount;
//                }
//                if ($invoice->direction == 'Расход'){
//                    if ($invoice->amount_income_date != '') {
//                        $outcome += $invoice->amount_income_date;
//                    }
//                    elseif($invoice->amount_actual != ''){
//                        $outcome += $invoice->amount_actual;
//                    }
//                    else {
//                        $outcome += $invoice->amount;
//                    }
//                    $outcome_total += $invoice->amount;
//                }
//            }

            foreach ($invoices as $invoice){
                if ($invoice->direction == 'Доход'){
                    $income += $this->getInvoiceAmountForReport($invoice)['actual_amount'];
                    $income_total += $this->getInvoiceAmountForReport($invoice)['planned_amount'];
                }
                else {
                    $outcome += $this->getInvoiceAmountForReport($invoice)['actual_amount'];
                    $outcome_total += $this->getInvoiceAmountForReport($invoice)['planned_amount'];
                }
            }

            $snp_amount = [
                'snp_for_us_usd' => 0,
                'snp_for_us_cny' => 0,
                'snp_for_us_rub' => 0,
                'snp_for_client_usd' => 0,
                'snp_for_client_cny' => 0,
                'snp_for_client_rub' => 0,
            ];

            if(!is_null($project->containers)){
                foreach ($project->containers as $container){
                    $getContainerUsageDates = $this->getContainerUsageDates($container->id);
                    if($getContainerUsageDates['snp_amount_for_us'] != 0){
                        switch ($getContainerUsageDates['snp_currency']){
                            case 'USD':
                                $snp_amount['snp_for_us_usd'] += $getContainerUsageDates['snp_amount_for_us'];
                                break;
                            case 'CNY':
                                $snp_amount['snp_for_us_cny'] += $getContainerUsageDates['snp_amount_for_us'];
                                break;
                            case 'RUB':
                                $snp_amount['snp_for_us_rub'] += $getContainerUsageDates['snp_amount_for_us'];
                                break;
                        }
                    }
                    if($getContainerUsageDates['snp_amount_for_client'] != 0){
                        switch ($getContainerUsageDates['snp_currency']){
                            case 'USD':
                                $snp_amount['snp_for_client_usd'] += $getContainerUsageDates['snp_amount_for_client'];
                                break;
                            case 'CNY':
                                $snp_amount['snp_for_client_cny'] += $getContainerUsageDates['snp_amount_for_client'];
                                break;
                            case 'RUB':
                                $snp_amount['snp_for_client_rub'] += $getContainerUsageDates['snp_amount_for_client'];
                                break;
                        }
                    }
                }
            }

            if(!is_null($project->containers_used)){
                foreach ($project->containers_used as $container_stat){
                    if($container_stat->snp_total_amount_for_us != 0){
                        switch ($container_stat->snp_currency){
                            case 'USD':
                                $snp_amount['snp_for_us_usd'] += $container_stat->snp_total_amount_for_us;
                                break;
                            case 'CNY':
                                $snp_amount['snp_for_us_cny'] += $container_stat->snp_total_amount_for_us;
                                break;
                            case 'RUB':
                                $snp_amount['snp_for_us_rub'] += $container_stat->snp_total_amount_for_us;
                                break;
                        }
                    }
                    if($container_stat->snp_total_amount_for_client != 0){
                        switch ($container_stat->snp_currency){
                            case 'USD':
                                $snp_amount['snp_for_client_usd'] += $container_stat->snp_total_amount_for_client;
                                break;
                            case 'CNY':
                                $snp_amount['snp_for_client_cny'] += $container_stat->snp_total_amount_for_client;
                                break;
                            case 'RUB':
                                $snp_amount['snp_for_client_rub'] += $container_stat->snp_total_amount_for_client;
                                break;
                        }
                    }
                }
            }

            $profit = $income - $outcome;
            $profit_total = $income_total - $outcome_total;

            $not_null_snp_amount = false;

            foreach ($snp_amount as $key => $value){
                if($value != 0) $not_null_snp_amount = true;
            }

            if($not_null_snp_amount){
                ProjectExpense::where('project_id', $project->id)->update([
                    'snp_amount' => $snp_amount,
                    'current_outcome' => $outcome,
                    'current_income' => $income,
                    'current_profit' => $profit,
                    'total_outcome' => $outcome_total,
                    'total_income' => $income_total,
                    'total_profit' => $profit_total,
                ]);
            }
            else {
                ProjectExpense::where('project_id', $project->id)->update([
                    'current_outcome' => $outcome,
                    'current_income' => $income,
                    'current_profit' => $profit,
                    'total_outcome' => $outcome_total,
                    'total_income' => $income_total,
                    'total_profit' => $profit_total,
                ]);
            }
        }

    }

    public function getActiveProjectsOutInvoices()
    {

        $active_projects = Project::where('active',1)->where('status','<>','Черновик')->get();

        $project_id = [];

        $summ = 0;

        foreach ($active_projects as $active_project){

            $summ += $this->getProjectFinance($active_project->id)['price'];
            $project_id [] = $active_project->id;

        }

        $out_invoices_count = Invoice::where('direction', 'Доход')->whereIn('project_id', $project_id)->count();

        return [
            'out_invoices_count' => $out_invoices_count,
            'sum' => $summ
        ];


    }

    public function getActiveProjectsInInvoices()
    {

        $active_projects = Project::where('active', 1)->where('status','<>','Черновик')->get();

        $project_id = [];

        $summ = 0;

        foreach ($active_projects as $active_project){

            $summ += $this->getProjectFinance($active_project->id)['cost'];
            $project_id [] = $active_project->id;

        }

        $in_invoices_count = Invoice::where('direction', 'Расход')->whereIn('project_id', $project_id)->count();

        return [
            'in_invoices_count' => $in_invoices_count,
            'sum' => $summ
        ];


    }

    public function getProjectFinance($id): array
    {
        $currency_rates = CurrencyRate::orderBy('created_at', 'desc')->first();

        $project = Project::where('id', $id)->withTrashed()->first();

        if ($project->currency == 'RUB'){
            $today_rate = 1;
        }
        else {

            $project_currency = optional($project->expense)->currency;
            $project_currency == '' ? $today_rate = 'Валюта не выбрана' : $today_rate = $currency_rates->$project_currency-1;

        }
        $project_expense = ProjectExpense::where('project_id', $project->id)->first();

        if (!is_null($project_expense)){
            $finance = array(
                'price' => $project_expense->current_income,
                'cost' => $project_expense->current_outcome,
                'profit' => $project_expense->current_profit,
                'total_price' => $project_expense->total_income,
                'total_cost' => $project_expense->total_outcome,
                'total_profit' => $project_expense->total_profit,
                'today_rate' => $today_rate,
            );
        }

        else {
            $create_project_expense = new ProjectExpense();

            $create_project_expense->project_id = $project->id;
            $create_project_expense->amount = 0;
            $create_project_expense->currency = 'RUB';
            $create_project_expense->cb_rate = 1;
            $create_project_expense->price_1pc = 0;
            $create_project_expense->price_in_currency = 0;
            $create_project_expense->price_in_rub = 0;
            $create_project_expense->planned_costs = 0;
            $create_project_expense->planned_profit = 0;

            $create_project_expense->save();

            $finance = array(
                'price' => 0,
                'cost' => 0,
                'profit' => 0,
                'today_rate' => 1
            );

        }

        /*$invoices = $project->invoices;

        if ($project->currency == 'RUB'){
            $today_rate = 1;
        }
        else {

            $project_currency = optional($project->expense)->currency;
            $project_currency == '' ? $today_rate = 'Валюта не выбрана' : $today_rate = $currency_rates->$project_currency-1;

        }

        $price = 0;
        $costs = 0;

        foreach ($invoices as $invoice){
            if ($invoice->direction == 'Доход'){
                if ($invoice->amount_sale_date != '') {
                    $price += $invoice->amount_sale_date;
                }
                elseif($invoice->amount_income_date != ''){
                    $price += $invoice->amount_income_date;
                }
                else {
                    $price += $invoice->amount;
                }
            }
            if ($invoice->direction == 'Расход'){
                if ($invoice->amount_income_date != '') {
                    $costs += $invoice->amount_income_date;
                }
                else {
                    $costs += $invoice->amount;
                }
            }
        }

        $profit = $price - $costs;
        */


        return $finance;
    }

    public function getThisActiveProjectsEstimatedProfit (){

        $active_projects = Project::where('active',1)
            ->where('status','<>','Черновик')
            ->whereNull('remove_from_stat')
            ->get();
//        dd($active_projects);
        $profit = 0;

        if (!empty($active_projects)) {
            foreach ($active_projects as $project){
                $finance = $this->getProjectFinance($project->id);
                $profit += $finance['profit'];
            }
        }

        return $profit;
    }

    public function getThisMonthTotalProfit (){

        $this_month_projects = DB::table('projects')
            ->whereMonth('finished_at', Carbon::now()->month)
            ->whereNull('remove_from_stat')
            ->where('active',0)
            ->get();

        $profit = 0;

        if (!empty($this_month_projects)) {
            foreach ($this_month_projects as $project){
                $finance = $this->getProjectFinance($project->id);
                $profit += $finance['total_profit'];
            }
        }

        return $profit;
    }

    public function getManagerThisMonthStatistic($id)
    {
        $active_projects = DB::table('projects')
            ->where('active',1)
            ->where('status','<>','Черновик')
            ->where(function($query) use ($id)
            {
                $query->where('user_id', $id)
                    ->orWhere('logist_id', $id)
                    ->orWhere('manager_id', $id);
            })
            ->get();

        $draft_projects = DB::table('projects')
            ->where('status','=','Черновик')
            ->where(function($query) use ($id)
            {
                $query->where('user_id', $id)
                    ->orWhere('logist_id', $id)
                    ->orWhere('manager_id', $id);
            })
            ->get();

        $finished_projects = DB::table('projects')
            ->whereMonth('finished_at', Carbon::now()->month)
            ->where('active',0)
            ->where(function($query) use ($id)
            {
                $query->where('user_id', $id)
                    ->orWhere('logist_id', $id)
                    ->orWhere('manager_id', $id);
            })
            ->get();


        $active_projects_profit = 0;

        foreach ($active_projects as $active_project){

            $active_projects_finance = $this->getProjectFinance($active_project->id);
            $active_projects_profit += $active_projects_finance['profit'];

        }

        $finished_projects_profit = 0;

        foreach ($finished_projects as $finished_project){

            $finished_projects_finance = $this->getProjectFinance($finished_project->id);
            $finished_projects_profit += $finished_projects_finance['profit'];

        }

        return array (
            'active_projects_profit' => $active_projects_profit,
            'active_projects_count' => $active_projects->count(),
            'finished_projects_profit' => $finished_projects_profit,
            'finished_projects_count' => $finished_projects->count(),
            'draft_projects_count' => $draft_projects->count()
        );

    }

    public function getUserInfoForStatistic($id)
    {
        $active_projects = DB::table('projects')
            ->where('active',1)
            ->where('status','<>','Черновик')
            ->where(function($query) use ($id)
            {
                $query->where('user_id', $id);
            })
            ->get();

        $draft_projects = DB::table('projects')
            ->where('status','=','Черновик')
            ->where(function($query) use ($id)
            {
                $query->where('user_id', $id);
            })
            ->get();

        $finished_projects = DB::table('projects')
            ->whereMonth('finished_at', Carbon::now()->month)
            ->where('active',0)
            ->where(function($query) use ($id)
            {
                $query->where('user_id', $id);
            })
            ->get();

        $all_finished_projects = DB::table('projects')
            ->where('active',0)
            ->where('paid', 'Оплачен')
            ->where(function($query) use ($id)
            {
                $query->where('user_id', $id);
            })
            ->get();

        $tasks_count = Task::whereJsonContains('to_users', (int)$id)->count();

        $active_projects_profit = 0;
        foreach ($active_projects as $active_project){

            $active_projects_profit += $this->getProjectFinance($active_project->id)['profit'];

        }

        $finished_projects_profit = 0;
        foreach ($finished_projects as $finished_project){

            $finished_projects_profit += $this->getProjectFinance($finished_project->id)['profit'];

        }

        $all_finished_projects_profit = 0;
        foreach ($all_finished_projects as $one_finished_projects){
            $all_finished_projects_profit += $this->getProjectFinance($one_finished_projects->id)['profit'];
        }

        return array (
            'active_projects_profit' => $active_projects_profit,
            'active_projects_count' => $active_projects->count(),
            'finished_projects_profit' => $finished_projects_profit,
            'finished_projects_count' => $finished_projects->count(),
            'all_finished_projects_profit' => $all_finished_projects_profit,
            'all_finished_projects_count' => $all_finished_projects->count(),
            'draft_projects_count' => $draft_projects->count(),
            'tasks_count' => $tasks_count
        );

    }

    public function getAgreedInvoices(){

        $agreed_invoices = Invoice::where('status', 'Счет согласован на оплату')->orWhere('status', 'Согласована частичная оплата')->paginate(30, ['*'], 'agreed_invoices');;
        $agreed_invoices_count = $agreed_invoices->count();


        return [
            'invoices' => $agreed_invoices,
            'count' => $agreed_invoices_count
        ];

    }

    public function getWaintingForCreateInvoices(){

        $waiting_invoices = Invoice::where('status', 'Создан черновик инвойса')->paginate(30, ['*'], 'waiting_invoices');
        $waiting_invoices_count = $waiting_invoices->count();


        return [
            'invoices' => $waiting_invoices,
            'count' => $waiting_invoices_count
        ];

    }

    public function invoiceGiveClass(Invoice $invoice)
    {
        switch ($invoice->status) {
            case 'Удален':
            case 'Не оплачен':
                $invoice->class = 'danger';
                break;
            case 'Частично оплачен':
            case 'Оплачен':
                $invoice->class = 'success';
                break;
            case 'Ожидается счет от поставщика':
            case 'Ожидается создание инвойса':
            case 'Создан черновик инвойса':
            case 'Ожидается загрузка счета':
                $invoice->class = 'warning';
                break;
            case 'Согласована частичная оплата':
            case 'Счет согласован на оплату':
                $invoice->class = 'info';
                break;
            case 'Ожидается оплата':
                $invoice->class = 'primary';
                break;
            case 'Счет на согласовании':
                $invoice->class = 'secondary';
                break;
            default:
                $invoice->class = 'secondary';
        }
    }

    public function getInvoiceAmount(Invoice $invoice){

        if ($invoice->direction == 'Доход'){
//            if ($invoice->amount_sale_date != '') {
//                $amount = $invoice->amount_sale_date;
//            }
//            if($invoice->amount_income_date != ''){
//                $amount = $invoice->amount_income_date;
//            }
            if($invoice->amount_actual != ''){
                $amount = $invoice->amount_actual;
            }
            else {
                $amount = $invoice->amount;
            }
        }
        if ($invoice->direction == 'Расход'){
//            if ($invoice->amount_income_date != '') {
//                $amount = $invoice->amount_income_date;
//            }
            if($invoice->amount_actual != ''){
                $amount = $invoice->amount_actual;
            }
            else {
                $amount = $invoice->amount;
            }
        }

        return (int)$amount;

    }

    public function getInvoiceAmountForReport(Invoice $invoice){

        $invoice->amount_actual != '' ? $planned_amount = $invoice->amount_actual: $planned_amount =  $invoice->amount;

        if ($invoice->amount_sale_date != '') {
            $fact_amount = $invoice->amount_sale_date;
        }
        else {
            if($invoice->status == 'Оплачен'){
                $fact_amount = $invoice->amount_income_date;
            }
            else {
                if($invoice->amount_actual != ''){
                    $fact_amount = $invoice->amount_actual;
                }
                else {
                    $fact_amount = $invoice->amount;
                }
            }
        }

        $invoice->amount_income_date == '' ? $paid = 0 : $paid = $invoice->amount_income_date;

        return [
            'planned_amount' => str_replace(',', '.', $planned_amount),
            'actual_amount' => str_replace(',', '.', $fact_amount),
            'paid' => str_replace(',', '.', $paid)
        ];

    }

    public function getInvoiceAmountWithCurrency(Invoice $invoice){
        if($invoice->currency == 'RUB'){
            if($invoice->amount_actual != ''){
                $amount = $invoice->amount_actual;
            }
            else {
                $amount = $invoice->amount;
            }
        }
        else {
            if($invoice->amount_in_currency_actual != ''){
                $amount = $invoice->amount_in_currency_actual;
            }
            else {
                $amount = $invoice->amount_in_currency;
            }
        }

        return [
            'amount' => $amount,
            'currency' => $invoice->currency
        ];
    }

    public function getInvoicePaidAmount(Invoice $invoice){

        if($invoice->amount_income_date != ''){
            $amount = $invoice->amount_income_date;
        }
        else {
            $amount = 0;
        }

        return (int)$amount;

    }

    public function updateInvoiceLosses($invoice, $invoice_id_for_losses_compensation){

        $invoice_amount = $this->getInvoiceAmount($invoice);
        $income_invoice_amount = 0;

        if($invoice_id_for_losses_compensation != ''){
            $income_invoice_amount = $this->getInvoiceAmount(Invoice::findOrFail($invoice_id_for_losses_compensation));
        }

        $loss_amount = $income_invoice_amount - $invoice_amount;

        if($loss_amount < 0) {
            $invoice->losses_amount = $loss_amount;
        }
        else {
            $invoice->losses_amount = null;
        }

        $invoice->save();
    }

    public function giveInvoiceTableColClass($invoice)
    {

        $class = '';

        if(in_array($invoice->status, ['Оплачен','Частично оплачен'])){
            $class = 'table-success';
        }

        if((!is_null($invoice->losses_amount) && is_null($invoice->losses_confirmed)) || !is_null($invoice->losses_confirmed)) {
            $class = 'table-danger';
        }

        return $class;
    }

    public function getProjectFinanceForReport($id): array
    {
        $project = Project::find($id);

        $invoices = $project->invoices;

        $price = 0;
        $costs = 0;
        $income_paid = 0;
        $outcome_paid = 0;
//040722
//        foreach ($invoices as $invoice){
//            if ($invoice->direction == 'Доход'){
//                if($invoice->status == 'Оплачен'){
//                    if ($invoice->amount_sale_date != '') {
//                        $price += $invoice->amount_sale_date;
//                        $income_paid += $invoice->amount_sale_date;
//                    }
//                    elseif ($invoice->amount_income_date != '') {
//                        $price += $invoice->amount_income_date;
//                        $income_paid += $invoice->amount_income_date;
//                    }
//                }
//                elseif($invoice->status == 'Частично оплачен'){
//                    if($invoice->amount_actual != ''){
//                        $price += $invoice->amount_actual;
//                    }
//                    else {
//                        $price += $invoice->amount;
//                    }
//                    $income_paid += $invoice->amount_income_date;
//                }
//                else {
//                    if($invoice->amount_actual != ''){
//                        $price += $invoice->amount_actual;
//                    }
//                    else {
//                        $price += $invoice->amount;
//                    }
//                }
//            }
//            else {
//                if($invoice->status == 'Оплачен'){
//                    if ($invoice->amount_income_date != '') {
//                        $costs += $invoice->amount_income_date;
//                    }
//                }
//                else {
//                    if($invoice->amount_actual != ''){
//                        $costs += $invoice->amount_actual;
//                    }
//                    else {
//                        $costs += $invoice->amount;
//                    }
//                }
//                if($invoice->amount_income_date != ''){
//                    if(in_array($invoice->status, ['Оплачен', 'Частично оплачен'])) $outcome_paid += $invoice->amount_income_date;
//                }
//
//            }
//
//        }

//          19082022
//        foreach ($invoices as $invoice){
//            if ($invoice->direction == 'Доход'){
//                $price += $this->getInvoiceAmountForReport($invoice)['planned_amount'];
//                $income_paid += $this->getInvoiceAmountForReport($invoice)['paid'];
//            }
//            else {
//                $costs += $this->getInvoiceAmountForReport($invoice)['planned_amount'];
//                $outcome_paid += $this->getInvoiceAmountForReport($invoice)['paid'];
//            }
//        }
        foreach ($invoices as $invoice){
            if ($invoice->direction == 'Доход'){
                $price += $this->getInvoiceAmountForReport($invoice)['actual_amount'];
                $income_paid += $this->getInvoiceAmountForReport($invoice)['paid'];
            }
            else {
                $costs += $this->getInvoiceAmountForReport($invoice)['actual_amount'];
                $outcome_paid += $this->getInvoiceAmountForReport($invoice)['paid'];
            }
        }

        $profit = $price - $costs;

        return [
            'price' => $price,
            'cost' => $costs,
            'profit' => $profit,
            'income_paid' => $income_paid,
            'income_unpaid' => $price - $income_paid,
            'outcome_paid' => $outcome_paid,
            'outcome_unpaid' => $costs - $outcome_paid,
            'today_rate' => 1
        ];

    }

}
