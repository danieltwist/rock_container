<?php

namespace App\Http\Controllers\Application;

use App\Http\Controllers\Controller;
use App\Http\Traits\ContainerTrait;
use App\Http\Traits\CurrencyTrait;
use App\Http\Traits\ProjectTrait;
use App\Models\Application;
use App\Models\Client;
use App\Models\Container;
use App\Models\ContainerHistory;
use App\Models\Contract;
use App\Models\Country;
use App\Models\CurrencyRate;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApplicationController extends Controller
{

    use ProjectTrait;
    use ContainerTrait;
    use CurrencyTrait;

    public function index()
    {
        return view('application.index', [
            'applications' => Application::all()
        ]);
    }

    public function create()
    {
        $latest_application_id = Application::latest()->first();
        if(!is_null($latest_application_id)){
            $latest_application_id = Application::latest()->first()->id+1;
        }
        else {
            $latest_application_id = 1;
        }
        return view('application.create', [
            'clients' => Client::all(),
            'suppliers' => Supplier::all(),
            'latest_application_id' => $latest_application_id,
            'countries' => Country::all()
        ]);
    }

    public function store(Request $request)
    {
        $application = new Application();

        $application->name = $request->name;
        $application->type = $request->application_type;
        $application->counterparty_type = $request->counterparty_type;
        $application->client_id = $request->client_id;
        $application->supplier_id = $request->supplier_id;
        $application->contract_id = $request->contract_id;

        if($request->counterparty_type == 'Клиент') {
            $client = Client::find($request->client_id);
            !is_null($client->short_name) ? $name = $client->short_name : $name = $client->name;
            $application->client_name = $name;
        }

        if($request->counterparty_type == 'Поставщик') {
            $supplier = Supplier::find($request->supplier_id);
            !is_null($supplier->short_name) ? $name = $supplier->short_name : $name = $supplier->name;
            $application->supplier_name = $name;
        }

        $contract = Contract::find($request->contract_id);

        $contract_info = [
            'name' => $contract->name,
            'date' => $contract->date_start
        ];

        $application->contract_info = $contract_info;
        $application->price_currency = $request->price_currency;
        $application->price_amount = $request->price_amount;
        $application->currency_rate = $this->getCurrencyRate($request->price_currency);
        $application->containers_amount = $request->containers_amount;
        $application->send_from_country = $request->send_from_country;
        $application->send_from_city = $request->send_from_city;
        $application->send_to_country = $request->send_to_country;
        $application->send_to_city = $request->send_to_city;
        $application->place_of_delivery_country = $request->place_of_delivery_country;
        $application->place_of_delivery_city = $request->place_of_delivery_city;
        $application->grace_period = $request->grace_period;
        $application->snp_currency = $request->snp_currency;
        $application->snp_range = $request->snp_application_array;
        $application->snp_after_range = $request->snp_after_range;
        $application->containers = $request->containers;
        $application->containers_type = $request->containers_type;
        $application->additional_info = $request->additional_info;

        $application->save();
        if(!is_null($request->containers)){
            if($request->application_type == 'Поставщик') {
                foreach ($request->containers as $item){
                    $item = str_replace(' ', '', $item);
                    $container = Container::where('name', $item)->first();

                    if(is_null($container)){
                        $container = new Container();
                    }

                    if(!is_null($request->send_from_city)){
                        if(count($request->send_from_city) > 1){
                            $send_from_city = implode(', ', $request->send_from_city);
                        }
                        else {
                            $send_from_city = $request->send_from_city[0];
                        }
                    }
                    if(!is_null($request->place_of_delivery_city)){
                        if(count($request->place_of_delivery_city) > 1){
                            $place_of_delivery_city = implode(', ', $request->place_of_delivery_city);
                        }
                        else {
                            $place_of_delivery_city = $request->place_of_delivery_city[0];
                        }
                    }

                    $container->name = $item;
                    $container->owner_id = $request->supplier_id;
                    $container->owner_name = $supplier->name;
                    $container->supplier_application_id = $application->id;
                    $container->supplier_application_name = $request->name;
                    $container->supplier_price_amount = $request->price_amount;
                    $container->supplier_price_currency = $request->price_currency;
                    $container->supplier_grace_period = $request->grace_period;
                    $container->supplier_snp_range = $request->snp_application_array;
                    $container->supplier_snp_after_range = $request->snp_after_range;
                    $container->supplier_snp_currency = $request->snp_currency;
                    $container->supplier_country = $request->send_from_country;
                    $container->supplier_city = $send_from_city;
                    $container->supplier_place_of_delivery_country = $request->place_of_delivery_country;
                    $container->supplier_place_of_delivery_city = $place_of_delivery_city;
                    $container->size = $request->containers_type;

                    $container->save();

                }
            }
            if($request->application_type == 'Подсыл') {
                foreach ($request->containers as $item){
                    $item = str_replace(' ', '', $item);
                    $container = Container::where('name', $item)->first();
                    if(!is_null($container)){
                        if(!is_null($request->send_to_city)){
                            if(count($request->send_to_city) > 1){
                                $send_to_city = implode(', ', $request->send_to_city);
                            }
                            else {
                                $send_to_city = $request->send_to_city[0];
                            }
                        }
                        if(!is_null($request->place_of_delivery_city)){
                            if(count($request->place_of_delivery_city) > 1){
                                $place_of_delivery_city = implode(', ', $request->place_of_delivery_city);
                            }
                            else {
                                $place_of_delivery_city = $request->place_of_delivery_city[0];
                            }
                        }
                        if($request->counterparty_type == 'Клиент') {
                            $client = Client::find($request->client_id);
                            !is_null($client->short_name) ? $name = $client->short_name : $name = $client->name;
                            $container->relocation_counterparty_id = $request->client_id;
                            $container->relocation_counterparty_name = $name;
                        }

                        if($request->counterparty_type == 'Поставщик') {
                            $supplier = Supplier::find($request->supplier_id);
                            !is_null($supplier->short_name) ? $name = $supplier->short_name : $name = $supplier->name;
                            $container->relocation_counterparty_id = $request->supplier_id;
                            $container->relocation_counterparty_name = $name;
                        }

                        $container->relocation_application_id = $application->id;
                        $container->relocation_application_name = $request->name;
                        $container->relocation_price_amount = $request->price_amount;
                        $container->relocation_price_currency = $request->price_currency;
                        $container->relocation_delivery_time_days = $request->grace_period;
                        $container->relocation_snp_range = $request->snp_application_array;
                        $container->relocation_snp_after_range = $request->snp_after_range;
                        $container->relocation_snp_currency = $request->snp_currency;
                        $container->relocation_place_of_delivery_city = $send_to_city;
                        $container->relocation_place_of_delivery_terminal = $place_of_delivery_city;
                        $container->size = $request->containers_type;

                        $container->save();
                    }
                }
            }
            if($request->application_type == 'Клиент') {
                foreach ($request->containers as $item){
                    $item = str_replace(' ', '', $item);
                    $container = Container::where('name', $item)->first();
                    if(!is_null($container)){
                        if(!is_null($request->place_of_delivery_city)){
                            if(count($request->place_of_delivery_city) > 1){
                                $place_of_delivery_city = implode(', ', $request->place_of_delivery_city);
                            }
                            else {
                                $place_of_delivery_city = $request->place_of_delivery_city[0];
                            }
                        }

                        $container->client_counterparty_id = $request->client_id;
                        $container->client_counterparty_name = $client->name;
                        $container->client_application_id = $application->id;
                        $container->client_application_name = $request->name;
                        $container->client_price_amount = $request->price_amount;
                        $container->client_price_currency = $request->price_currency;
                        $container->client_grace_period = $request->grace_period;
                        $container->client_snp_range = $request->snp_application_array;
                        $container->client_snp_after_range = $request->snp_after_range;
                        $container->client_snp_currency = $request->snp_currency;
                        $container->client_place_of_delivery_country = $request->place_of_delivery_country;
                        $container->client_place_of_delivery_city = $place_of_delivery_city;
                        $container->size = $request->containers_type;

                        $container->save();
                    }
                }
            }
        }

        return redirect()->route('application.show', $application->id)->withSuccess('Заявка была успешно добавлена');
    }

    public function show(Application $application)
    {
        is_null($application->invoices_generate) ? $planned_invoices = $this->getApplicationInvoices($application) : $planned_invoices = $application->invoices_generate;
        $planned_out = 0;
        $planned_in = 0;
        $fact_out = 0;
        $fact_in = 0;

        foreach ($planned_invoices as $invoice){
            if($invoice['type'] == 'Расход'){
                $planned_in += $invoice['amount_in_rubles'];
            }
            if($invoice['type'] == 'Доход'){
                $planned_out += $invoice['amount_in_rubles'];
            }
        }

        foreach ($application->invoices as $invoice){
            if($invoice->direction == 'Расход'){
                $fact_in += $invoice->amount;
            }
            if($invoice->direction == 'Доход'){
                $fact_out += $invoice->amount;
            }
        }

        return view('application.show', [
            'application' => $application,
            'clients' => Client::all(),
            'suppliers' => Supplier::all(),
            'rates' => $this->getRates(),
            'projects' => Project::orderBy('created_at', 'desc')->get(),
            'columns' => $this->columns,
            'containers_count' => Container::application($application->id)->count(),
            'containers_archive_count' => ContainerHistory::application($application->id)->count(),
            'planned_in' => $planned_in,
            'planned_out' => $planned_out,
            'fact_in' => $fact_in,
            'fact_out' => $fact_out,
        ]);
    }

    public function edit(Application $application)
    {
        $contracts = [];

        if($application->counterparty_type == 'Поставщик'){
            $supplier = Supplier::find($application->supplier_id);
            $contracts = $supplier->contracts;
        }

        if($application->counterparty_type == 'Клиент'){
            $client = Client::find($application->client_id);
            $contracts = $client->contracts;
        }

        return view('application.edit', [
            'clients' => Client::all(),
            'suppliers' => Supplier::all(),
            'application' => $application,
            'countries' => Country::all(),
            'contracts' => $contracts,
            'cities_from' => Country::where('name', $application->send_from_country)->pluck('cities'),
            'cities_to' => Country::where('name', $application->send_to_country)->pluck('cities'),
            'cities_place_of_delivery' => Country::where('name', $application->place_of_delivery_country)->pluck('cities'),
        ]);
    }

    public function update(Request $request, Application $application)
    {
        $containers_removed = null;
        $removed_by = null;
        $containers_removed_now = null;
        $containers_added = [];
        $all_containers = [];

        if(!is_null($application->containers)){
            if(!is_null($request->containers)){
                $containers_removed_now = array_values(array_diff($application->containers, $request->containers));
                if(!empty($containers_removed_now)) {
                    if(!is_null($application->containers_removed)){
                        $containers_removed = array_merge($containers_removed_now, $application->containers_removed);
                    }
                    else {
                        $containers_removed = $containers_removed_now;
                    }
                    $removed_by = auth()->user()->name;
                }
                else {
                    $containers_removed_now = null;
                }
                $containers_added = array_values(array_diff($request->containers, $application->containers));
                $all_containers = array_merge($containers_added, $application->containers);
                if(!empty($containers_removed)){
                    $all_containers = array_unique(array_merge($all_containers, $containers_removed));
                }
            }
        }
        else {
            $containers_added = $request->containers;
            $all_containers = $request->containers;
        }

        $application->name = $request->name;
        $application->type = $request->type;
        $application->counterparty_type = $request->counterparty_type;
        $application->client_id = $request->client_id;
        $application->supplier_id = $request->supplier_id;
        $application->contract_id = $request->contract_id;

        if($request->counterparty_type == 'Клиент') {
            $client = Client::find($request->client_id);
            !is_null($client->short_name) ? $name = $client->short_name : $name = $client->name;
            $application->client_name = $name;
        }
        if($request->counterparty_type == 'Поставщик') {
            $supplier = Supplier::find($request->supplier_id);
            !is_null($supplier->short_name) ? $name = $supplier->short_name : $name = $supplier->name;
            $application->supplier_name = $name;
        }

        $contract = Contract::find($request->contract_id);
        $contract_info = [
            'name' => $contract->name,
            'date' => $contract->date_start
        ];

        $application->contract_info = $contract_info;
        $application->price_currency = $request->price_currency;
        $application->price_amount = $request->price_amount;
        $application->currency_rate = $this->getCurrencyRate($request->price_currency);
        $application->containers_amount = $request->containers_amount;
        $application->send_from_country = $request->send_from_country;
        $application->send_from_city = $request->send_from_city;
        $application->send_to_country = $request->send_to_country;
        $application->send_to_city = $request->send_to_city;
        $application->place_of_delivery_country = $request->place_of_delivery_country;
        $application->place_of_delivery_city = $request->place_of_delivery_city;
        $application->grace_period = $request->grace_period;
        $application->snp_currency = $request->snp_currency;
        $application->snp_range = $request->snp_application_array;
        $application->snp_after_range = $request->snp_after_range;
        $application->containers_type = $request->containers_type;

        if(is_null($application->containers_archived)){
            $application->containers = $request->containers;
            if(!is_null($containers_removed)){
                $application->containers_removed = $containers_removed;
            }
            $application->removed_by = $removed_by;

            if(!is_null($containers_removed_now)){
                foreach ($containers_removed_now as $container_name){
                    $container = Container::where('name', $container_name)->first();
                    if(!is_null($container)){
                        if(in_array($container_name, $containers_removed_now)){
                            $container->update([
                                'removed' => $removed_by
                            ]);
                        }
                    }
                }
            }
        }

        $application->additional_info = $request->additional_info;
        $application->save();
        if(is_null($application->containers_archived)){
            if(!is_null($request->containers)){
                if($request->type == 'Поставщик') {
                    foreach ($request->containers as $item){
                        $container = Container::where('name', $item)->first();

                        if(is_null($container)){
                            $container = new Container();
                        }

                        if(!is_null($request->send_from_city)){
                            if(count($request->send_from_city) > 1){
                                $send_from_city = implode(', ', $request->send_from_city);
                            }
                            else {
                                $send_from_city = $request->send_from_city[0];
                            }
                        }
                        if(!is_null($request->place_of_delivery_city)){
                            if(count($request->place_of_delivery_city) > 1){
                                $place_of_delivery_city = implode(', ', $request->place_of_delivery_city);
                            }
                            else {
                                $place_of_delivery_city = $request->place_of_delivery_city[0];
                            }
                        }

                        $container->name = $item;
                        $container->owner_id = $request->supplier_id;
                        $container->owner_name = $supplier->name;
                        $container->supplier_application_id = $application->id;
                        $container->supplier_application_name = $request->name;
                        $container->supplier_price_amount = $request->price_amount;
                        $container->supplier_price_currency = $request->price_currency;
                        $container->supplier_grace_period = $request->grace_period;
                        $container->supplier_snp_range = $request->snp_application_array;
                        $container->supplier_snp_after_range = $request->snp_after_range;
                        $container->supplier_snp_currency = $request->snp_currency;
                        $container->supplier_country = $request->send_from_country;
                        $container->supplier_city = $send_from_city;
                        $container->supplier_place_of_delivery_country = $request->place_of_delivery_country;
                        $container->supplier_place_of_delivery_city = $place_of_delivery_city;
                        $container->size = $request->containers_type;
                        $container->save();

                    }
                }
                if($request->type == 'Подсыл') {
                    foreach ($request->containers as $item){
                        $container = Container::where('name', $item)->first();
                        if(!is_null($container)){
                            if(!is_null($request->send_to_city)){
                                if(count($request->send_to_city) > 1){
                                    $send_to_city = implode(', ', $request->send_to_city);
                                }
                                else {
                                    $send_to_city = $request->send_to_city[0];
                                }
                            }
                            if(!is_null($request->place_of_delivery_city)){
                                if(count($request->place_of_delivery_city) > 1){
                                    $place_of_delivery_city = implode(', ', $request->place_of_delivery_city);
                                }
                                else {
                                    $place_of_delivery_city = $request->place_of_delivery_city[0];
                                }
                            }

                            if($request->counterparty_type == 'Клиент') {
                                $client = Client::find($request->client_id);
                                !is_null($client->short_name) ? $name = $client->short_name : $name = $client->name;
                                $container->relocation_counterparty_id = $request->client_id;
                                $container->relocation_counterparty_name = $name;
                            }

                            if($request->counterparty_type == 'Поставщик') {
                                $supplier = Supplier::find($request->supplier_id);
                                !is_null($supplier->short_name) ? $name = $supplier->short_name : $name = $supplier->name;
                                $container->relocation_counterparty_id = $request->supplier_id;
                                $container->relocation_counterparty_name = $name;
                            }

                            $container->relocation_application_id = $application->id;
                            $container->relocation_application_name = $request->name;
                            $container->relocation_price_amount = $request->price_amount;
                            $container->relocation_price_currency = $request->price_currency;
                            $container->relocation_delivery_time_days = $request->grace_period;
                            $container->relocation_snp = $request->snp_after_range;
                            $container->relocation_snp_currency = $request->snp_currency;
                            $container->relocation_place_of_delivery_city = $send_to_city;
                            $container->relocation_place_of_delivery_terminal = $place_of_delivery_city;
                            $container->size = $request->containers_type;
                            $container->save();
                        }
                    }
                }
                if($request->type == 'Клиент') {
                    foreach ($request->containers as $item){
                        $container = Container::where('name', $item)->first();
                        if(!is_null($container)){
                            if(!is_null($request->place_of_delivery_city)){
                                if(count($request->place_of_delivery_city) > 1){
                                    $place_of_delivery_city = implode(', ', $request->place_of_delivery_city);
                                }
                                else {
                                    $place_of_delivery_city = $request->place_of_delivery_city[0];
                                }
                            }

                            $container->client_counterparty_id = $request->client_id;
                            $container->client_counterparty_name = $client->name;
                            $container->client_application_id = $application->id;
                            $container->client_application_name = $request->name;
                            $container->client_price_amount = $request->price_amount;
                            $container->client_price_currency = $request->price_currency;
                            $container->client_grace_period = $request->grace_period;
                            $container->client_snp_range = $request->snp_application_array;
                            $container->client_snp_after_range = $request->snp_after_range;
                            $container->client_snp_currency = $request->snp_currency;
                            $container->client_place_of_delivery_country = $request->place_of_delivery_country;
                            $container->client_place_of_delivery_city = $place_of_delivery_city;
                            $container->size = $request->containers_type;
                            $container->save();
                        }
                    }
                }
            }
        }

        return redirect()->route('application.show', $application->id)->withSuccess(__('Заявка была успешно изменена'));
        //return redirect()->back()->withSuccess(__('Заявка была успешно изменена'));
    }

    public function destroy(Application $application)
    {
        $project_id = $application->project_id;

        Storage::delete($application->file);
        $application->delete();

        return response()->json([
            'bg-class' => 'bg-success',
            'from' => 'Система',
            'message' => __('project.application_was_deleted'),
            'ajax' => view('project.ajax.project_applications', [
                'applications' => $this->getApplications($project_id)
            ])->render()
        ]);
    }

    public function loadCounterpartyContract(Request $request){

        $contracts = [];

        if ($request->counterparty_type == 'client'){
            $client = Client::find($request->counterparty_id);
            $contracts = $client->contracts;
        }

        if ($request->counterparty_type == 'supplier'){
            $supplier = Supplier::find($request->counterparty_id);
            $contracts = $supplier->contracts;
        }

        return response()->json([
            'view' => view('application.ajax.counterparty_contracts', [
                'contracts' => $contracts
            ])->render()
        ]);
    }

    public function processContainersList(Request $request){

        $probable_delimiters = array(" ", ", ", ";", "\r\n", "\r", "\n", "\n,", ",");
        $used_delimiter = null;
        $not_found = null;
        $not_correct_format = null;

        foreach ($probable_delimiters as $delimiter){
            if(strpos($request->containers_list, $delimiter) !== false ){
                $used_delimiter = $delimiter;
                break;
            }
        }

        $containers = [];

        if(mb_strlen(preg_replace("/[^,.0-9A-Z]/",'', $request->containers_list)) == 11){
            $container_name = preg_replace("/[^,.0-9A-Z]/",'', $request->containers_list);
            if($this->checkContainerFormat($container_name)){
                $containers [] = $container_name;
            }
            else {
                $not_correct_format [] = $container_name;
            }
        }
        else {
            if(!is_null($used_delimiter)){
                $containers = array_unique(explode($used_delimiter, $request->containers_list));
            }

            if($request->containers != ''){
                $containers = array_unique(array_merge($containers, $request->containers));
            }
        }
        foreach ($containers as $key => $name){
            $containers[$key] = preg_replace("/[^0-9A-Za-z]/",'', $name);
        }
        //dd($used_delimiter);
        //dd($containers);
        foreach ($containers as $container_name){

            if($this->checkContainerFormat($container_name)){
                if(in_array($request->application_type, ['Клиент', 'Подсыл'])){

                    $container = Container::where('name', $container_name)->first();

                    if(is_null($container)) {
                        if (($key = array_search($container_name, $containers)) !== false) {
                            unset($containers[$key]);
                        }
                        $not_found [] = $container_name;
                    }
                }
            }
            else {
                if (($key = array_search($container_name, $containers)) !== false) {
                    unset($containers[$key]);
                }
                $not_correct_format [] = $container_name;
            }
        }

        return response()->json([
            'view' => view('application.ajax.dymanic_containers', [
                'containers' => array_unique($containers),
                'not_found' => $not_found,
                'not_correct_format' => $not_correct_format
            ])->render()
        ]);

    }

    public function loadCities(Request $request){
        return [
            'view' => view('application.ajax.'. $request->type .'_cities_list', [
                'cities' => Country::where('name', $request->country)->pluck('cities'),
            ])->render()
        ];
    }

    public function addCity(Request $request){

        $country = Country::where('name', $request->country)->first();
        $cities = $country->cities;
        $cities [] = $request->city;

        Country::where('name', $request->country)->update([
            'cities' => $cities
        ]);

    }

    public function confirmContainersRemove(Request $request){
        $application = Application::find($request->application_id);
        $containers_removed = $application->containers_removed;

        if(!is_null($containers_removed)){
            foreach ($containers_removed as $container){
                $container = Container::where('name', $container)->first();
                $this->saveContainerUsageHistory($container, $application->id);
                $container->update([
                    'archive' => 'yes'
                ]);
            }
        }

        $application->containers_removed = null;
        $application->removed_by = null;

        $application->save();

        return response()->json([
            'bg-class' => 'bg-success',
            'from' => 'Система',
            'message' => 'Удаление успешно подтверждено'
        ]);
    }

    public function cancelContainersRemove(Request $request){
        $application = Application::find($request->application_id);
        $containers_removed = $application->containers_removed;
        $containers = $application->containers;

        $application->update([
            'containers' => array_merge($containers, $containers_removed),
            'containers_removed' => null,
            'removed_by' => null
        ]);

        foreach ($containers_removed as $container_name){
            $container = Container::where('name', $container_name)->first();
            if(!is_null($container)){
                if(in_array($container_name, $containers_removed)){
                    $container->update([
                        'removed' => null
                    ]);
                }
            }
        }

        return response()->json([
            'bg-class' => 'bg-success',
            'from' => 'Система',
            'message' => 'Контейнеры были успешно восстановлены'
        ]);
    }

    public function getApplicationInvoices($application){
        $invoices = [];

        switch ($application->counterparty_type){
            case 'Поставщик':
                $counterparty_type = 'supplier';
                $counterparty_id = $application->supplier_id;
                $counterparty_name = $application->supplier_name;
                break;
            case 'Клиент':
                $counterparty_type = 'client';
                $counterparty_id = $application->client_id;
                $counterparty_name = $application->client_name;
                break;
        }

        $price_amount = $application->containers_amount*$application->price_amount;
        $amount_in_rubles = $this->getPriceInRub($price_amount, $application->price_currency);

        if($application->type == 'Поставщик'){

            $invoices [] = [
                'type' => 'Расход',
                'counterparty_type' => $counterparty_type,
                'counterparty_id' => $counterparty_id,
                'counterparty_name' => $counterparty_name,
                'amount_in_currency' => $price_amount,
                'currency' => $application->price_currency,
                'amount_in_rubles' => $amount_in_rubles,
                'info' => 'Базовая ставка взятие ктк у поставщика'
            ];

            if(!is_null($application->containers)){
                $snp = [
                    'RUB' => 0,
                    'CNY' => 0,
                    'USD' => 0
                ];
                $terminal = [
                    'RUB' => 0,
                    'CNY' => 0,
                    'USD' => 0
                ];
                $renewal = [
                    'RUB' => 0,
                    'CNY' => 0,
                    'USD' => 0
                ];
                $repair_out = [
                    'RUB' => 0,
                    'CNY' => 0,
                    'USD' => 0
                ];
                $repair_in = [
                    'RUB' => 0,
                    'CNY' => 0,
                    'USD' => 0
                ];

                foreach ($application->containers as $container_name){
                    $container = Container::where('name', $container_name)->first();
                    if(!is_null($container)) {
                        if (!is_null($container->supplier_snp_total)) {
                            if (!is_null($container->supplier_snp_currency))
                                $snp[$container->supplier_snp_currency] += $container->supplier_snp_total;
                        }
                        if (!is_null($container->supplier_terminal_storage_amount) && $container->supplier_payer_tx == 'РК') {
                            if (!is_null($container->supplier_terminal_storage_currency))
                                $terminal[$container->supplier_terminal_storage_currency] += $container->supplier_terminal_storage_amount;
                        }
                        if (!is_null($container->supplier_renewal_reexport_costs_amount)) {
                            if (!is_null($container->supplier_renewal_reexport_costs_currency))
                                $renewal[$container->supplier_renewal_reexport_costs_currency] += $container->supplier_renewal_reexport_costs_amount;
                        }
                        if (!is_null($container->supplier_repair_amount)) {
                            if ($container->supplier_repair_confirmation == 'Да') {
                                if (!is_null($container->supplier_repair_currency))
                                    $repair_out[$container->supplier_repair_currency] += $container->supplier_repair_amount;
                            }
                            if ($container->supplier_repair_confirmation == 'Нет') {
                                if (!is_null($container->supplier_repair_currency))
                                    $repair_in[$container->supplier_repair_currency] += $container->supplier_repair_amount;
                            }
                        }
                    }
                }

                foreach ($snp as $key => $value){
                    if($value != 0){
                        $info = 'СНП от поставщика';
                        $in_rubles = $value;
                        if($key != 'RUB'){
                            $in_rubles = $this->getPriceInRub($value, $key);
                            $info .= ' '.$key;
                        }
                        $invoices [] = [
                            'type' => 'Расход',
                            'counterparty_type' => $counterparty_type,
                            'counterparty_id' => $counterparty_id,
                            'counterparty_name' => $counterparty_name,
                            'amount_in_currency' => $value,
                            'currency' => $key,
                            'amount_in_rubles' => $in_rubles,
                            'info' => $info
                        ];
                    }
                }

                foreach ($terminal as $key => $value){
                    if($value != 0){
                        $info = 'Терминальное хранение';
                        $in_rubles = $value;
                        if($key != 'RUB'){
                            $in_rubles = $this->getPriceInRub($value, $key);
                            $info .= ' '.$key;
                        }
                        $invoices [] = [
                            'type' => 'Расход',
                            'counterparty_type' => $counterparty_type,
                            'counterparty_id' => $counterparty_id,
                            'counterparty_name' => $counterparty_name,
                            'amount_in_currency' => $value,
                            'currency' => $key,
                            'amount_in_rubles' => $in_rubles,
                            'info' => $info
                        ];
                    }
                }

                foreach ($renewal as $key => $value){
                    if($value != 0){
                        $info = 'Продление/реэкспорт';
                        $in_rubles = $value;
                        if($key != 'RUB'){
                            $in_rubles = $this->getPriceInRub($value, $key);
                            $info .= ' '.$key;
                        }
                        $invoices [] = [
                            'type' => 'Расход',
                            'counterparty_type' => $counterparty_type,
                            'counterparty_id' => $counterparty_id,
                            'counterparty_name' => $counterparty_name,
                            'amount_in_currency' => $value,
                            'currency' => $key,
                            'amount_in_rubles' => $in_rubles,
                            'info' => $info
                        ];
                    }
                }

                foreach ($repair_out as $key => $value){
                    if($value != 0){
                        $info = 'Ремонт поставщик подтвержден';
                        $in_rubles = $value;
                        if($key != 'RUB'){
                            $in_rubles = $this->getPriceInRub($value, $key);
                            $info .= ' '.$key;
                        }
                        $invoices [] = [
                            'type' => 'Доход',
                            'counterparty_type' => $counterparty_type,
                            'counterparty_id' => $counterparty_id,
                            'counterparty_name' => $counterparty_name,
                            'amount_in_currency' => $value,
                            'currency' => $key,
                            'amount_in_rubles' => $in_rubles,
                            'info' => $info
                        ];
                    }
                }
                foreach ($repair_in as $key => $value){
                    if($value != 0){
                        $info = 'Ремонт поставщик не подтвержден';
                        $in_rubles = $value;
                        if($key != 'RUB'){
                            $in_rubles = $this->getPriceInRub($value, $key);
                            $info .= ' '.$key;
                        }
                        $invoices [] = [
                            'type' => 'Расход',
                            'counterparty_type' => $counterparty_type,
                            'counterparty_id' => $counterparty_id,
                            'counterparty_name' => $counterparty_name,
                            'amount_in_currency' => $value,
                            'currency' => $key,
                            'amount_in_rubles' => $in_rubles,
                            'info' => $info
                        ];
                    }
                }
            }
        }

        if($application->type == 'Подсыл'){

            $invoices [] = [
                'type' => 'Расход',
                'counterparty_type' => $counterparty_type,
                'counterparty_id' => $counterparty_id,
                'counterparty_name' => $counterparty_name,
                'amount_in_currency' => $price_amount,
                'currency' => $application->price_currency,
                'amount_in_rubles' => $amount_in_rubles,
                'info' => 'Перевозка до терминала выдачи клиенту'
            ];

            if(!is_null($application->containers)){
                $snp = [
                    'RUB' => 0,
                    'CNY' => 0,
                    'USD' => 0
                ];
                $repair_out = [
                    'RUB' => 0,
                    'CNY' => 0,
                    'USD' => 0
                ];
                $repair_in = [
                    'RUB' => 0,
                    'CNY' => 0,
                    'USD' => 0
                ];

                foreach ($application->containers as $container_name){
                    $container = Container::where('name', $container_name)->first();
                    if(!is_null($container)) {
                        if (!is_null($container->relocation_snp_total)) {
                            if (!is_null($container->relocation_snp_currency))
                                $snp[$container->relocation_snp_currency] += $container->relocation_snp_total;
                        }
                        if (!is_null($container->relocation_repair_amount)) {
                            if ($container->relocation_repair_confirmation == 'Да') {
                                if (!is_null($container->relocation_repair_currency))
                                    $repair_out[$container->relocation_repair_currency] += $container->relocation_repair_amount;
                            }
                            if ($container->relocation_repair_confirmation == 'Нет') {
                                if (!is_null($container->relocation_repair_currency))
                                    $repair_in[$container->relocation_repair_currency] += $container->relocation_repair_amount;
                            }
                        }
                    }
                }

                foreach ($snp as $key => $value){
                    if($value != 0){
                        $info = 'СНП от подсыла';
                        $in_rubles = $value;
                        if($key != 'RUB'){
                            $in_rubles = $this->getPriceInRub($value, $key);
                            $info .= ' '.$key;
                        }
                        $invoices [] = [
                            'type' => 'Доход',
                            'counterparty_type' => $counterparty_type,
                            'counterparty_id' => $counterparty_id,
                            'counterparty_name' => $counterparty_name,
                            'amount_in_currency' => $value,
                            'currency' => $key,
                            'amount_in_rubles' => $in_rubles,
                            'info' => $info
                        ];
                    }
                }

                foreach ($repair_out as $key => $value){
                    if($value != 0){
                        $info = 'Ремонт ктк подсыл подтвержден';
                        $in_rubles = $value;
                        if($key != 'RUB'){
                            $in_rubles = $this->getPriceInRub($value, $key);
                            $info .= ' '.$key;
                        }
                        $invoices [] = [
                            'type' => 'Доход',
                            'counterparty_type' => $counterparty_type,
                            'counterparty_id' => $counterparty_id,
                            'counterparty_name' => $counterparty_name,
                            'amount_in_currency' => $value,
                            'currency' => $key,
                            'amount_in_rubles' => $in_rubles,
                            'info' => $info
                        ];
                    }
                }
                foreach ($repair_in as $key => $value){
                    if($value != 0){
                        $info = 'Ремонт ктк подсыл не подтвержден';
                        $in_rubles = $value;
                        if($key != 'RUB'){
                            $in_rubles = $this->getPriceInRub($value, $key);
                            $info .= ' '.$key;
                        }
                        $invoices [] = [
                            'type' => 'Расход',
                            'counterparty_type' => $counterparty_type,
                            'counterparty_id' => $counterparty_id,
                            'counterparty_name' => $counterparty_name,
                            'amount_in_currency' => $value,
                            'currency' => $key,
                            'amount_in_rubles' => $in_rubles,
                            'info' => $info
                        ];
                    }
                }
            }
        }

        if($application->type == 'Клиент'){

            $invoices [] = [
                'type' => 'Доход',
                'counterparty_type' => $counterparty_type,
                'counterparty_id' => $counterparty_id,
                'counterparty_name' => $counterparty_name,
                'amount_in_currency' => $price_amount,
                'currency' => $application->price_currency,
                'amount_in_rubles' => $amount_in_rubles,
                'info' => 'Базовая ставка выдачи ктк клиенту'
            ];

            if(!is_null($application->containers)){
                $snp = [
                    'RUB' => 0,
                    'CNY' => 0,
                    'USD' => 0
                ];
                $repair_out = [
                    'RUB' => 0,
                    'CNY' => 0,
                    'USD' => 0
                ];
                $repair_in = [
                    'RUB' => 0,
                    'CNY' => 0,
                    'USD' => 0
                ];

                foreach ($application->containers as $container_name){
                    $container = Container::where('name', $container_name)->first();
                    if(!is_null($container)){
                        if(!is_null($container->client_snp_total)){
                            if(!is_null($container->client_snp_currency))
                                $snp[$container->client_snp_currency] += $container->client_snp_total;
                        }
                        if(!is_null($container->client_repair_amount)){
                            if($container->client_repair_confirmation == 'Да'){
                                if(!is_null($container->client_repair_currency))
                                    $repair_out[$container->client_repair_currency] += $container->client_repair_amount;
                            }
                            if($container->client_repair_confirmation == 'Нет'){
                                if(!is_null($container->client_repair_currency))
                                    $repair_in[$container->client_repair_currency] += $container->client_repair_amount;
                            }
                        }
                    }

                }

                foreach ($snp as $key => $value){
                    if($value != 0){
                        $info = 'СНП от клиента';
                        $in_rubles = $value;
                        if($key != 'RUB'){
                            $in_rubles = $this->getPriceInRub($value, $key);
                            $info .= ' '.$key;
                        }
                        $invoices [] = [
                            'type' => 'Доход',
                            'counterparty_type' => $counterparty_type,
                            'counterparty_id' => $counterparty_id,
                            'counterparty_name' => $counterparty_name,
                            'amount_in_currency' => $value,
                            'currency' => $key,
                            'amount_in_rubles' => $in_rubles,
                            'info' => $info
                        ];
                    }
                }

                foreach ($repair_out as $key => $value){
                    if($value != 0){
                        $info = 'Ремонт ктк клиент подтвержден';
                        $in_rubles = $value;
                        if($key != 'RUB'){
                            $in_rubles = $this->getPriceInRub($value, $key);
                            $info .= ' '.$key;
                        }
                        $invoices [] = [
                            'type' => 'Доход',
                            'counterparty_type' => $counterparty_type,
                            'counterparty_id' => $counterparty_id,
                            'counterparty_name' => $counterparty_name,
                            'amount_in_currency' => $value,
                            'currency' => $key,
                            'amount_in_rubles' => $in_rubles,
                            'info' => $info
                        ];
                    }
                }

                foreach ($repair_in as $key => $value){
                    if($value != 0){
                        $info = 'Ремонт ктк клиент не подтвержден';
                        $in_rubles = $value;
                        if($key != 'RUB'){
                            $in_rubles = $this->getPriceInRub($value, $key);
                            $info .= ' '.$key;
                        }
                        $invoices [] = [
                            'type' => 'Расход',
                            'counterparty_type' => $counterparty_type,
                            'counterparty_id' => $counterparty_id,
                            'counterparty_name' => $counterparty_name,
                            'amount_in_currency' => $value,
                            'currency' => $key,
                            'amount_in_rubles' => $in_rubles,
                            'info' => $info
                        ];
                    }
                }
            }
        }

        return $invoices;
    }

    public function getInvoicesPreviewBeforeGenerate(Request $request){
        $application = Application::find($request->application_id);

        is_null($application->invoices_generate) ? $invoices = $this->getApplicationInvoices($application) : $invoices = $application->invoices_generate;

        return [
            'view' => view('application.ajax.preview_invoices', [
                'invoices' => $invoices
            ])->render(),
        ];
    }

    public function addChosenInvoices(Request $request){

        $currency_rates = CurrencyRate::orderBy('created_at', 'desc')->first();
        $invoice_added = false;

        if(!is_null($request->all_invoices)){
            foreach (unserialize($request->all_invoices) as $key => $invoice){
                $item = 'preview_invoices_application_'.$key;
                if(isset($request->$item)){
                    $type = $invoice['counterparty_type'].'_id';

                    if($invoice['counterparty_type'] == 'supplier'){
                        $counterparty = Supplier::where('id', $invoice['counterparty_id'])->first();
                        if(!is_null($counterparty)){
                            $country = $counterparty->country;
                        }
                        else break;
                    }
                    else {
                        $counterparty = Client::where('id', $invoice['counterparty_id'])->first();
                        if(!is_null($counterparty)){
                            $country = $counterparty->country;
                        }
                        else break;
                    }

                    $new_invoice = new Invoice();

                    $new_invoice->$type = $invoice['counterparty_id'];
                    $new_invoice->project_id = $request->project_id;
                    $new_invoice->application_id = $request->application_id;
                    $new_invoice->direction = $invoice['type'];

                    $new_invoice->amount = $invoice['amount_in_rubles'];
                    $new_invoice->currency = $invoice['currency'];

                    if($invoice['currency'] != 'RUB'){
                        $new_invoice->amount_in_currency = $invoice['amount_in_currency'];
                        $new_invoice->rate_out_date = $currency_rates[$invoice['currency']];
                    }
                    if($invoice['type'] == 'Расход'){
                        $status = 'Ожидается счет от поставщика';
                    }
                    else {
                        if($country == 'Россия') {
                            $status = 'Ожидается оплата';
                        }
                        else {
                            $status = 'Ожидается создание инвойса';
                        }
                    }

                    $new_invoice->status = $status;
                    $new_invoice->additional_info = $invoice['info'];

                    $new_invoice->save();

                    $invoice_added = true;
                }
            }

        }

        if($invoice_added){
            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'message' => 'Счета были успешно добавлены'
            ]);
        }
        else {
            return response()->json([
                'bg-class' => 'bg-danger',
                'from' => 'Система',
                'message' => 'Нет счетов для добавления'
            ]);
        }
    }

    public function archiveContainersUsageInfo(Request $request){
        $application = Application::find($request->application_id);
        $containers = $application->containers;

        $invoices_generate = $this->getApplicationInvoices($application);
        $application->update([
            'invoices_generate' => $invoices_generate,
            'containers_archived' => 'yes'
        ]);

        if(!is_null($containers)){
            foreach ($containers as $container_name){
                $container = Container::where('name', $container_name)->first();
                $this->saveContainerUsageHistory($container, $application->id);
            }
        }

        return redirect()->back()->withSuccess('Информация по контейнерам данной заявки успешно перенесена в архив');

    }

    public function downloadApplicationTemplate(Request $request){
        $application = Application::find($request->application_id);

        switch ($request->application_template){
            case '1':
                $application_type_name = 'Мы даем ктк рус-кит';
                $template_file = 'storage/templates/application/application_template_1.docx';
                break;
            case '2':
                $application_type_name = 'ТЭО нам платят';
                $template_file = 'storage/templates/application/application_template_2.docx';
                break;
            case '3':
                $application_type_name = 'Заявка наша к договору предоставления ктк платят нам';
                $template_file = 'storage/templates/application/application_template_3.docx';
                break;
            default:
                $application_type_name = 'Мы даем ктк рус-кит';
                $template_file = 'storage/templates/application/application_template_1.docx';
        }

        $today = explode('-', Carbon::now()->format('Y-m-d'));
        $application_date = explode('-', explode(' ', $application->created_at)[0]);

        $filename = 'Заявка_'.$application->id.'_'.$application_type_name.'_'.Carbon::now()->format('Y-m-d h-i-s').'.docx';

        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($template_file);

        $templateProcessor->setValue('today_day', $today[2]);
        $templateProcessor->setValue('today_month', $this->monthInWord($today[1]));
        $templateProcessor->setValue('today_year', $today[0]);

        $templateProcessor->setValue('application_day', $application_date[2]);
        $templateProcessor->setValue('application_month', $application_date[1]);
        $templateProcessor->setValue('application_year', $application_date[0]);

        if(!is_null($application->contract)){
            $templateProcessor->setValue('dogovor_number', $application->contract->name);
            $contract_date = explode('-', $application->contract->date_start);
            $templateProcessor->setValue('dogovor_day', $contract_date[2]);
            $templateProcessor->setValue('dogovor_month', $contract_date[1]);
            $templateProcessor->setValue('dogovor_year', $contract_date[0]);
        }

        $templateProcessor->setValue('application_number', $application->name);

        $counterparty = $this->getCounterpartyName($application);

        $templateProcessor->setValue('counterparty_name', $counterparty['name']);
        $templateProcessor->setValue('counterparty_director', $counterparty['director']);

        $from = $application->send_from_country.', '.implode(' / ', $application->send_from_city);
        $to = $application->place_of_delivery_country.', '.implode(' / ', $application->place_of_delivery_city);

        $templateProcessor->setValue('application_from', $from);
        $templateProcessor->setValue('application_to', $to);
        $templateProcessor->setValue('application_containers_count', $application->containers_amount);
        $templateProcessor->setValue('application_price', $application->price_amount.$application->price_currency);
        $templateProcessor->setValue('application_grace_period', $application->grace_period);
        $templateProcessor->setValue('application_grace_period_plus_1', (int)$application->grace_period + 1);
        $templateProcessor->setValue('application_containers_type', $application->containers_type);


        $snp = $application->snp_after_range.$application->snp_currency;
        if(!is_null($application->snp_range)){
            $snp_range = [];
            foreach ($application->snp_range as $range){
                $snp_range [] = $range['range'].' - '.$range['price'];
            }
            $snp_range [] = 'далее - '.$snp;

            $snp = implode(', ', $snp_range);
        }
        $templateProcessor->setValue('application_snp', $snp);
        $templateProcessor->setValue('application_additional_info', $application->additional_info);

        $templateProcessor->setValue('application_price_without_currency', $application->price_amount);
        $templateProcessor->setValue('application_price_in_word', $this->num2str($application->price_amount));
        $templateProcessor->setValue('application_currency', $application->price_currency);

        if(!is_null($application->containers)){
            $table = '<table class="table table-condensed" style="border: 1px #000000 solid; width:630px">
                        <tbody>
                        <tr>
                            <th style="width:3%"><strong>№</strong></th>
                            <th style="width:19%"><strong>Пункт отправления (станция, порт)</strong></th>
                            <th style="width:19%"><strong>Пункт назначения (станция, порт)</strong></th>
                            <th style="width:20%"><strong>Типоразмер</strong></th>
                            <th style="width:20%"><strong>Префикс, номер</strong></th>
                            <th style="width:19%"><strong>Срок доставки контейнера в пункт назначения</strong></th>
                        </tr>';
            $i = 1;
            foreach ($application->containers as $container){
                $table .= '<tr>';
                $table .= '<td>'.$i.'</td>';
                $table .= '<td>'.$from.'</td>';
                $table .= '<td>'.$to.'</td>';
                $table .= '<td>'.$application->containers_type.'</td>';
                $table .= '<td>'.$container.'</td>';
                $table .= '<td>'.$application->grace_period.'</td>';
                $table .= '</tr>';

                $i++;
            }
            $table .= '</tbody></table>';
        }


        $wordTable = new \PhpOffice\PhpWord\Element\Table();
        $wordTable->addRow();
        $cell = $wordTable->addCell();
        \PhpOffice\PhpWord\Shared\Html::addHtml($cell, $table);

        $templateProcessor->setComplexBlock('table', $wordTable);


        $templateProcessor->saveAs('storage/'.$filename);

        $folder = 'Заявки/'.$application->id;
        Storage::makeDirectory('public/'.$folder);

        Storage::move('public/'.$filename, 'public/'.$folder.'/'.$filename);

        return config('app.url').'storage/'.$folder.'/'.$filename;

    }

    public function getCounterpartyName(Application $application){

        if(!is_null($application->supplier_id)){
            $company = Supplier::find($application->supplier_id);
        }
        else {
            $company = Client::find($application->client_id);
        }
        if(!is_null($company)){
            $company_name = $company->name;
            $company_director = $company->director;
        }
        else {
            $company_name = '';
            $company_director = '';
        }

        return [
            'name' => $company_name,
            'director' => $company_director
        ];
    }

    public function monthInWord($month)
    {
        if((int)$month < 10) $month = str_replace('0', '', $month);

        $months_arr = [
            'января',
            'февраля',
            'марта',
            'апреля',
            'мая',
            'июня',
            'июля',
            'августа',
            'сентября',
            'октября',
            'ноября',
            'декабря'
        ];
        return $months_arr[$month - 1];
    }

    public function morph($n, $f1, $f2, $f5)
    {
        $n = abs(intval($n)) % 100;
        if ($n > 10 && $n < 20) return $f5;
        $n = $n % 10;
        if ($n > 1 && $n < 5) return $f2;
        if ($n == 1) return $f1;
        return $f5;
    }

    public function num2str($num)
    {
        $nul = 'ноль';
        $ten = array(
            array('', 'один', 'два', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'),
            array('', 'одна', 'две', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'),
        );
        $a20 = array('десять', 'одиннадцать', 'двенадцать', 'тринадцать', 'четырнадцать', 'пятнадцать', 'шестнадцать', 'семнадцать', 'восемнадцать', 'девятнадцать');
        $tens = array(2 => 'двадцать', 'тридцать', 'сорок', 'пятьдесят', 'шестьдесят', 'семьдесят', 'восемьдесят', 'девяносто');
        $hundred = array('', 'сто', 'двести', 'триста', 'четыреста', 'пятьсот', 'шестьсот', 'семьсот', 'восемьсот', 'девятьсот');
        $unit = array( // Units
            array('копейка', 'копейки', 'копеек', 1),
            array('рубль', 'рубля', 'рублей', 0),
            array('тысяча', 'тысячи', 'тысяч', 1),
            array('миллион', 'миллиона', 'миллионов', 0),
            array('миллиард', 'милиарда', 'миллиардов', 0),
        );
        //
        list($rub, $kop) = explode('.', sprintf("%015.2f", floatval($num)));
        $out = array();
        if (intval($rub) > 0) {
            foreach (str_split($rub, 3) as $uk => $v) { // by 3 symbols
                if (!intval($v)) continue;
                $uk = sizeof($unit) - $uk - 1; // unit key
                $gender = $unit[$uk][3];
                list($i1, $i2, $i3) = array_map('intval', str_split($v, 1));
                // mega-logic
                $out[] = $hundred[$i1]; # 1xx-9xx
                if ($i2 > 1) $out[] = $tens[$i2] . ' ' . $ten[$gender][$i3]; # 20-99
                else $out[] = $i2 > 0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
                // units without rub & kop
                if ($uk > 1) $out[] = $this->morph($v, $unit[$uk][0], $unit[$uk][1], $unit[$uk][2]);
            } //foreach
        } else $out[] = $nul;
        //$out[] = $this->morph(intval($rub), $unit[1][0], $unit[1][1], $unit[1][2]); // rub
        //$out[] = $kop . ' ' . $this->morph($kop, $unit[0][0], $unit[0][1], $unit[0][2]); // kop
        return trim(preg_replace('/ {2,}/', ' ', join(' ', $out)));
    }

    public function checkContainerFormat($container_name){

        $correct = true;

        $container_name = mb_strtoupper(preg_replace("/[^,.0-9A-Za-z]/",'', $container_name));

        if(mb_strlen($container_name) == 11){
            if(preg_match('/^[A-Z]+$/', mb_substr($container_name, 0, 4)) !== 1 || preg_match('/^[0-9]+$/', mb_substr($container_name, 4, 7)) !== 1 ){
                $correct = false;
            }
        }
        else $correct = false;

        return $correct;

    }

    public function deleteRow($id){

        $application = Application::findOrFail($id);
        $application_name = $application->name;
        $application->delete();

        return response()->json([
            'bg-class' => 'bg-success',
            'from' => 'Система',
            'message' => __('Заявка ' .$application_name. ' была успешно удалена')
        ]);
    }

}
