<?php

namespace App\Http\Controllers\Adminpage;

use App\Http\Controllers\Controller;
use App\Http\Traits\ContainerTrait;
use App\Http\Traits\ProjectTrait;
use App\Models\Application;
use App\Models\BankAccountBalance;
use App\Models\Container;
use App\Models\ContainerUsageStatistic;
use App\Models\Country;
use App\Models\Invoice;
use App\Models\Notification;
use App\Models\Project;
use App\Models\Task;
use App\Models\WorkRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\FinanceTrait;
use Illuminate\Support\Facades\Storage;
use OwenIt\Auditing\Models\Audit;


class HomeController extends Controller
{
    use FinanceTrait;
    use ProjectTrait;
    use ContainerTrait;

    public function test_function()
    {

        $updates = Storage::files('public/templates/1C/balances/');
        if(!is_null($updates)) {
            foreach ($updates as $file) {
                $result = simplexml_load_string(Storage::get($file));

                $info = [];

                foreach ($result as $value){
                    $info [] = [
                        'account_type' => (string)$value->attributes()['Счет'],
                        'amount' => (string)$value->attributes()['Сумма'],
                        'account_number' => (string)$value->attributes()['БанковскийСчет'],
                        'company' => (string)$value->attributes()['Организация'],
                    ];
                }
                if(!empty($info)){
                    $bank_account_balance = new BankAccountBalance();
                    $bank_account_balance->info = $info;
                    $bank_account_balance->save();
                }
                Storage::move($file, 'public/templates/1C/balances/processed/'.$this->generateRandomString().'_'.basename($file));
            }
        }
    }

    public function getUserCounts()
    {
        $currentUser = auth()->user();

        $current_user_notifications = Notification::where('to_id', $currentUser->id)
            ->whereNull('received')
            ->orderBy('created_at', 'desc')
            ->get();

        $current_user_income_tasks_count = Task::whereJsonContains('to_users', $currentUser->id)
            ->where('active', '1')
            ->where('accepted_user_id', null)
            ->orderBy('created_at', 'desc')
            ->get()
            ->count();

        $current_user_outcome_overdue_tasks_count = Task::where('from_user_id', $currentUser->id)
            ->where('active', '1')
            ->where(function ($query) {
                $query
                    ->whereNotNull('deadline')
                    ->where('deadline', '<', Carbon::now());
            })
            ->get()
            ->count();

        $current_user_income_work_requests_count = WorkRequest::whereJsonContains('to_users', $currentUser->id)
            ->where('active', '1')
            ->where('accepted_user_id', null)
            ->orderBy('created_at', 'desc')
            ->get()
            ->count();

        $current_user_outcome_overdue_work_requests_count = WorkRequest::where('from_user_id', $currentUser->id)
            ->where('active', '1')
            ->where(function ($query) {
                $query
                    ->whereNotNull('deadline')
                    ->where('deadline', '<', Carbon::now());
            })
            ->get()
            ->count();

        $notifications_count = $current_user_notifications->count();

        return response()->json([
            'user_notifications' => view('layouts.ajax.notifications_dropdown', [
                'current_user_notifications' => $current_user_notifications
            ])->render(),
            'current_user_notifications_count' => $notifications_count,
            'current_user_notifications_count_menu' => $notifications_count,
            'current_user_income_tasks_count' => $current_user_income_tasks_count,
            'current_user_outcome_overdue_tasks_count' => $current_user_outcome_overdue_tasks_count,
            'current_user_income_work_requests_count' => $current_user_income_work_requests_count,
            'current_user_outcome_overdue_work_requests_count' => $current_user_outcome_overdue_work_requests_count
        ]);

    }

    public function index()
    {
        $user = auth()->user();
        $role = $user->getRoleNames()[0];

        if (in_array($role, ['super-admin', 'director'])) {

            $active_project_count = Project::where('active', 1)->where('status', '<>', 'Черновик')->count();
            $this_month_projects_count = DB::table('projects')->whereMonth('created_at', Carbon::now()->month)->count();
            $this_month_finished_projects_count = DB::table('projects')->whereMonth('finished_at', Carbon::now()->month)->count();
            $draft_projects_count = Project::where('status', 'Черновик')->count();
            $invoices_count = Invoice::where('status', 'Счет на согласовании')->orderBy('created_at', 'desc')->count();

            return view('home.director', [
                'active_project_count' => $active_project_count,
                'draft_projects_count' => $draft_projects_count,
                'this_month_projects_count' => $this_month_projects_count,
                'this_month_finished_projects_count' => $this_month_finished_projects_count,
                'active_projects_estimated_profit' => $this->getThisActiveProjectsEstimatedProfit(),
                'this_month_total_profit' => $this->getThisMonthTotalProfit(),
                'invoices_count' => $invoices_count,
                'roles' => $role
            ]);


        }

        if (in_array($role, ['manager', 'logist', 'special', 'supply', 'equipment'])) {

            $my_projects = Project::where('active', 1)->where('user_id', $user->id)->orWhere('manager_id', $user->id)->orWhere('logist_id', $user->id)->orderBy('created_at', 'desc')->paginate(30, ['*'], 'my_projects');

            foreach ($my_projects as $project => $key) {
                $key->finance = $this->getProjectFinance($key['id']);
                $key->complete_level = $this->getProjectCompleteLevel($key['id']);

                switch ($key->status) {
                    case 'Черновик':
                        $key->status_class = 'secondary';
                        break;
                    case 'В работе':
                        $key->status_class = 'primary';
                        break;
                    case 'Завершен':
                        $key->status_class = 'success';
                        break;
                    default:
                        $key->status_class = 'info';
                }
            }

            $user->stat = $this->getManagerThisMonthStatistic($user->id);

            return view('home.manager', [
                'projects' => $my_projects,
                'my_projects_count' => $my_projects->count(),
                'user' => $user,
            ]);
        }

        if ($role == 'accountant') {

            return view('home.accountant', [
                'agreed_invoices_count' => $this->getAgreedInvoices()['count'],
                'this_month_total_profit' => $this->getThisMonthTotalProfit(),
                'out_invoices_count' => $this->getActiveProjectsOutInvoices()['out_invoices_count'],
                'in_invoices_count' => $this->getActiveProjectsInInvoices()['in_invoices_count'],
                'waiting_invoices_count' => $this->getWaintingForCreateInvoices()['count'],
                'credit' => $this->getActiveProjectsOutInvoices()['sum'],
                'debit' => $this->getActiveProjectsInInvoices()['sum']
            ]);
        }

    }

}
