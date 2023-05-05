<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Traits\FinanceTrait;
use App\Http\Traits\ProjectTrait;
use App\Models\Client;
use App\Models\Contract;
use App\Models\Country;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClientController extends Controller
{
    Use FinanceTrait;
    Use ProjectTrait;

    public function index()
    {
        return view('client.index', [
            'countries' => Country::all()
        ]);
    }

    public function create()
    {

        return view('client.create',[
            'countries' => Country::all(),
            'suppliers' => Supplier::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->country == 'Россия' ? $name = $request->name_1 . ' ' . $request->name_2 : $name = $request->name_2;

        $client_exist = Client::where('name', $name)->count();

        if ($client_exist == 0){
            $new_client = new Client();

            $new_client->name = $name;
            $new_client->short_name = $request->short_name;
            $request->linked == 'Отменить связь' ? $new_client->linked = null : $new_client->linked = $request->linked;
            $new_client->requisites = $request->requisites;
            $new_client->inn = $request->inn;
            $new_client->country = $request->country;
            $new_client->email = $request->email;
            $new_client->additional_info = $request->additional_info;
            $new_client->director = $request->director;

            $folder = preg_replace("/[^(\w)|(\x7F-\xFF)|(\s)|(\-)]/", '', $name);

            if ($request->hasFile('card')) {

                $new_client->card = $request->card->storeAs('public/Клиенты/Карточки контрагента/' . $folder, $request->card->getClientOriginalName());

            }

            $new_client->save();
            $client_id = $new_client->id;

            if (isset($request->contract)) {

                for ($i = 0; $i < count($request->contract["name"]); $i++) {

                    $new_contract = new Contract();

                    $new_contract->name = $request->contract['name'][$i];
                    $new_contract->date_period = $request->contract['date_period'][$i];
                    $new_contract->date_start = $request->contract['date_start'][$i];
                    $new_contract->file = $request->contract['file'][$i]->storeAs('public/Клиенты/Договоры/' . $folder, $request->contract['file'][$i]->getClientOriginalName());
                    $new_contract->type = 'Клиент';
                    $new_contract->client_id = $client_id;
                    if (!empty($request->contract['additional_info'])) {
                        $new_contract->additional_info = $request->contract['additional_info'][$i];
                    }

                    $new_contract->save();

                }

            }

            if($request->linked != ''){
                Supplier::findOrFail($request->linked)->update(
                    [
                        'linked' => $new_client->id
                    ]
                );
            }

            return redirect()->route('client.index')->withSuccess(__('client.was_added'));
        }

        else return redirect()->back()->withError(__('client.already_exist'));

    }

    public function show(Client $client)
    {

        $all_projects = Project::where('status','<>','Черновик')->get();

        $projects = [];

        foreach ($all_projects as $project){
            $project->additional_clients != '' ? $additional_clients = unserialize($project->additional_clients) : $additional_clients = false;
            if($additional_clients){
                if (in_array($client->id, $additional_clients)){
                    $projects [] = $project;
                }
            }
            if($client->id === $project->client_id){
                $projects [] = $project;
            }
        }

        $profit = 0;
        if(!is_null($projects)){
            foreach ($projects as $project => $key) {
                $key->finance = $this->getProjectFinance($key['id']);
                $key->complete_level = $this->getProjectCompleteLevel($key['id']);
                switch ($key->status) {
                    case 'Черновик':
                        $key->status_class = 'info';
                        break;
                    case 'В работе':
                        $key->status_class = 'primary';
                        break;
                    case 'Завершен':
                        $key->status_class = 'success';
                        break;
                    default:
                        $key->status_class = 'secondary';
                }
                if ($key->active == '0') {
                    if($key->client_id == $client->id){
                        $profit += $key->finance['profit'];
                    }
                }
            }
        }

        $invoices = Invoice::where('client_id', $client->id)->orderBy('created_at', 'desc')->get();
        $not_paid_invoices_count = Invoice::where('client_id', $client->id)->where('status','<>','Оплачен')->get()->count();

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

        $invoices_sum = $invoices->sum('amount');

        return view('client.show', [
            'client' => $client,
            'projects' => $projects,
            'invoices' => $invoices,
            'invoices_sum' => $invoices_sum,
            'profit' => $profit,
            'not_paid_invoices_count' => $not_paid_invoices_count
        ]);
    }

    public function edit(Client $client)
    {
        return view('client.edit', [
            'client' => $client,
            'countries' => Country::all(),
            'suppliers' => Supplier::all()
        ]);
    }

    public function update(Request $request, Client $client)
    {
        $client->name = $request->name;
        $client->short_name = $request->short_name;

        $create_linked = false;
        $update_linked = false;
        $remove_linked = false;

        if(!is_null($client->linked)){
            if($request->linked != 'Отменить связь' && $client->linked != $request->linked) {
                $update_linked = true;
                $linked_before = $client->linked;
                $client->linked = $request->linked;
            }
            if($request->linked == 'Отменить связь'){
                $remove_linked = true;
                $linked_before = $client->linked;
                $client->linked = null;
            }
        }
        else {
            if($request->linked != 'Отменить связь') {
                $create_linked = true;
                $client->linked = $request->linked;
            }
        }

        $client->requisites = $request->requisites;
        $client->inn = $request->inn;
        $client->country = $request->country;
        $client->email = $request->email;
        $client->additional_info = $request->additional_info;
        $client->director = $request->director;

        $folder = preg_replace("/[^(\w)|(\x7F-\xFF)|(\s)|(\-)]/", '', $request->name);

        if ($request->hasFile('card')) {

            $client->card = $request->card->storeAs('public/Клиенты/Карточки контрагента/' . $folder, $request->card->getClientOriginalName());

        }

        $client->save();

        $client_id = $client->id;

        if (isset($request->contract)) {

            for ($i = 0; $i < count($request->contract["name"]); $i++) {

                $new_contract = new Contract();

                $new_contract->name = $request->contract['name'][$i];
                $new_contract->date_start = $request->contract['date_start'][$i];
                $new_contract->date_period = $request->contract['date_period'][$i];
                $new_contract->file = $request->contract['file'][$i]->storeAs('public/Клиенты/Договоры/' . $folder, $request->contract['file'][$i]->getClientOriginalName());
                $new_contract->type = 'Клиент';
                $new_contract->client_id = $client_id;
                if (!empty($request->contract['additional_info'])) {
                    $new_contract->additional_info = $request->contract['additional_info'][$i];
                }

                $new_contract->save();

            }

        }

        if($remove_linked){
            Supplier::findOrFail($linked_before)->update(
                [
                    'linked' => null
                ]
            );
        }

        if($update_linked){
            Supplier::findOrFail($request->linked)->update(
                [
                    'linked' => $client->id
                ]
            );

            Supplier::findOrFail($linked_before)->update(
                [
                    'linked' => null
                ]
            );
        }

        if($create_linked){
            Supplier::findOrFail($request->linked)->update(
                [
                    'linked' => $client->id
                ]
            );
        }

        return redirect()->back()->withSuccess(__('client.successfully_updated'));
    }

    public function destroy(Client $client)
    {
        Storage::delete($client->card);
        Storage::delete($client->contract);
        $client->delete();

        return redirect()->back()->withSuccess(__('client.was_deleted'));
    }


    public function deleteRow($id){

        Client::findOrFail($id)->delete();

        return response()->json([
            'bg-class' => 'bg-success',
            'from' => 'Система',
            'message' => __('client.was_deleted')
        ]);
    }

    public function restoreRow($id){

        $client = Client::withTrashed()->findOrFail($id);
        $client_name = $client->name;
        $client->restore();

        return response()->json([
            'bg-class' => 'bg-success',
            'from' => 'Система',
            'message' => __('Клиент ' .$client_name. ' был успешно восстановлен')
        ]);
    }

}
