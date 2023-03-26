<?php

namespace App\Http\Controllers\Project;

use App\Filters\ProjectFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFormRequest;
use App\Http\Traits\ContainerTrait;
use App\Http\Traits\FinanceTrait;
use App\Http\Traits\ProjectTrait;
use App\Models\Application;
use App\Models\BlockItem;
use App\Models\Container;
use App\Models\ContainerGroup;
use App\Models\ContainerGroupLocation;
use App\Models\ContainerUsageStatistic;
use App\Models\CurrencyRate;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\ProjectExpense;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Client;
use App\Models\Block;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Yandex\Disk\DiskClient;
use function PHPUnit\Framework\assertDirectoryDoesNotExist;

class ProjectController extends Controller
{

    public function __construct()
    {
        $this->middleware('can:edit projects,remove_projects')->only(['edit', 'update', 'destroy']);
    }

    use FinanceTrait;
    use ProjectTrait;
    use ContainerTrait;

    private $user;

    public function index(ProjectFilter $request)
    {
        return view('project.index',[
            'page_title' => __('project.all_projects'),
        ]);
    }

    public function create()
    {
        $currency_rates = CurrencyRate::orderBy('created_at', 'desc')->first();

        $users = User::whereHas('roles', function ($query) {
            $query->whereNotIn('name', ['super-admin']);
        })->get();

        $clients = Client::orderBy('created_at','desc')->get();
        $suppliers = Supplier::orderBy('created_at','desc')->get();

        return view('project.create',[
            'clients' => $clients,
            'suppliers' =>$suppliers,
            'rates' => $currency_rates,
            'users' => $users,
            'today' => Carbon::now()->format('Ymd').'_',
        ]);
    }

    public function store(StoreFormRequest $request)
    {
        $user = auth()->user();
        $role = $user->getRoleNames()[0];

        if (in_array($role,['super-admin','director'])){
            $status = 'В работе';
        }
        else {
            $status = 'Черновик';
        }

        $new_project = new Project();

        $request->additional_clients == '' ? $additional_clients = null : $additional_clients = serialize($request->additional_clients);

        $new_project->name = $request->name;
        $new_project->client_id = $request->client_id;
        $new_project->additional_clients = $additional_clients;
        $new_project->from = $request->from;
        $new_project->pogranperehod = $request->pogranperehod;
        $new_project->to = $request->to;
        $new_project->freight_info = $request->freight_info;
        $new_project->freight_amount = $request->freight_amount;
        $new_project->user_id = $request->user_id;
        $new_project->manager_id = $request->manager_id;
        $new_project->logist_id = $request->logist_id;
        $new_project->additional_info = $request->additional_info;
        $new_project->planned_payment_date = $request->planned_payment_date;
        $new_project->prepayment = $request->prepayment;
        $new_project->status = $status;
        !isset($request->management_expenses) ?: $new_project->management_expenses = $request->management_expenses;
        $new_project->access_to_project = $request->access_to_project;


        $new_project->save();

        if($request->folder_yandex_disk != '') {

            if (isset($_COOKIE['yaToken'])) {

                $diskClient = new DiskClient($_COOKIE['yaToken']);
                $diskClient->setServiceScheme(DiskClient::HTTPS_SCHEME);

                $root_folder = $request->folder_yandex_disk;
                $project_folder = $request->name;

                try {
                    $diskClient->createDirectory($root_folder);
                } catch (\Exception $e) {

                }

                $subfolders = [
                    'Заявки', 'Договоры', 'Договоры/Поставщики', 'Договоры/Клиенты', 'Отгрузочные документы', 'Реестры', 'Сопроводительные документы', 'ЖДН и акты Китай', 'Счета', 'Счета/Клиенты', 'Счета/Поставщики'
                ];
                /*
                $files = [
                    [
                        'name' => 'Тестовый договор шаблон.xlsx', 'folder' => 'Договоры/Клиенты'
                    ],
                    [
                        'name' => 'Заявка.xlsx', 'folder' => 'Заявки'
                    ]
                ];
                */
                try {
                    $dirContent = $diskClient->createDirectory($root_folder.'/'.$project_folder);
                    if ($dirContent) {
                        foreach ($subfolders as $folder) {
                            try {
                                $diskClient->createDirectory($root_folder .'/'. $project_folder .'/' . $folder);
                            } catch (\Exception $e) {
                                continue;
                            }
                        }

                        /*foreach ($files as $file) {
                            $fileName = public_path('/storage/excel_containers/containers_update_template.xlsx');
                            $newName = $file['name'];
                            if (file_exists($fileName)) {
                                try {
                                    $diskClient->uploadFile(
                                        $root_folder . '/' . $project_folder . '/' . $file['folder'] . '/',
                                        array(
                                            'path' => $fileName,
                                            'size' => filesize($fileName),
                                            'name' => $newName
                                        )
                                    );
                                } catch (\Exception $e) {
                                    continue;
                                }
                            }

                        }*/

                    }
                } catch (\Exception $e) {

                }
            }
        }

        if(!is_null($request->expenses_array)){
            $expenses_array = serialize($request->expenses_array);
        }
        else {
            $expenses_array = null;
        }

        ProjectExpense::create([
            'project_id' => $new_project->id,
            'amount' => $request->amount,
            'currency' => $request->currency,
            'cb_rate' => $request->cb_rate,
            'price_1pc' => $request->price_1pc,
            'price_in_currency' => $request->price_in_currency,
            'price_in_rub' => $request->price_in_rub,
            'planned_costs' => $request->planned_costs,
            'planned_profit' => $request->planned_profit,
            'expenses_array' => $expenses_array
        ]);

        return redirect()->route('project.show',$new_project->id)->withSuccess(__('project.created_successfully'));
    }

    public function show(Project $project)
    {
        $blocks = Block::where('project_id', $project->id)->get();

        $currency_rates = CurrencyRate::orderBy('created_at', 'desc')->first();

        foreach ($project->all_clients() as $id){
            $this_project_clients [] = Client::find($id);
        }

        /*
        $can_finish_project = false;

        if($project->active == '1'){
            if($blocks->isNotEmpty()){

                foreach ($blocks as $block){
                    if ($block['status']=='Завершен') {
                        $can_finish_project = true;
                        continue;
                    }
                    else {
                        $can_finish_project = false;
                    }
                }
            }
            else $can_finish_project = true;
        }
        */

        $project->active == '1' ? $can_finish_project = true : $can_finish_project = false;

        $clients = Client::orderBy('created_at','desc')->get();
        $suppliers = Supplier::orderBy('created_at','desc')->get();

        $role = auth()->user()->getRoleNames()[0];

        if(!in_array($role,['super-admin','director'])){
            $invoices = $project->invoices->where('user_add', auth()->user()->name);
        }
        else $invoices = $project->invoices;

        foreach ($invoices as $invoice){
            switch($invoice->status){
                case 'Удален': case 'Не оплачен':
                    $invoice->class = 'danger';
                    break;
                case 'Частично оплачен': case 'Оплачен':
                    $invoice->class = 'success';
                    break;
                case 'Ожидается счет от поставщика': case 'Ожидается создание инвойса': case 'Создан черновик инвойса': case 'Ожидается загрузка счета':
                    $invoice->class = 'warning';
                    break;
                case 'Согласована частичная оплата': case 'Счет согласован на оплату':
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

        $container_groups = ContainerGroup::where('project_id', $project->id)->get();

        foreach ($container_groups as $group){
            $containers = unserialize($group->containers);
            $group->container_group_locations_list = ContainerGroupLocation::where('container_group_id', $group['id'])->get();

            $containers_list = array();

            foreach ($containers as $container){
                $container = Container::find($container);
                if(!is_null($container)){
                    $container->usage_statistic = ContainerUsageStatistic::where('project_id', $project->id)->where('container_id', $container->id)->get();
                    $container->usage_dates = $this->getContainerUsageDates($container->id);
                    $containers_list[] = $container;
                }
            }

            $group->containers_list = $containers_list;

        }

        return view('project.show',[
            'project' => $project,
            'blocks' => $blocks,
            'this_project_clients' => $this_project_clients,
            'finance' => $this->getProjectFinance($project->id),
            'complete_level' => $this->getProjectCompleteLevel($project->id),
            'invoices' => $invoices,
            'files' => $this->getFiles($project->id),
            'clients' => $clients,
            'suppliers' => $suppliers,
            'container_groups' => $container_groups,
            'can_finish_project' => $can_finish_project,
            'applications' => $this->getApplications($project->id),
            'comments' => $project->comments,
            'rates' => $currency_rates,
            'users' => User::all()
        ]);
    }

    public function edit(Project $project)
    {

        $currency_rates = CurrencyRate::orderBy('created_at', 'desc')->first();

        $users = User::whereHas('roles', function ($query) {
            $query->whereNotIn('name', ['super-admin']);
        })->get();

        $clients = Client::orderBy('created_at','desc')->get();

        $additional_clients = [];


        if($project->additional_clients != ''){

            foreach (unserialize($project->additional_clients) as $additional_client){
                $additional_clients [] = Client::find($additional_client);
            }

        }

        $project_clients = $additional_clients;
        $project_clients [] = $project->client;

        $suppliers = Supplier::orderBy('created_at','desc')->get();

        return view('project.edit',[
            'project' => $project,
            'rates' => $currency_rates,
            'users' => $users,
            'clients' => $clients,
            'suppliers' => $suppliers,
            'project_clients' => $project_clients,
            'additional_clients' => $additional_clients
        ]);

    }

    public function update(Request $request, Project $project)
    {
        if ($request->action == 'upload_lading_photos'){

            if($request->hasFile('photos')) {

                $container_number = preg_replace( "/[^(\w)|(\x7F-\xFF)|(\s)|(\-)]/", '', $request->container_number);
                $i=1;
                foreach ($request->file('photos') as $photo){

                    $filename = $container_number.'PHOTOS'.$i;
                    $photo->storeAs('public/Проекты/Активные проекты/'.$project->name.'/Фото погрузки/'.$container_number.'/', $filename.'.'.$photo->extension());
                    $i++;

                }

                return response()->json([
                    'bg-class' => 'bg-success',
                    'from' => 'Система',
                    'message' => __('project.upload_lading_photos_successfully'),
                    'ajax' => view('project.ajax.project_files', [
                        'files' => $this->getFiles($project->id)
                    ])->render()
                ]);
            }

            else {
                return response()->json([
                    'bg-class' => 'bg-danger',
                    'from' => 'Система',
                    'message' => __('general.first_choose_file'),
                    'ajax' => view('project.ajax.project_files', [
                        'files' => $this->getFiles($project->id)
                    ])->render()
                ]);
            }

        }

        if ($request->action == 'upload_application_client'){

            if($request->hasFile('application')) {

                $new_application = new Application();

                $new_application->type = 'Клиент';
                $new_application->client_id = $request->client_id;
                $new_application->project_id = $project->id;
                $new_application->contract_id = $request->contract;

                $client = Client::find($request->client_id);

                $folder = preg_replace( "/[^(\w)|(\x7F-\xFF)|(\s)|(\-)]/", '', $client->name );
                $new_application->file = $request->application->storeAs('public/Проекты/Активные проекты/'.$project->name.'/Заявки/Клиент/'.$folder, $request->application->getClientOriginalName());

                $new_application->save();

                return response()->json([
                    'bg-class' => 'bg-success',
                    'from' => 'Система',
                    'message' => __('project.upload_application_client_successfully'),
                    'ajax' => view('project.ajax.project_applications', [
                        'applications' => $this->getApplications($project->id)
                    ])->render()
                ]);
            }

            else {
                return response()->json([
                    'bg-class' => 'bg-danger',
                    'from' => 'Система',
                    'message' => __('general.first_choose_file'),
                    'ajax' => view('project.ajax.project_applications', [
                        'applications' => $this->getApplications($project->id)
                    ])->render()
                ]);
            }

        }

        if ($request->action == 'upload_files'){

            if($request->hasFile('files')) {

                if($project->active == '1'){

                    $path = 'public/Проекты/Активные проекты/'.$project["name"].'/';

                }
                else {

                    $path = 'public/Проекты/Завершенные проекты/'.$project["name"].'/';

                }

                $folder = $request->folder;

                $subfolder = false;

                if($request->subfolder_new == ''){

                    $request->subfolder !='' ? $subfolder = $request->subfolder : $subfolder = false;

                }
                else $subfolder = $request->subfolder_new;

                if($subfolder){
                    $save_path = $path.$folder.'/'.$subfolder.'/';
                }
                elseif ($folder == 'Корневая папка проекта'){
                    $save_path = $path;
                }
                else $save_path = $path.$folder.'/';

                foreach ($request->file('files') as $file){

                    $file->storeAs($save_path, $file->getClientOriginalName());

                }

                return response()->json([
                    'bg-class' => 'bg-success',
                    'from' => 'Система',
                    'message' => __('project.upload_files_successfully'),
                    'ajax' => view('project.ajax.project_files', [
                        'files' => $this->getFiles($project->id)
                    ])->render()
                ]);
            }

            else {
                return response()->json([
                    'bg-class' => 'bg-danger',
                    'from' => 'Система',
                    'message' => __('general.first_choose_file'),
                    'ajax' => view('project.ajax.project_files', [
                        'files' => $this->getFiles($project->id)
                    ])->render()
                ]);
            }

        }

        if ($request->action == 'update_paid_status'){

            $request->paid == 'Оплачен' ? $paid_at = Carbon::now() : $paid_at = null;

            $project->update([
                'paid' => $request->paid,
                'paid_at' => $paid_at
            ]);

            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'message' => __('project.update_paid_status_successfully')
            ]);


        }

        if ($request->action == 'update_project'){

            $request->additional_clients == '' ? $additional_clients = null : $additional_clients = serialize($request->additional_clients);

            $project->name = $request->name;
            $project->client_id = $request->client_id;
            $project->additional_clients = $additional_clients;
            $project->from = $request->from;
            $project->pogranperehod = $request->pogranperehod;
            $project->to = $request->to;
            $project->manager_id = $request->manager_id;
            $project->logist_id = $request->logist_id;
            $project->freight_info = $request->freight_info;
            $project->freight_amount = $request->freight_amount;
            $project->planned_payment_date = $request->planned_payment_date;
            $project->prepayment = $request->prepayment;
            $project->additional_info = $request->additional_info;
            isset($request->management_expenses) ? $project->management_expenses = $request->management_expenses : $project->management_expenses = null;
            $project->access_to_project = $request->access_to_project;

            if (can_edit_this_project_price($project->id) || (can_edit_this_project($project->id) && ($project->status == 'Черновик'))){

                !is_null($request->expenses_array) ? $expenses_array = serialize($request->expenses_array) : $expenses_array = null;

                ProjectExpense::where('project_id', $project->id)->update([
                    'amount' => $request->amount,
                    'currency' => $request->currency,
                    'cb_rate' => $request->cb_rate,
                    'price_1pc' => $request->price_1pc,
                    'price_in_currency' => $request->price_in_currency,
                    'price_in_rub' => $request->price_in_rub,
                    'planned_costs' => $request->planned_costs,
                    'planned_profit' => $request->planned_profit,
                    'expenses_array' => $expenses_array
                ]);
            }

            $project->save();

            return redirect()->route('project.show', $project->id)->withSuccess(__('project.update_project_successfully'));
        }

    }

    public function destroy(Project $project)
    {
        ProjectExpense::where('project_id', $project->id)->delete();

        $project->delete();

        return redirect()->back()->withSuccess(__('project.project_removed_successfully'));
    }

    public function create_plan(Project $project, $id)
    {

        $project = Project::find($id);
        $blocks = Block::where('project_id', $project['id'])->get();
        $block_items = BlockItem::all();

        if ($blocks->isEmpty()) {
            return view('project.plan.create', [
                'project' => $project,
                'block_items' => $block_items
            ]);
        } else {
            return view('project.plan.edit', [
                'project' => $project,
                'blocks' => $blocks,
                'block_items' => $block_items
            ]);
        }
    }

    public function save_plan(Request $request){

        foreach ($request->items as $item){

            $new_block = new Block();

            $new_block->name = $item;
            $new_block->project_id = $request->project_id;
            $new_block->status = 'В ожидании';

            $new_block->save();

        }

        return redirect()->back()->withSuccess(__('project.project_save_plan_successfully'));

    }

    public function getActiveProjects(Request $request)
    {

        $projects = Project::where('active','1')->where('status','<>','Черновик')->get();

        $complete_level = array();

        foreach ($projects as $project){
            $project->finance = $this->getProjectFinance($project['id']);
            $project->complete_level = $this->getProjectCompleteLevel($project['id']);

        }

        return view('project.index',[
            'projects' => $projects,
            'page_title' => __('project.getActiveProjects')
        ]);
    }

    public function getFinishedProjects(Request $request)
    {

        $projects = Project::where('active','0')->get();

        $complete_level = array();

        foreach ($projects as $project){
            $project->finance = $this->getProjectFinance($project['id']);
            $project->complete_level = $this->getProjectCompleteLevel($project['id']);

        }

        return view('project.index',[
            'projects' => $projects,
            'page_title' => __('project.getFinishedProjects')
        ]);
    }

    public function getDraftProjects()
    {

        $projects = Project::where('status', 'Черновик')->get();

        $complete_level = array();

        foreach ($projects as $project){
            $project->finance = $this->getProjectFinance($project['id']);
            $project->complete_level = $this->getProjectCompleteLevel($project['id']);

        }

        return view('project.index',[
            'projects' => $projects,
            'page_title' => __('project.getDraftProjects')
        ]);
    }

    public function getProjectBySupplier($id)
    {

        $projects_id = Invoice::select('project_id')->where('supplier_id', $id)->groupBy('project_id')->get()->toArray();

        foreach ($projects_id as $item){
            $projects [] = $item['project_id'];
        }

        $projects = Project::whereIn('id', $projects_id)->get();

        $complete_level = array();

        foreach ($projects as $project){
            $project->finance = $this->getProjectFinance($project['id']);
            $project->complete_level = $this->getProjectCompleteLevel($project['id']);
        }

        return view('project.index_table_full_render',[
            'projects' => $projects,
            'page_title' => __('project.getProjectBySupplier')
        ]);
    }

    public function finish_project(Request $request)
    {

        $project = Project::find($request->project_id);
        $old_folder = 'public/Проекты/Активные проекты/'.$project->name;
        $new_folder = 'public/Проекты/Завершенные проекты/'.$project->name;

        $invoices = $project->invoices;
        $applications = $project->applications;
        $comments = $project->comments;

        if(!is_null($invoices)){
            foreach ($invoices as $invoice){
                if(checkUploadedFileInvoice($invoice->id)['file']){
                    $file = unserialize($invoice->file);
                    $file['filename'] = str_replace('Активные проекты', 'Завершенные проекты', $file['filename']);
                    $invoice->update([
                        'file' => serialize($file)
                    ]);
                }
                else {
                    $invoice->update([
                        'file' => str_replace('Активные проекты', 'Завершенные проекты', $invoice->file)
                    ]);
                }

                if(!is_null($invoice->invoice_file)){
                    $array = $invoice->invoice_file;
                    foreach ($array as $key => $invoice_file){
                        $array[$key]['filename'] = str_replace('Активные проекты', 'Завершенные проекты', $invoice_file['filename']);
                    }
                    $invoice->update([
                        'invoice_file' => $array
                    ]);
                }

                if(!is_null($invoice->upd_file)){
                    $array = $invoice->upd_file;
                    foreach ($array as $key => $invoice_file){
                        $array[$key]['filename'] = str_replace('Активные проекты', 'Завершенные проекты', $invoice_file['filename']);
                    }
                    $invoice->update([
                        'upd_file' => $array
                    ]);
                }

                if(!is_null($invoice->payment_order_file)){
                    $array = $invoice->payment_order_file;
                    foreach ($array as $key => $invoice_file){
                        $array[$key]['filename'] = str_replace('Активные проекты', 'Завершенные проекты', $invoice_file['filename']);
                    }
                    $invoice->update([
                        'payment_order_file' => $array
                    ]);

                }

            }
        }

        if(!is_null($applications)){
            foreach ($applications as $application){
                $application->file = str_replace('Активные проекты', 'Завершенные проекты', $application->file);
                $application->save();
            }
        }

        if(!is_null($comments)){
            foreach ($comments as $comment){
                $comment->file = str_replace('Активные проекты', 'Завершенные проекты', $comment->file);
                $comment->save();
            }
        }

        $project->status = 'Завершен';
        $project->active = '0';
        $project->finished_at = Carbon::now();

        $project->save();

        if (!empty(Storage::directories($old_folder))){
            Storage::move($old_folder, $new_folder);
        }

        return redirect()->back()->withSuccess(__('project.finished_successfully'));
    }

    public function setStatusInWork(Request $request)
    {
        $project = Project::find($request->project_id);
        $old_folder = 'public/Проекты/Завершенные проекты/'.$project->name;
        $new_folder = 'public/Проекты/Активные проекты/'.$project->name;

        $invoices = $project->invoices;
        $applications = $project->applications;
        $comments = $project->comments;

        if(!is_null($invoices)){
            foreach ($invoices as $invoice){
                if(checkUploadedFileInvoice($invoice->id)['file']){
                    $file = unserialize($invoice->file);
                    $file['filename'] = str_replace('Завершенные проекты', 'Активные проекты', $file['filename']);
                    $invoice->file = serialize($file);
                    $invoice->save();
                }
                else {
                    $invoice->file = str_replace('Завершенные проекты', 'Активные проекты', $invoice->file);
                    $invoice->save();
                }

                if(!is_null($invoice->invoice_file)){
                    $array = $invoice->invoice_file;
                    foreach ($array as $key => $invoice_file){
                        $array[$key]['filename'] = str_replace('Завершенные проекты', 'Активные проекты', $invoice_file['filename']);
                    }
                    $invoice->update([
                        'invoice_file' => $array
                    ]);
                }

                if(!is_null($invoice->upd_file)){
                    $array = $invoice->upd_file;
                    foreach ($array as $key => $invoice_file){
                        $array[$key]['filename'] = str_replace('Завершенные проекты', 'Активные проекты', $invoice_file['filename']);
                    }
                    $invoice->update([
                        'upd_file' => $array
                    ]);
                }

                if(!is_null($invoice->payment_order_file)){
                    $array = $invoice->payment_order_file;
                    foreach ($array as $key => $invoice_file){
                        $array[$key]['filename'] = str_replace('Завершенные проекты', 'Активные проекты', $invoice_file['filename']);
                    }
                    $invoice->update([
                        'payment_order_file' => $array
                    ]);

                }


            }
        }

        if(!is_null($applications)){
            foreach ($applications as $application){
                $application->file = str_replace('Завершенные проекты', 'Активные проекты', $application->file);
                $application->save();
            }
        }

        if(!is_null($comments)){
            foreach ($comments as $comment){
                $comment->file = str_replace('Завершенные проекты', 'Активные проекты',  $comment->file);
                $comment->save();
            }
        }

        if (!empty(Storage::directories($old_folder))){
            Storage::move($old_folder, $new_folder);
        }

        Project::find($request->project_id)->update([
            'status' => 'В работе',
            'active' => 1
        ]);

        return redirect()->back()->withSuccess(__('project.update_paid_status_successfully'));

    }

    public function getProjectTable(ProjectFilter $filter, Request $request){

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
        $totalRecords = Project::filter($filter);
        if($request->data_range != ''){
            $totalRecords->whereDate('finished_at', '>=', $range_from)
                ->whereDate('finished_at', '<=', $range_to);
        }
        $totalRecords = $totalRecords->count();

        if($searchValue != ''){
            $totalRecordswithFilter = Project::where('projects.name', 'like', '%' . $searchValue . '%')
                ->orWhere('projects.from', 'like', '%' . $searchValue . '%')
                ->orWhere('projects.to', 'like', '%' . $searchValue . '%')
                ->orWhere('projects.additional_info', 'like', '%' . $searchValue . '%')
                ->orWhere('projects.status', 'like', '%' . $searchValue . '%')
                ->orWhere('projects.created_at', 'like', '%' . $searchValue . '%')
                ->orWhere('projects.finished_at', 'like', '%' . $searchValue . '%')
                ->orWhereHas('client', function ($q) use($searchValue)
                {
                    $q->where('name', 'like', '%' . $searchValue . '%');
                })
                ->orWhereHas('user', function ($q) use($searchValue)
                {
                    $q->where('name', 'like', '%' . $searchValue . '%');
                });
                if($request->data_range != ''){
                    $totalRecordswithFilter->whereDate('finished_at', '>=', $range_from)
                        ->whereDate('finished_at', '<=', $range_to);
                }
            $totalRecordswithFilter = $totalRecordswithFilter->filter($filter)
            ->count();


            // Fetch records
            $records = Project::where('projects.name', 'like', '%' . $searchValue . '%')
                ->orWhere('projects.from', 'like', '%' . $searchValue . '%')
                ->orWhere('projects.to', 'like', '%' . $searchValue . '%')
                ->orWhere('projects.additional_info', 'like', '%' . $searchValue . '%')
                ->orWhere('projects.status', 'like', '%' . $searchValue . '%')
                ->orWhere('projects.created_at', 'like', '%' . $searchValue . '%')
                ->orWhere('projects.finished_at', 'like', '%' . $searchValue . '%')
                ->orWhereHas('client', function ($q) use($searchValue)
                {
                    $q->where('name', 'like', '%' . $searchValue . '%');
                })
                ->orWhereHas('user', function ($q) use($searchValue)
                {
                    $q->where('name', 'like', '%' . $searchValue . '%');
                })
                ->orWhereHas('manager', function ($q) use($searchValue)
                {
                    $q->where('name', 'like', '%' . $searchValue . '%');
                })
                ->orWhereHas('logist', function ($q) use($searchValue)
                {
                    $q->where('name', 'like', '%' . $searchValue . '%');
                });

                if($request->data_range != ''){
                    $records->whereDate('finished_at', '>=', $range_from)
                        ->whereDate('finished_at', '<=', $range_to);
                }

                $records = $records
                ->filter($filter)
                ->orderBy($columnName, $columnSortOrder)
                ->select('projects.*')
                ->skip($start)
                ->take($rowperpage)
                ->get();
        }
        else {
            $totalRecordswithFilter = Project::filter($filter);
            if($request->data_range != ''){
                $totalRecordswithFilter->whereDate('finished_at', '>=', $range_from)
                    ->whereDate('finished_at', '<=', $range_to);
            }
            $totalRecordswithFilter = $totalRecordswithFilter->count();

            $records = Project::orderBy($columnName, $columnSortOrder);

            if($request->data_range != ''){
                $records->whereDate('finished_at', '>=', $range_from)
                    ->whereDate('finished_at', '<=', $range_to);
            }

            $records = $records
                ->filter($filter)
                ->select('projects.*')
                ->skip($start)
                ->take($rowperpage)
                ->get();
        }


        $data_arr = array();

        $sno = $start + 1;

        foreach ($records as $project) {

            $project->finance = $this->getProjectFinance($project['id']);
            $project->complete_level = $this->getProjectCompleteLevel($project['id']);

            $id = $project->id;

            $name = view('project.table.name', [
                'project' => $project
            ])->render();

            $info = view('project.table.info', [
                'project' => $project
            ])->render();

            $finance = view('project.table.finance', [
                'project' => $project
            ])->render();

            $status = view('project.table.status', [
                'project' => $project
            ])->render();

            $actions = view('project.table.actions', [
                'project' => $project
            ])->render();


            $data_arr[] = array(
                "id" => $id,
                "name" => $name,
                "client_id" => $info,
                "freight_amount" => $finance,
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

    public function update_project_finance($id) {

        $this->updateProjectFinance($id);

        return redirect()->back()->withSuccess(__('project.update_project_finance'));

    }

    public function deleteRow($id){

        $project = Project::find($id);

        ProjectExpense::where('project_id', $project->id)->delete();
        $project->delete();

        return response()->json([
            'bg-class' => 'bg-success',
            'from' => 'Система',
            'message' =>  __('project.project_successfully_removed', ['name' => $project->name])
        ]);
    }

    public function checkProjectNameFree(Request $request){

        Project::where('name', $request->name)->count() == 0 ? $name_free = true : $name_free = false;

        return $name_free;

    }

    public function removeFromStatView(){

        return view('settings.project_remove_from_stat', [
            'projects' => Project::all()
        ]);
    }

    public function removeFromStat(Request $request){
        $projects = Project::all();

        if(is_null($request->projects)){
            foreach ($projects as $project){
                $project->update([
                    'remove_from_stat' => null
                ]);
            }
        }
        else {
            foreach ($projects as $project){
                if(in_array($project->id, $request->projects)){
                    $project->update([
                        'remove_from_stat' => '1'
                    ]);
                }
                else {
                    $project->update([
                        'remove_from_stat' => null
                    ]);
                }

            }
        }

        return redirect()->back()->withSuccess(__('project.remove_from_stat_successfully'));
    }

}
