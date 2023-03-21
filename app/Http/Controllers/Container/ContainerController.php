<?php

namespace App\Http\Controllers\Container;

use App\Filters\ContainerFilter;
use App\Http\Controllers\Controller;
use App\Http\Traits\ContainerTrait;
use App\Http\Traits\FinanceTrait;
use App\Models\Container;
use App\Models\ContainerGroup;
use App\Models\ContainerProblem;
use App\Models\ContainerUsageStatistic;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ContainerController extends Controller
{
    use ContainerTrait;
    use FinanceTrait;

    public function index(ContainerFilter $request)
    {
        return view('container.index');
    }

    public function create()
    {
        return view('container.create', [
            'suppliers' => Supplier::orderBy('created_at', 'desc')->get()
        ]);
    }

    public function store(Request $request)
    {

        $new_container = new Container();

        $new_container->name = $request->name;
        $new_container->size = $request->size;
        $new_container->type = $request->type;
        $new_container->start_date_for_client = $request->start_date_for_client;
        $new_container->start_date_for_us = $request->start_date_for_us;
        $new_container->border_date = $request->border_date;
        $new_container->grace_period_for_client = $request->grace_period_for_client;
        $new_container->grace_period_for_us = $request->grace_period_for_us;
        $new_container->snp_amount_for_client = $request->snp_amount_for_client;
        $new_container->snp_amount_for_us = $request->snp_amount_for_us;
        $new_container->snp_currency = $request->snp_currency;
        $new_container->svv = $request->svv;

        $new_container->supplier_id = $request->supplier_id;
        $new_container->additional_info = $request->additional_info;

        !is_null($request->snp_client_array)
            ? $new_container->snp_range_for_client = serialize($request->snp_client_array)
            : $new_container->snp_range_for_client = null;

        !is_null($request->snp_us_array)
            ? $new_container->snp_range_for_us = serialize($request->snp_us_array)
            : $new_container->snp_range_for_us = null;

        $new_container->save();

        return redirect()->back()->withSuccess(__('container.added_successfully'));
    }

    public function show(Container $container)
    {
        $container->usage_dates = $this->getContainerUsageDates($container->id);
        if($container->type == 'В собственности'){
            $view = 'container.show_own';
            $container_projects = $container->container_projects;

            foreach ($container_projects as $project){
                $project->info = $this->getContainerProjectInfo($project->id);
            }

            $container->info = $this->getOwnContainerFinance($container->id);

        }
        else {
            $view = 'container.show';
            $container_projects = null;
        }
        return view($view, [
            'container' => $container,
            'columns' => $this->columns
        ]);
    }

    public function edit(Container $container)
    {
        return view('container.edit', [
            'suppliers' => Supplier::orderBy('created_at', 'desc')->get(),
            'container' => $container
        ]);
    }

    public function update(Request $request, $id)
    {
        if ($request->action == 'return_container') {

            $container = Container::find($id);

            $project_id = $container->project_id;

            $groups = ContainerGroup::where('project_id', $container->project_id)->get();

            $group=[];

            foreach ($groups as $all_groups){
                foreach (unserialize($all_groups->containers) as $group_container){
                    $group_container_object = Container::find($group_container);
                    if(!empty($group_container_object)){
                        if($group_container_object->id == $id) {
                            $group = $all_groups;
                            break;
                        }
                    }
                }
            }

            $new_container_stat = new ContainerUsageStatistic();

            $usage_dates = $this->getContainerUsageDates($id);

            $new_container_stat->container_id = $container->id;
            $new_container_stat->project_id = $container->project_id;
            $new_container_stat->start_date_for_us = $container->start_date_for_us;
            $new_container_stat->start_date_for_client = $container->start_date_for_client;
            $new_container_stat->border_date = $container->border_date;
            $new_container_stat->return_date = Carbon::now()->format('Y-m-d');
            $new_container_stat->svv = $usage_dates['svv_date'];
            $new_container_stat->snp_days_for_client = $usage_dates['overdue_days'];
            $new_container_stat->snp_days_for_us = $usage_dates['overdue_days_for_us'];
            $new_container_stat->snp_total_amount_for_client = $usage_dates['snp_amount_for_client'];
            $new_container_stat->snp_total_amount_for_us = $usage_dates['snp_amount_for_us'];
            $new_container_stat->snp_currency = $usage_dates['snp_currency'];


            $new_container_stat->save();

            if($container->type == 'Аренда') $container->archive = 'yes';

            $container->project_id = null;
            $container->seal = null;
            $container->start_date_for_us = null;
            $container->start_date_for_client = null;
            $container->border_date = null;

            $container->save();

            $containers_list = array();

            $containers = unserialize($group->containers);

            foreach ($containers as $container){
                $container = Container::find($container);
                if(!empty($container)){
                    $container->usage_statistic = ContainerUsageStatistic::where('project_id', $group->project_id)->where('container_id', $container->id)->get();
                    $container->usage_dates = $this->getContainerUsageDates($container->id);
                    $containers_list[] = $container;
                }

            }

            $group->containers_list = $containers_list;

            $this->updateProjectFinance($project_id);

            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'group_id' => $group->id,
                'table' => view('project.layouts.containers_table', [
                    'group' => $group
                ])->render(),
                'message' => __('container.return_successfully')
            ]);

        }

        if ($request->action == 'prolong_svv') {

            $container = Container::find($id);

            $container->svv = $request->svv_prolong_to;

            $container->save();

            return redirect()->back()->withSuccess(__('container.svv_updated_successfully'));

        }

        if ($request->action == 'start_use_container_for_us') {

            $container = Container::find($id);

            $container->start_date_for_us = Carbon::now()->format('Y-m-d');

            $container->save();

            if($request->static == 'yes'){
                return redirect()->back()->withSuccess(__('container.start_use_container_for_us'));
            }
            else {
                return response()->json([
                    'bg-class' => 'bg-success',
                    'from' => 'Система',
                    'message' => __('container.start_use_container_for_us')
                ]);
            }

        }
        if ($request->action == 'start_use_container_for_client') {

            $container = Container::find($id);

            $container->start_date_for_client = Carbon::now()->format('Y-m-d');

            $container->save();

            if($request->static == 'yes'){
                return redirect()->back()->withSuccess(__('container.start_use_container_for_client'));
            }
            else {
                return response()->json([
                    'bg-class' => 'bg-success',
                    'from' => 'Система',
                    'message' => __('container.start_use_container_for_us')
                ]);
            }


        }

        if ($request->action == 'make_seal') {

            $container = Container::find($id);

            $container->seal = $request->seal;

            $container->save();

            return redirect()->back()->withSuccess(__('container.make_seal'));

        }

        if ($request->action == 'edit_container') {

            $container = Container::find($id);

            $container->name = $request->name;
            $container->size = $request->size;
            $container->type = $request->type;
            $container->start_date_for_us = $request->start_date_for_us;
            $container->start_date_for_client = $request->start_date_for_client;
            $container->border_date = $request->border_date;
            $container->grace_period_for_client = $request->grace_period_for_client;
            $container->grace_period_for_us = $request->grace_period_for_us;
            $container->snp_amount_for_client = $request->snp_amount_for_client;
            $container->snp_amount_for_us = $request->snp_amount_for_us;
            $container->snp_currency = $request->snp_currency;
            $container->svv = $request->svv;

            $container->supplier_id = $request->supplier_id;
            $container->additional_info = $request->additional_info;

            !is_null($request->snp_client_array)
                ? $container->snp_range_for_client = serialize($request->snp_client_array)
                : $container->snp_range_for_client = null;

            !is_null($request->snp_us_array)
                ? $container->snp_range_for_us = serialize($request->snp_us_array)
                : $container->snp_range_for_us = null;

            $container->save();

            return redirect()->route('container.show', $container->id)->withSuccess(__('container.info_updated_successfully'));

        }

    }

    public function destroy(Container $container)
    {
        $container->delete();
        return redirect()->back()->withSuccess(__('container.deleted'));
    }

    public function makeReturn($id)
    {

        $container = Container::find($id);
        return $container->name;

    }

    public function getContainer(ContainerFilter $filter, Request $request)
    {
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
        $totalRecords = Container::filter($filter);
        if($request->filter != 'archive') $totalRecords->whereNull('archive');
        $totalRecords = $totalRecords->count();
        if($searchValue != ''){
            $totalRecordswithFilter = Container::filter($filter)
                ->where('containers.name', 'like', '%' . $searchValue . '%')
                ->orwhere('containers.id', 'like', '%' . $searchValue . '%')
                ->orWhere('containers.country', 'like', '%' . $searchValue . '%')
                ->orWhere('containers.city', 'like', '%' . $searchValue . '%')
                ->orWhereHas('supplier', function ($q) use($searchValue)
                {
                    $q->where('name', 'like', '%' . $searchValue . '%');
                })
                ->orWhereHas('project', function ($q) use($searchValue)
                {
                    $q->where('name', 'like', '%' . $searchValue . '%');
                });

                if($request->filter != 'archive') $totalRecordswithFilter->whereNull('archive');

            $totalRecordswithFilter = $totalRecordswithFilter->count();


            // Fetch records
            $records = Container::filter($filter)->orderBy($columnName, $columnSortOrder)
                ->where('containers.name', 'like', '%' . $searchValue . '%')
                ->orwhere('containers.id', 'like', '%' . $searchValue . '%')
                ->orWhere('containers.country', 'like', '%' . $searchValue . '%')
                ->orWhere('containers.city', 'like', '%' . $searchValue . '%')
                ->orWhereHas('supplier', function ($q) use($searchValue)
                {
                    $q->where('name', 'like', '%' . $searchValue . '%');
                })
                ->orWhereHas('project', function ($q) use($searchValue)
                {
                    $q->where('name', 'like', '%' . $searchValue . '%');
                });
                if($request->filter != 'archive') $records->whereNull('archive');
            $records = $records->select('containers.*')
                ->skip($start)
                ->take($rowperpage)
                ->get();
        }
        else {
            $totalRecordswithFilter = Container::filter($filter);
            if($request->filter != 'archive') $totalRecordswithFilter->whereNull('archive');
            $totalRecordswithFilter = $totalRecordswithFilter->count();


            // Fetch records
            $records = Container::filter($filter)->orderBy($columnName, $columnSortOrder);
            if($request->filter != 'archive')
                $records->whereNull('archive');
            $records = $records->select('containers.*')
                ->skip($start)
                ->take($rowperpage)
                ->get();
        }


        $data_arr = array();

        $sno = $start + 1;

        foreach ($records as $container) {
            $container->usage_dates = $this->getContainerUsageDates($container->id);

            $id = $container->id;

            $name = view('container.table.name', [
                'container' => $container
            ])->render();

            $usage = view('container.table.usage', [
                'container' => $container
            ])->render();

            $conditions = view('container.table.conditions', [
                'container' => $container
            ])->render();

            $place = view('container.table.place', [
                'container' => $container
            ])->render();

            $actions = view('container.table.actions', [
                'container' => $container
            ])->render();


            $data_arr[] = array(
                "id" => $id,
                "name" => $name,
                "start_date" => $usage,
                "grace_period_for_client" => $conditions,
                "country" => $place,
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

    public function deleteRow($id){

        $container = Container::find($id);
        $name = $container->name;

        $container->delete();

        return response()->json([
            'bg-class' => 'bg-success',
            'from' => 'Система',
            'message' => __('container.deleted_successfully', ['name' => $name])
        ]);
    }

    public function update_list(Request $request){

        if($request->containers_list != ''){

            $fields_for_update = [];
            $fields_for_null = [];
            $fields = [];

            foreach ($request->all() as $key => $value){
                if(!is_null($value) && !in_array($key, ['_token', 'containers_list', 'null_array'])){
                    $fields_for_update [] = [
                        $key => $value,
                    ];
                }
            }

            if(!is_null($request->null_array)){
                foreach ($request->null_array as $key => $value){
                    if(in_array($key, ['supplier_terminal_storage', 'supplier_renewal_reexport_costs', 'supplier_repair', 'relocation_repair', 'client_repair'])){
                        $fields_for_null [] = [
                            $key.'_amount' => null,
                        ];
                        $fields_for_null [] = [
                            $key.'_currency' => null,
                        ];
                    }
                    else {
                        $fields_for_null [] = [
                            $key => null,
                        ];
                    }
                }
            }

            $fields_for_update = call_user_func_array('array_merge', $fields_for_update);
            $fields_for_null = call_user_func_array('array_merge', $fields_for_null);
            $fields = array_merge($fields_for_update, $fields_for_null);

            if(!empty($fields)){
                foreach (explode(',', $request->containers_list) as $container_id){
                    DB::transaction(function() use($container_id, $fields) {
                        Container::where(['id' => $container_id])
                            ->update($fields);
                    });
                    $this->updateUsageInfo(Container::find($container_id));
                }
            }
            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'message' => 'Информация была успешно обновлена'
            ]);
        }
        else {
            return response()->json([
                'bg-class' => 'bg-danger',
                'from' => 'Система',
                'message' => 'Контейнеры для редактирования не были выбраны'
            ]);
        }
    }

    public function checkContainersProcessing(Request $request){

        $exclude_from_list = [];
        $exclude_from_list_id = [];
        $chosen_containers_id = [];
        $containers_names = '';
        $excluded_containers_exist = false;

        if(!is_null($request->chosen_containers_id)){
            foreach ($request->chosen_containers_id as $id){
                $container = Container::find($id);
                if(!is_null($container->processing)){
                    if($container->processing != auth()->user()->name){
                        $exclude_from_list [] = [
                            'name' => $container->name,
                            'user' => $container->processing,
                            'reason' => 'Редактируется'
                        ];
                        $exclude_from_list_id [] = $id;
                    }
                }
//                if(!is_null($container->removed)){
//                    $exclude_from_list [] = [
//                        'name' => $container->name,
//                        'user' => $container->processing,
//                        'reason' => 'Удален'
//                    ];
//                    $exclude_from_list_id [] = $id;
//                }
            }

            $chosen_containers_id = array_diff($request->chosen_containers_id, $exclude_from_list_id);

            if(!empty($chosen_containers_id)) $this->markContainersProcessing($chosen_containers_id);

            $containers_names = implode(', ', Container::whereIn('id', $chosen_containers_id)->pluck('name')->toArray());

            if(count($exclude_from_list_id) > 0) $excluded_containers_exist = true;

        }
            return [
                'view' => view('container.modal.ajax.containers_list', [
                    'exclude_from_list' => $exclude_from_list,
                    'chosen_containers_id' => $chosen_containers_id,
                    'containers_names' => $containers_names,
                    'excluded_containers_exist' => $excluded_containers_exist
                ])->render(),
                'chosen_containers_list' => implode(',', $chosen_containers_id),
                'excluded_containers_exist' => $excluded_containers_exist,
                'chosen_containers_id' => array_values($chosen_containers_id),
            ];

        }

    public function markContainersProcessing($list){
        if(!is_null($list)){
            Container::whereIn('id', $list)->update([
                'processing' => auth()->user()->name
            ]);
        }
    }

    public function unmarkContainersProcessing(Request $request){
        if(!is_null($request->list)){
            Container::whereIn('id', $request->list)->update([
                'processing' => null
            ]);
        }
    }

    public function checkProcessing(){
        $containers_by_me = Container::where('processing', auth()->user()->name)->get();
        $containers_by_me->isEmpty() ? $processing_by_me = 'no' : $processing_by_me = 'yes';
        $containers = Container::whereNotNull('processing')->get();
        $containers->isEmpty() ? $processing = 'no' : $processing = 'yes';

        return [
            'processing_by_me' => $processing_by_me,
            'processing' => $processing
        ];
    }

    public function unblockProcessingByMe(){
        Container::where('processing', auth()->user()->name)->update([
            'processing' => null
        ]);
    }

    public function unblockProcessing(){
        Container::whereNotNull('processing')->update([
            'processing' => null
        ]);
    }

}
