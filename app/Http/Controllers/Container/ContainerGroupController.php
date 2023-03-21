<?php

namespace App\Http\Controllers\Container;

use App\Http\Controllers\Controller;
use App\Http\Traits\ContainerTrait;
use App\Models\Container;
use App\Models\ContainerGroup;
use App\Models\ContainerProject;
use App\Models\ContainerUsageStatistic;
use App\Models\Project;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\Console\Input\Input;

class ContainerGroupController extends Controller
{
    Use ContainerTrait;

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Project $project
     * @return Factory|View
     */
    public function create(Project $project)
    {
        $container_groups = ContainerGroup::where('project_id', $project['id'])->get();
        $all_containers = Container::whereNull('project_id')->orderBy('created_at','desc')->get();

        foreach ($container_groups as $group){
            $containers = unserialize($group->containers);

            foreach ($containers as $container){
                $containers_list[] = Container::find($container);
            }

            $group->containers_list = $containers_list;
            $containers_list = array();

        }

        return view('project.container.choose_containers',[
            'project' => $project,
            'container_groups' => $container_groups,
            'all_containers' => $all_containers
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Project $project
     * @param Request $request
     * @return void
     */
    public function store(Project $project, Request $request)
    {
        if ($request->type == 'new_group'){
            $new_container_group = new ContainerGroup();

            $new_container_group->name = $request->name;
            $new_container_group->project_id = $project->id;
            $new_container_group->containers = serialize($request->chosen_containers);
            $new_container_group->additional_info = $request->additional_info;

            $new_container_group->save();

            foreach ($request->chosen_containers as $container){
                $container = Container::find($container);

                if(!is_null($container->archive)) $container->update([
                    'archive' => null
                ]);

                $container->project_id = $project->id;
                $container->save();
            }

            return redirect()->back()->withSuccess(__('container.new_group_successfully'));

        }

        if($request->type == 'add_to_group'){

            $container_group = ContainerGroup::find($request->chosen_group_to_add);

            $containers = array_merge(unserialize($container_group->containers), $request->chosen_containers);

            $container_group->containers = serialize($containers);

            $container_group->save();

            foreach ($request->chosen_containers as $container){
                $container = Container::find($container);
                $container->project_id = $project->id;
                $container->save();
            }

            return redirect()->back()->withSuccess(__('container.add_to_group_successfully'));

        }

    }

    /**
     * Display the specified resource.
     *
     * @param ContainerGroup $containerGroup
     * @return Factory|View
     */
    public function show(ContainerGroup $containerGroup)
    {
        $containers_list = unserialize($containerGroup->containers);

        foreach ($containers_list as $container){

            $container_from_group = Container::find($container);

                $container_from_group->usage_dates = $this->getContainerUsageDates($container_from_group->id);
                $container_from_group->usage_statistic = ContainerUsageStatistic::where('container_id', $container_from_group->id)->get();


            $container_names [] = $container_from_group->name;

            $containers [] = $container_from_group;
        }

        return view('container.group.container_group__show',[
            'container_group' => $containerGroup,
            'containers' => $containers,
            'containers_list'=> implode(', ', $container_names)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Project $project
     * @param ContainerGroup $containerGroup
     * @return void
     */
    public function update(Project $project, ContainerGroup $containerGroup, Request $request)
    {

        if ($request->action == 'delete_from_list'){
            $group = ContainerGroup::find($containerGroup->id);
            $containers = unserialize($group->containers);

            if(($key = array_search($request->container_id, $containers)) !== false){
                unset($containers[$key]);
            }

            $group->containers = serialize($containers);

            $group->save();

            $container = Container::find($request->container_id);
            $container->project_id = null;
            $container->save();

            return redirect()->back()->withSuccess(__('container.delete_from_list_successfully'));
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ContainerGroup $containerGroup
     * @return Response
     * @throws \Exception
     */
    public function destroy(Request $request)
    {
        $group = ContainerGroup::find($request->list_id);

        $containers_list = unserialize($group->containers);

        foreach ($containers_list as $container){
            if(!is_null(Container::find($container))) Container::find($container)->update(['project_id'=>null]);
        }

        $group->delete();

        return redirect()->back()->withSuccess(__('container.group_deleted_successfully'));
    }

    public function container_group_actions(Request $request){

        if ($request->action == 'start_usage_date'){

            $group = ContainerGroup::find($request->container_group_id);
            $group->start_date = Carbon::now()->format('Y-m-d');
            $group->save();

            $containers_list = array();

            $containers = unserialize($group->containers);

            foreach ($containers as $container){
                $container = Container::find($container);
                $container->start_date_for_client = Carbon::now()->format('Y-m-d');
                $container->save();

                $container->usage_statistic = ContainerUsageStatistic::where('project_id', $group->project_id)->where('container_id', $container->id)->get();
                $container->usage_dates = $this->getContainerUsageDates($container->id);
                $containers_list[] = $container;
            }

            $group->containers_list = $containers_list;

            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'message' => __('container.start_usage_date_successfully'),
                'group_id' => $group->id,
                'table' => view('project.layouts.containers_table', [
                    'group' => $group
                ])->render()
            ]);


        }

        if ($request->action == 'border_date'){

            $group = ContainerGroup::find($request->container_group_id);
            $group->border_date = Carbon::now()->format('Y-m-d');
            $group->save();

            $containers_list = array();

            $containers = unserialize($group->containers);

            foreach ($containers as $container){
                $container = Container::find($container);
                $container->border_date = Carbon::now()->format('Y-m-d');
                $container->save();

                $container->usage_statistic = ContainerUsageStatistic::where('project_id', $group->project_id)->where('container_id', $container->id)->get();
                $container->usage_dates = $this->getContainerUsageDates($container->id);
                $containers_list[] = $container;
            }

            $group->containers_list = $containers_list;

            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'message' => __('container.border_date_successfully'),
                'group_id' => $group->id,
                'table' => view('project.layouts.containers_table', [
                    'group' => $group
                ])->render()
            ]);

        }

    }

    public function uploadList (Request $request)
    {
        $error_found = false;
        $containers_id = [];

        if($request->hasFile('containers_list')) {

            $file = \PhpOffice\PhpSpreadsheet\IOFactory::load($request->containers_list);
            $worksheet = $file->getActiveSheet();
            $rows = [];
            foreach ($worksheet->getRowIterator() AS $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(FALSE); // This loops through all cells,
                $cells = [];
                foreach ($cellIterator as $cell) {
                    $cells[] = $cell->getValue();
                }
                $rows[] = $cells;
            }

            $isFirst = true;

            $i = 0;
            foreach ($rows as $row){

                if ($isFirst)
                {
                    $isFirst = false;
                    continue;
                }

                $name = str_replace(' ', '', $row[0]);
                if ($name !='') {
                    if (!is_null($name) && $name != 'null') {
                        $list[$i]['name'] = $name;
                    } else {
                        $error_found = true;
                        $message = __('container.name_not_correct', ['name' => $name]);
                        break;
                    }

                    if ($row[1] != '') {
                        if ($row[1] == 'null') {
                            $supplier_id = 'null';
                            $list[$i]['supplier_id'] = '';
                        } else {
                            $supplier_id = preg_replace("/[^0-9]/", '', $row[1]);
                            $supplier = Supplier::find($supplier_id);
                            if (!is_null($supplier)) {
                                $list[$i]['supplier_id'] = $supplier_id;
                            } else {
                                $error_found = true;
                                $message = __('container.not_found_supplier', ['name' => $name]);
                                break;
                            }
                        }
                    } else {
                        $supplier_id = '';
                    }

                    if ($row[2] == 'null') {
                        $grace_period_for_client = 'null';
                        $list[$i]['grace_period_for_client'] = '';
                    } else {
                        $grace_period_for_client = preg_replace("/[^0-9]/", '', $row[2]);
                        $list[$i]['grace_period_for_client'] = $grace_period_for_client;
                    }

                    if ($row[3] == 'null') {
                        $grace_period_for_us = 'null';
                        $list[$i]['grace_period_for_us'] = '';
                    } else {
                        $grace_period_for_us = preg_replace("/[^0-9]/", '', $row[3]);
                        $list[$i]['grace_period_for_us'] = $grace_period_for_us;
                    }

                    if ($row[4] != '') {
                        if ($row[4] == 'null') {
                            $svv = 'null';
                            $list[$i]['svv'] = '';
                        } else {
                            try {
                                $svv = Carbon::parse($row[4])->format('Y-m-d');
                            } catch (\Exception $e) {
                                $error_found = true;
                                $message = __('container.not_correct_svv_format', ['name' => $name]);
                                break;
                            }

                            if ($svv) {
                                $list[$i]['svv'] = $svv;
                            }
                        }
                    } else {
                        $svv = $row[4];
                        $list[$i]['svv'] = $svv;
                    }

                    if ($row[5] != '') {
                        if ($row[5] == 'null') {
                            $snp_currency = 'null';
                            $list[$i]['snp_currency'] = '';
                        } else {
                            $snp_currency = str_replace(' ', '', $row[5]);

                            if (!in_array($snp_currency, ['USD', 'CNY', 'RUB'])) {
                                $error_found = true;
                                $message = __('container.not_correct_currency_format', ['name' => $name]);
                                break;
                            } else {
                                $list[$i]['snp_currency'] = $snp_currency;
                            }
                        }
                    } else {
                        $snp_currency = $row[5];
                        $list[$i]['snp_currency'] = $snp_currency;
                    }

                    //диапазон для клиента 6

                    if ($row[6] == 'null') {
                        $snp_range_for_client = 'null';
                        $list[$i]['snp_range_for_client'] = '';
                    } else {
                        $snp_range_string = str_replace(' ', '', $row[6]);
                        if($snp_range_string != ''){
                            $snp_range_array = explode(',', $snp_range_string);
                            $snp_range_for_client = null;

                            foreach ($snp_range_array as $range_item){
                                $value = explode(':', $range_item);
                                $snp_range_for_client [] = [
                                    'range' => $value[0],
                                    'price' => $value[1]
                                ];
                            }
                            if(!is_null($snp_range_for_client)){
                                $list[$i]['snp_range_for_client'] = serialize($snp_range_for_client);
                                $snp_range_for_client = serialize($snp_range_for_client);
                            }
                            else
                            {
                                $list[$i]['snp_range_for_client'] = null;
                                $snp_range_for_client = null;
                            }
                        }
                        else {
                            $snp_range_for_client = null;
                            $list[$i]['snp_range_for_client'] = $snp_range_for_client;
                        }

                    }

                    if ($row[7] == 'null') {
                        $snp_amount_for_client = 'null';
                        $list[$i]['snp_amount_for_client'] = '';
                    } else {
                        $snp_amount_for_client = preg_replace("/[^0-9\.]/", '', $row[7]);
                        $list[$i]['snp_amount_for_client'] = $snp_amount_for_client;
                    }

                    //диапазон для нас 8

                    if ($row[8] == 'null') {
                        $snp_range_for_us = 'null';
                        $list[$i]['snp_range_for_us'] = '';
                    } else {
                        $snp_range_string = str_replace(' ', '', $row[8]);
                        if($snp_range_string != ''){
                            $snp_range_array = explode(',', $snp_range_string);
                            $snp_range_for_us = null;

                            foreach ($snp_range_array as $range_item){
                                $value = explode(':', $range_item);
                                $snp_range_for_us [] = [
                                    'range' => $value[0],
                                    'price' => $value[1]
                                ];
                            }
                            if(!is_null($snp_range_for_us)){
                                $list[$i]['snp_range_for_us'] = serialize($snp_range_for_us);
                                $snp_range_for_us = serialize($snp_range_for_us);
                            }
                            else {
                                $list[$i]['snp_range_for_us'] = null;
                                $snp_range_for_us = null;
                            }
                        }
                        else {
                            $snp_range_for_us = null;
                            $list[$i]['snp_range_for_us'] = $snp_range_for_us;
                        }

                    }

                    if ($row[9] == 'null') {
                        $snp_amount_for_us = 'null';
                        $list[$i]['snp_amount_for_us'] = '';
                    } else {
                        $snp_amount_for_us = preg_replace("/[^0-9\.]/", '', $row[9]);
                        $list[$i]['snp_amount_for_us'] = $snp_amount_for_us;
                    }

                    if ($row[10] != '') {
                        if ($row[10] == 'null') {
                            $start_date_for_us = 'null';
                            $list[$i]['start_date_for_us'] = '';
                        } else {
                            try {
                                $start_date_for_us = Carbon::parse($row[10])->format('Y-m-d');
                            } catch (\Exception $e) {
                                $error_found = true;
                                $message = __('container.not_correct_start_date_for_us_format', ['name' => $name]);
                                break;
                            }

                            if ($start_date_for_us) {
                                $list[$i]['start_date_for_us'] = $start_date_for_us;
                            }
                        }

                    } else {
                        $start_date_for_us = $row[8];
                        $list[$i]['start_date_for_us'] = $start_date_for_us;
                    }

                    if ($row[11] != '') {
                        if ($row[11] == 'null') {
                            $start_date_for_client = 'null';
                            $list[$i]['start_date_for_client'] = '';
                        } else {
                            try {
                                $start_date_for_client = Carbon::parse($row[11])->format('Y-m-d');
                            } catch (\Exception $e) {
                                $error_found = true;
                                $message = __('container.not_correct_start_date_for_client_format', ['name' => $name]);
                                break;
                            }

                            if ($start_date_for_client) {
                                $list[$i]['start_date_for_client'] = $start_date_for_client;
                            }
                        }

                    } else {
                        $start_date_for_client = $row[11];
                        $list[$i]['start_date_for_client'] = $start_date_for_client;
                    }

                    if ($row[12] != '') {
                        $type = $row[12];

                        if (!in_array($type, ['В собственности', 'Аренда'])) {
                            $error_found = true;
                            $message = __('container.not_correct_type_format', ['name' => $name]);
                            break;
                        } else {
                            $list[$i]['type'] = $type;
                        }
                    } else {
                        $type = $row[12];
                        $list[$i]['type'] = $type;
                    }

                    $edits[$i] = $list[$i];

                    $container = Container::where('name', $name)->first();

                    if (is_null($container)) {
                        $list[$i]['add_new'] = true;
                        $edits[$i]['add_new'] = true;
                    } else {
                        $list[$i]['add_new'] = false;
                        $edits[$i]['add_new'] = false;

                        $container->grace_period_for_client == $grace_period_for_client || $grace_period_for_client == ''
                            ? $list[$i]['grace_period_for_client'] = $container->grace_period_for_client
                            : $list[$i]['update_grace_period_for_client'] = true;

                        $container->grace_period_for_us == $grace_period_for_us || $grace_period_for_us == ''
                            ? $list[$i]['grace_period_for_us'] = $container->grace_period_for_us
                            : $list[$i]['update_grace_period_for_us'] = true;

                        $container->type == $type || $type == ''
                            ? $list[$i]['type'] = $container->type
                            : $list[$i]['update_type'] = true;

                        $container->supplier_id == $supplier_id || $supplier_id == ''
                            ? $list[$i]['supplier_id'] = $container->supplier_id
                            : $list[$i]['update_supplier_id'] = true;

                        $container->svv == $svv || $svv == ''
                            ? $list[$i]['svv'] = $container->svv
                            : $list[$i]['update_svv'] = true;

                        $container->snp_currency == $snp_currency || $snp_currency == ''
                            ? $list[$i]['snp_currency'] = $container->snp_currency
                            : $list[$i]['update_snp_currency'] = true;
                        $container->snp_range_for_client == $snp_range_for_client || $snp_range_for_client == ''
                            ? $list[$i]['snp_range_for_client'] = $container->snp_range_for_client
                            : $list[$i]['update_snp_range_for_client'] = true;

                        $container->snp_amount_for_client == $snp_amount_for_client || $snp_amount_for_client == ''
                            ? $list[$i]['snp_amount_for_client'] = $container->snp_amount_for_client
                            : $list[$i]['update_snp_amount_for_client'] = true;

                        $container->snp_range_for_us == $snp_range_for_us || $snp_range_for_us == ''
                            ? $list[$i]['snp_range_for_us'] = $container->snp_range_for_us
                            : $list[$i]['update_snp_range_for_us'] = true;

                        $container->snp_amount_for_us == $snp_amount_for_us || $snp_amount_for_us == ''
                            ? $list[$i]['snp_amount_for_us'] = $container->snp_amount_for_us
                            : $list[$i]['update_snp_amount_for_us'] = true;

                        $container->start_date_for_client == $start_date_for_client || $start_date_for_client == ''
                            ? $list[$i]['start_date_for_client'] = $container->start_date_for_client
                            : $list[$i]['update_start_date_for_client'] = true;

                        $container->start_date_for_us == $start_date_for_us || $start_date_for_us == ''
                            ? $list[$i]['start_date_for_us'] = $container->start_date_for_us
                            : $list[$i]['update_start_date_for_us'] = true;

                    }

                    $i++;
                }
            }

            if (!$error_found){

                foreach ($list as $item) {

                    if ($item['add_new']) {

                        $new_container = new Container();

                        foreach ($item as $key => $value) {
                            if (!in_array($key, ['add_new', 'make_return','return_date', 'supplier'])) {
                                $value == '' ? $new_value = null : $new_value = $value;
                                $new_container->$key = $new_value;
                            }
                        }
                        $new_container->project_id = $request->project_id;

                        $new_container->save();

                        $containers_id [] = $new_container->id;
                    }

                    else {
                        $container = Container::where('name', $item['name'])->first();

                        if(!is_null($container->archive)) $container->update([
                            'archive' => null
                        ]);

                        foreach ($item as $key => $value) {
                            if (!in_array($key, ['add_new', 'make_return', 'return_date', 'supplier'])) {
                                if ($value === true) {
                                    $key_to_update = str_replace('update_', '', $key);
                                    $item[$key_to_update] == '' ? $new_value = null : $new_value = $item[$key_to_update];
                                    $container->$key_to_update = $new_value;
                                }
                            }
                        }
                        $container->project_id = $request->project_id;

                        $container->save();
                        $containers_id [] = $container->id;
                    }
                }

                if ($request->type == 'new_group'){

                    $new_container_group = new ContainerGroup();

                    $new_container_group->name = $request->name;
                    $new_container_group->project_id = $request->project_id;
                    $new_container_group->containers = serialize($containers_id);
                    $new_container_group->additional_info = $request->additional_info;

                    $new_container_group->save();

                    $message = __('container.new_group_successfully');

                }

                if($request->type == 'add_to_group'){

                    $container_group = ContainerGroup::find($request->chosen_group_to_add);

                    $containers = array_merge(unserialize($container_group->containers), $containers_id);
                    $container_group->containers = serialize(array_unique($containers));

                    $container_group->save();

                    $message = __('container.add_to_group_successfully');

                }

                foreach ($containers_id as $container_id){
                    $container = Container::find($container_id);
                    if (!containerHasProject($container->id, $request->project_id) && $container->type == 'В собственности') {

                        $latest_place = null;
                        if (ContainerProject::where('container_id', $container->id)->orderBy('created_at', 'desc')->count() > 0){

                            $container_project = ContainerProject::where('container_id', $container->id)->orderBy('created_at', 'desc')->first();

                            if ($container_project->drop_off_location != '') {
                                $latest_place = $container_project->drop_off_location;
                            }
                            elseif ($container_project->place_of_arrival != '') {
                                $latest_place = $container_project->place_of_arrival;
                            }
                            else $latest_place = null;

                        }

                        $new_container_project = new ContainerProject();
                        $new_container_project->start_place = $latest_place;
                        $new_container_project->project_id = $request->project_id;
                        $new_container_project->container_id = $container_id;
                        $new_container_project->status = 'Добавлен автоматически';
                        $new_container_project->save();

                    }
                }

            }

        }
        else {
            $error_found = true;
            $message = __('general.first_choose_file');
        }

        if ($error_found){
            return redirect()->back()->withError($message);
        }
        else {
            return redirect()->back()->withSuccess($message);
        }
    }

    public function loadTableRow($id)
    {

        $container = Container::find($id);

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

        $container->usage_statistic = ContainerUsageStatistic::where('project_id', $container->project_id)->where('container_id', $container->id)->get();
        $container->usage_dates = $this->getContainerUsageDates($container->id);

        $id = $container->id;

        $number = view('project.containers_table.number', [
            'container' => $container,
            'group' => $group
        ])->render();

        $dates = view('project.containers_table.dates', [
            'container' => $container,
            'group' => $group
        ])->render();

        $usage = view('project.containers_table.usage', [
            'container' => $container,
            'group' => $group
        ])->render();

        $place = view('project.containers_table.place', [
            'container' => $container,
            'group' => $group
        ])->render();

        $return = view('project.containers_table.return', [
            'container' => $container,
            'group' => $group
        ])->render();

        return array(
            $id,
            $number,
            $dates,
            $usage,
            $place,
            $return
        );
    }

}
