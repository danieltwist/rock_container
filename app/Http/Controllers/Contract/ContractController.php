<?php

namespace App\Http\Controllers\Contract;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContractController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \Exception
     */
    public function index()
    {
        $contracts = Contract::all();

        foreach ($contracts as $contract){
            $data_period = new Carbon($contract->date_period);
            $data_period_reduce_month = $data_period->subMonth();
            $contract->need_prolong = 0;
            if ($data_period_reduce_month < Carbon::now()->format('Y-m-d')){
                $contract->need_prolong = 1;
            }
        }

        return view('contract.index',[
            'contracts' => $contracts
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function show(Contract $contract)
    {
        return view('contract.edit',[
            'contract' => $contract
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Contract $contract
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(Contract $contract)
    {
        return view('contract.edit',[
            'contract' => $contract
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Contract $contract
     * @return void
     */
    public function update(Contract $contract, Request $request)
    {
        $contract->name = $request->name;
        $contract->date_period = $request->date_period;
        $contract->date_start = $request->date_start;
        $contract->additional_info = $request->additional_info;

        if ($request->action == 'client'){

            $folder = preg_replace( "/[^(\w)|(\x7F-\xFF)|(\s)|(\-)]/", '', $contract->client->name);

            if($request->hasFile('file')) {
                Storage::delete($contract->file);

                $contract->file = $request->file->storeAs('public/Клиенты/Договоры/'.$folder, $request->file->getClientOriginalName());
            }
        }

        if ($request->action == 'supplier'){

            $folder = preg_replace( "/[^(\w)|(\x7F-\xFF)|(\s)|(\-)]/", '', $contract->supplier->name);

            if($request->hasFile('file')) {
                Storage::delete($contract->file);

                $contract->file = $request->file->storeAs('public/Поставщики/Договоры/'.$folder, $request->file->getClientOriginalName());
            }
        }

        $contract->save();
        return redirect()->back()->withSuccess(__('contract.updated_successfully'));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Contract $contract
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Contract $contract)
    {
        $contract->delete();

        return redirect()->back()->withSuccess(__('contract.deleted_successfully'));
    }

    public function getClientContracts()
    {
        $contracts = Contract::where('type', 'Клиент')
            ->orderBy('created_at','desc')
            ->get();

        foreach ($contracts as $contract){
            $data_period = new Carbon($contract->date_period);
            $data_period_reduce_month = $data_period->subMonth();
            $contract->need_prolong = 0;
            if ($data_period_reduce_month < Carbon::now()->format('Y-m-d')){
                $contract->need_prolong = 1;
            }
        }

        return view('contract.index',[
            'contracts' => $contracts,
            'title' => __('contract.client_contracts')
        ]);
    }

    public function getSupplierContracts()
    {
        $contracts = Contract::where('type', 'Поставщик')
            ->orderBy('created_at','desc')
            ->get();

        foreach ($contracts as $contract){
            $data_period = new Carbon($contract->date_period);
            $data_period_reduce_month = $data_period->subMonth();
            $contract->need_prolong = 0;
            if ($data_period_reduce_month < Carbon::now()->format('Y-m-d')){
                $contract->need_prolong = 1;
            }
        }

        return view('contract.index',[
            'contracts' => $contracts,
            'title' => __('contract.supplier_contracts')
        ]);
    }
}
