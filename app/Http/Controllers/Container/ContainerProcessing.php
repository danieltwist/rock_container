<?php

namespace App\Http\Controllers\Container;

use App\Http\Controllers\Controller;
use App\Http\Traits\ContainerTrait;
use App\Models\Container;
use App\Models\ContainerGroup;
use App\Models\ContainerUsageStatistic;
use App\Models\Project;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ContainerProcessing extends Controller
{
    use ContainerTrait;

    public function index()
    {
        $user = Auth::user();
        //$file_path = 'public/excel_containers/'.$user->id.'/containers_update_template.xlsx';
        $file_path = 'public/excel_containers/containers_update_template.xlsx';
        return view('container.processing.index',[
            'file_path' => $file_path
        ]);
    }

    public function uploadList (Request $request)
    {
        $message = __('container.template_error');

        if($request->hasFile('containers_list')) {

            $user = Auth::user();

            //$request->containers_list->storeAs('public/excel_containers/'.$user->id.'/', 'containers_update_template.xlsx');
            //$file_path = 'public/excel_containers/'.$user->id.'/containers_update_template.xlsx';

            $request->containers_list->storeAs('public/excel_containers/', 'containers_update_template.xlsx');
            $file_path = 'public/excel_containers/containers_update_template.xlsx';


            $file = \PhpOffice\PhpSpreadsheet\IOFactory::load($request->containers_list);
            $worksheet = $file->getActiveSheet();
            $rows = [];
            $error_found = false;
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

                    if ($row[13] != '') {
                        if ($row[13] == 'null') {
                            $return_date = 'null';
                            $list[$i]['return_date'] = '';
                        } else {
                            $container = Container::where('name', $name)->first();
                            if ($container->project_id != '') {
                                try {
                                    $return_date = Carbon::parse($row[13])->format('Y-m-d');
                                } catch (\Exception $e) {
                                    $error_found = true;
                                    $message = __('container.not_correct_return_date_format', ['name' => $name]);
                                    break;
                                }

                                if ($return_date) {
                                    $list[$i]['return_date'] = $return_date;
                                }
                            } else {
                                $error_found = true;
                                $message = __('container.cant_return_not_used', ['name' => $name]);
                                break;
                            }
                        }

                    } else {
                        $return_date = $row[13];
                        $list[$i]['return_date'] = $return_date;
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

                        if ($return_date != '') {
                            $list[$i]['make_return'] = true;

                        } else {
                            $list[$i]['make_return'] = false;
                        }

                    }

                    $i++;
                }
            }

            $need_update = false;

            foreach ($list as $item){
                foreach ($item as $key => $value){
                    $pos = strripos($key, 'update');
                    if ($pos !== false) {
                        $need_update = true;
                        break;
                    }
                    if ($key == 'add_new' && $value == 'true'){
                        $need_update = true;
                        break;
                    }
                    if ($key == 'make_return' && $value == 'true'){
                        $need_update = true;
                        break;
                    }
                }
            }

            foreach ($list as $item){
                $container_arr = new Container();
                $container_arr->fill($item);

                if($container_arr->supplier_id != ''){
                    $supplier = Supplier::find($container_arr->supplier_id);
                    $container_arr->supplier = $supplier->name;
                }
                $range_client_string = null;
                if($container_arr->snp_range_for_client != ''){
                    $range_client = unserialize($container_arr->snp_range_for_client);

                    foreach ($range_client as $client){
                        $range_client_string [] = $client['range'].' дней - '. $client['price'].$container_arr->snp_currency;
                    }

                    $container_arr->range_client_string = implode(' / ', $range_client_string);
                }
                $range_us_string = null;
                if($container_arr->snp_range_for_us != ''){
                    $range_us = unserialize($container_arr->snp_range_for_us);

                    foreach ($range_us as $us){
                        $range_us_string [] = $us['range'].' дней - '. $us['price'].$container_arr->snp_currency;
                    }

                    $container_arr->range_us_string = implode(' / ', $range_us_string);
                }

                $containers [] = $container_arr;
                $array_list [] = $container_arr->toArray();
            }


            if ($error_found){
                return redirect()->back()->withError($message);
            }
            else
                {
                    return view('container.processing.index', [
                        'containers' => $containers,
                        'list' => $array_list,
                        'need_update' => $need_update,
                        'file_path' => $file_path
                    ]);
                }

        }

        return redirect()->back()->withError(__('general.first_choose_file'));

    }

    public function saveActions(Request $request)
    {
        $containers = unserialize($request->list);

        foreach ($containers as $item) {

            if ($item['add_new']) {

                $new_container = new Container();

                foreach ($item as $key => $value) {
                    if (!in_array($key, ['add_new', 'make_return','return_date', 'supplier','range_client_string','range_us_string'])) {
                        $value == '' ? $new_value = null : $new_value = $value;
                        $new_container->$key = $new_value;
                    }
                }

                $new_container->save();

            }
            elseif ($item['make_return'] && $item['return_date'] != '') {

                $container = Container::where('name', $item['name'])->first();

                foreach ($item as $key => $value) {
                    if (!in_array($key, ['add_new', 'make_return', 'return_date', 'supplier','range_client_string','range_us_string'])) {
                        if ($value === true) {
                            $key_to_update = str_replace('update_', '', $key);
                            $item[$key_to_update] == '' ? $new_value = null : $new_value = $item[$key_to_update];
                            $container->$key_to_update = $new_value;
                        }
                    }
                }

                $container->save();

                $new_container_stat = new ContainerUsageStatistic();

                $usage_dates = $this->getContainerUsageDates($container->id);

                $new_container_stat->container_id = $container->id;
                $new_container_stat->project_id = $container->project_id;
                $new_container_stat->start_date_for_us = $container->start_date_for_us;
                $new_container_stat->start_date_for_client = $container->start_date_for_client;
                $new_container_stat->border_date = $container->border_date;
                $new_container_stat->return_date = $item['return_date'];
                $new_container_stat->svv = $container->svv;
                $new_container_stat->snp_days_for_client = $usage_dates['overdue_days'];
                $new_container_stat->snp_days_for_us = $usage_dates['overdue_days_for_us'];
                $new_container_stat->snp_total_amount_for_client = $usage_dates['snp_amount_for_client'];
                $new_container_stat->snp_total_amount_for_us = $usage_dates['snp_amount_for_us'];

                $new_container_stat->save();

                $container->project_id = null;
                $container->seal = null;
                $container->start_date_for_us = null;
                $container->start_date_for_client = null;
                $container->border_date = null;
                $container->svv = null;

                $container->save();

            }
            else {
                $container = Container::where('name', $item['name'])->first();

                if(!is_null($container->archive)) $container->update([
                    'archive' => null
                ]);

                foreach ($item as $key => $value) {
                    if (!in_array($key, ['add_new', 'make_return', 'return_date', 'supplier','range_client_string','range_us_string'])) {
                        if ($value === true) {
                            $key_to_update = str_replace('update_', '', $key);
                            $item[$key_to_update] == '' ? $new_value = null : $new_value = $item[$key_to_update];
                            $container->$key_to_update = $new_value;
                        }
                    }
                }

                $container->save();
            }
        }

        return redirect()->route('containers_processing')->withSuccess(__('container.updated_successfully'));
    }

    public function DownloadExcel(Request $request)
    {
        $today = date('Y-m-d');

        switch ($request->filter){
            case 'all':
                $containers = Container::whereNull('archive')->get();
                $filename = $today.'_все_ктк_выгрузка.xlsx';
                break;
            case 'using_now':
                $containers = Container::whereNotNull('project_id')->whereNull('archive')->get();
                $filename = $today.'_ктк_используются_в_проектах_выгрузка.xlsx';
                break;
            case 'own':
                $containers = Container::where('type', 'В собственности')->whereNull('archive')->get();
                $filename = $today.'_ктк_в_собственности_выгрузка.xlsx';
                break;
            case 'rent':
                $containers = Container::where('type', 'Аренда')->whereNull('archive')->get();
                $filename = $today.'_ктк_в_аренде_выгрузка.xlsx';
                break;
            case 'project':
                $containers = Container::where('project_id', $request->project_id)->get();
                $project = Project::find($request->project_id);
                $filename = $today.'_невозвращенные_ктк_проекта_'. $project->name .'_выгрузка.xlsx';
                break;
            case 'archive':
                $containers = Container::whereNotNull('archive')->get();
                $filename = $today.'_ктк_в_архиве_выгрузка.xlsx';
                break;
            case 'free':
                $containers = Container::whereNull('project_id')->whereNull('archive')->get();
                $filename = $today.'_ктк_не_используются_выгрузка.xlsx';
                break;
            default:
                $containers = Container::all();
                $filename = $today.'_ктк_выгрузка.xlsx';
        }

        $array = [];

        foreach ($containers as $container){
            if(!is_null($container->snp_range_for_client)){
                $snp_range_client_db = unserialize($container->snp_range_for_client);
                $snp_range_client_array = null;
                foreach ($snp_range_client_db as $client){
                    $snp_range_client_array [] = $client['range'].':'.$client['price'];
                }
                !is_null($snp_range_client_array) ? $snp_range_client = implode(',', $snp_range_client_array) : $snp_range_client = null;
            }
            else $snp_range_client = null;

            if(!is_null($container->snp_range_for_us)){
                $snp_range_us_db = unserialize($container->snp_range_for_us);
                $snp_range_us_array = null;
                foreach ($snp_range_us_db as $us){
                    $snp_range_us_array [] = $us['range'].':'.$us['price'];
                }
                !is_null($snp_range_us_array) ? $snp_range_us = implode(',', $snp_range_us_array) : $snp_range_us = null;
            }
            else $snp_range_us = null;

            $array [] = [
                'name' => $container->name,
                'supplier_id' => $container->supplier_id,
                'grace_period_for_client' => $container->grace_period_for_client,
                'grace_period_for_us' => $container->grace_period_for_us,
                'svv' => $container->svv,
                'snp_currency' => $container->snp_currency,
                'snp_range_for_client' => $snp_range_client,
                'snp_amount_for_client' => $container->snp_amount_for_client,
                'snp_range_for_us' => $snp_range_us,
                'snp_amount_for_us' => $container->snp_amount_for_us,
                'start_date_for_us' => $container->start_date_for_us,
                'start_date_for_client' => $container->start_date_for_client,
                'type' => $container->type

            ];
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load("storage/templates/containers_update_template.xlsx");
        $sheet = $spreadsheet->getActiveSheet();

        $i = 2;
        foreach ($array as $string){
            $sheet->fromArray([$string], NULL, 'A'.$i);
            $i++;
        }

        $folder = 'public/excel_containers/export/';
        $path = 'public/excel_containers/export/'.$filename;
        $savepath = 'storage/excel_containers/export/';

        Storage::makeDirectory($folder);

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
        $writer->save($savepath.$filename);

        return Storage::download($path);

    }
}
