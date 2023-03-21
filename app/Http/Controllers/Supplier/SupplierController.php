<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Project\ProjectController;
use App\Http\Traits\FinanceTrait;
use App\Http\Traits\ProjectTrait;
use App\Models\Block;
use App\Models\Client;
use App\Models\Contract;
use App\Models\Country;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SupplierController extends Controller
{
    Use FinanceTrait;
    Use ProjectTrait;

    public function index()
    {
        return view('supplier.index',[
            'countries' => Country::all()
        ]);
    }

    public function create()
    {

        return view('supplier.create',[
            'countries' => Country::all(),
            'clients' => Client::all()
        ]);

    }

    public function store(Request $request)
    {
        if ($request->country == 'Россия'){
            $request->name_1 != 'Физ лицо' ? $name = $request->name_1.' '.$request->name_2 : $name = $request->name_2;
        }
        else {
            $name = $request->name_2;
        }

        $supplier_exist = Supplier::where('name', $name)->count();

        if($supplier_exist == 0){
            $new_supplier = new Supplier();
            $new_supplier->name = $name;
            $new_supplier->short_name = $request->short_name;
            $new_supplier->linked = $request->linked;
            $new_supplier->requisites = $request->requisites;
            $new_supplier->inn = $request->inn;
            $new_supplier->country = $request->country;
            $new_supplier->email = $request->email;
            if (!is_null($request->type)) $new_supplier->type = implode(', ', $request->type);
            $new_supplier->additional_info = $request->additional_info;
            $new_supplier->director = $request->director;

            $folder = preg_replace( "/[^(\w)|(\x7F-\xFF)|(\s)|(\-)]/", '', $request->name_1.' '.$request->name_2);

            if($request->hasFile('card')) {

                $new_supplier->card = $request->card->storeAs('public/Поставщики/Карточки контрагента/'.$folder, $request->card->getClientOriginalName());

            }

            $new_supplier->save();
            $supplier_id = $new_supplier->id;

            if (isset($request->contract)) {

                for ($i=0; $i < count($request->contract["name"]); $i++){

                    $new_contract = new Contract();

                    $new_contract->name = $request->contract['name'][$i];
                    $new_contract->date_period = $request->contract['date_period'][$i];
                    $new_contract->date_start = $request->contract['date_start'][$i];
                    $new_contract->file = $request->contract['file'][$i]->storeAs('public/Поставщики/Договоры/'.$folder, $request->contract['file'][$i]->getClientOriginalName());
                    $new_contract->type = 'Поставщик';
                    $new_contract->supplier_id = $supplier_id;
                    if (!empty($request->contract['additional_info'])){
                        $new_contract->additional_info = $request->contract['additional_info'][$i];
                    }

                    $new_contract->save();

                }

            }

            if($request->linked != ''){
                Client::findOrFail($request->linked)->update(
                    [
                        'linked' => $new_supplier->id
                    ]
                );
            }

            return redirect()->route('supplier.index')->withSuccess(__('supplier.was_added'));
        }

        else return redirect()->back()->withError(__('supplier.already_exist'));

    }

    public function show($id)
    {
        $supplier = Supplier::find($id);
        $projects_id = Invoice::select('project_id')->where('supplier_id', $id)->groupBy('project_id')->get()->toArray();

        foreach ($projects_id as $item){
            $projects [] = $item['project_id'];
        }

        $projects = Project::whereIn('id', $projects_id)->get();

        foreach ($projects as $project=>$key){
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
        }

        $invoices = Invoice::where('supplier_id', $id)->orderBy('created_at','desc')->get();

        $not_paid_invoices_count = Invoice::where('supplier_id', $id)->where('status','<>','Оплачен')->get()->count();

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

        $invoices_sum = $invoices->sum('amount_income_date');

        return view('supplier.show',[
            'supplier' => $supplier,
            'projects' => $projects,
            'invoices' => $invoices,
            'invoices_sum' => $invoices_sum,
            'not_paid_invoices_count' => $not_paid_invoices_count
        ]);
    }

    public function edit(Supplier $supplier)
    {
        return view('supplier.edit', [
            'supplier' => $supplier,
            'countries' => Country::all(),
            'clients' => Client::all()
        ]);
    }

    public function update(Request $request, Supplier $supplier)
    {
        $supplier->name = $request->name;
        $supplier->short_name = $request->short_name;

        $create_linked = false;
        $update_linked = false;
        $remove_linked = false;

        if(!is_null($supplier->linked)){
            if($request->linked != 'Отменить связь' && $supplier->linked != $request->linked) {
                $update_linked = true;
                $linked_before = $supplier->linked;
                $supplier->linked = $request->linked;
            }
            if($request->linked == 'Отменить связь'){
                $remove_linked = true;
                $linked_before = $supplier->linked;
                $supplier->linked = null;
            }
        }
        else {
            if($request->linked != 'Отменить связь') {
                $create_linked = true;
                $supplier->linked = $request->linked;
            }
        }

        $supplier->requisites = $request->requisites;
        $supplier->inn = $request->inn;
        $supplier->country = $request->country;
        $supplier->email = $request->email;
        if (!is_null($request->type)) $supplier->type = implode(', ', $request->type);
        $supplier->additional_info = $request->additional_info;
        $supplier->director = $request->director;

        $folder = preg_replace( "/[^(\w)|(\x7F-\xFF)|(\s)|(\-)]/", '', $request->name);

        if($request->hasFile('card')) {

            $supplier->card = $request->card->storeAs('public/Поставщики/Карточки контрагента/'.$folder, $request->card->getClientOriginalName());

        }

        $supplier->save();

        $supplier_id = $supplier->id;

        if (isset($request->contract)) {

            for ($i=0; $i < count($request->contract["name"]); $i++){

                $new_contract = new Contract();

                $new_contract->name = $request->contract['name'][$i];
                $new_contract->date_period = $request->contract['date_period'][$i];
                $new_contract->date_start = $request->contract['date_start'][$i];
                $new_contract->file = $request->contract['file'][$i]
                    ->storeAs('public/Поставщики/Договоры/'.$folder, $request->contract['file'][$i]->getClientOriginalName());
                $new_contract->type = 'Поставщик';
                $new_contract->supplier_id = $supplier_id;
                if (!empty($request->contract['additional_info'])){
                    $new_contract->additional_info = $request->contract['additional_info'][$i];
                }

                $new_contract->save();

            }

        }

        if($remove_linked){
            Client::findOrFail($linked_before)->update(
                [
                    'linked' => null
                ]
            );
        }

        if($update_linked){
            Client::findOrFail($request->linked)->update(
                [
                    'linked' => $supplier->id
                ]
            );

            Client::findOrFail($linked_before)->update(
                [
                    'linked' => null
                ]
            );
        }

        if($create_linked){
            Client::findOrFail($request->linked)->update(
                [
                    'linked' => $supplier->id
                ]
            );
        }

        return redirect()->back()->withSuccess(__('supplier.successfully_updated'));
    }

    public function destroy(Supplier $supplier)
    {
        Storage::delete($supplier->card);
        Storage::delete($supplier->contract);
        $supplier->delete();

        return redirect()->back()->withSuccess(__('supplier.was_deleted'));
    }
}
