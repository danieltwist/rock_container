<?php

namespace App\Http\Controllers\Block;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Block;
use App\Models\BlockItem;
use App\Models\CurrencyRate;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Supplier;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class BlockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

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
     * @param  \App\Models\Block  $block
     * @return Factory|View
     */
    public function show(Block $block)
    {

        $block_item = BlockItem::where('name', $block->name)->first();
        $statuses = explode(', ', $block_item->statuses);
        $currency_rates = CurrencyRate::orderBy('created_at', 'desc')->first();

        $applications = $block->applications;

        return view('block.show',[
            'block' => $block,
            'invoices' => Invoice::where('block_id', $block['id'])->get(),
            'suppliers' => Supplier::orderBy('created_at','desc')->get(),
            'statuses' => $statuses,
            'applications' => $applications,
            'rates' => $currency_rates
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Block  $block
     * @return \Illuminate\Http\Response
     */
    public function edit(Block $block)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Block  $block
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Block $block)
    {

        if ($request->action=='choose_supplier'){

            $block ->supplier_id = $request->supplier_id;
            $block ->status = 'В работе';
            $block ->save();

            return redirect()->back()->withSuccess(__('block.supplier_was_chosen'));
        }

        if ($request->action=='change_supplier'){

            $block ->supplier_id = $request->supplier_id;
            $block ->contract_id = null;
            $block ->save();

            return redirect()->back()->withSuccess(__('block.supplier_was_changed'));
        }

        if ($request->action=='change_additional_info'){

            $block ->additional_info = $request->additional_info;
            $block ->save();

            return redirect()->back()->withSuccess(__('block.additional_info_was_changed'));
        }

        if ($request->action=='upload_application'){

            if($request->hasFile('application')) {

                $new_application = new Application();

                $new_application->type = 'Поставщик';
                $new_application->supplier_id = $block->supplier_id;
                $new_application->project_id = $block->project_id;
                $new_application->block_id = $block->id;
                $new_application->contract_id = $block->contract_id;


                $folder = preg_replace( "/[^(\w)|(\x7F-\xFF)|(\s)|(\-)]/", '', $block->supplier['name'] );
                $new_application->file = $request->application->storeAs('public/Проекты/Активные проекты/'.$block->project["name"].'/Заявки/Поставщик/'.$folder, $request->application->getClientOriginalName());

                $new_application->save();

                return redirect()->back()->withSuccess(__('block.additional_was_uploaded'));
            }

            else {
                return redirect()->back()->withError(__('general.first_choose_file'));
            }

        }

        if ($request->action=='choose_supplier_contract'){

            $block->contract_id = $request->contract;
            $block->save();

            return redirect()->back()->withSuccess(__('block.contract_was_chosen'));

        }

        if ($request->action=='change_status'){

            $block->status = $request->status;
            $block->save();

            return redirect()->back()->withSuccess(__('block.status_was_updated'));

        }


        if ($request->action=='done_this_block'){

            $block ->status = 'Завершен';
            $block ->save();

            return redirect()->back()->withSuccess(__('block.status_was_changed_to_finished'));
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Block $block
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Block $block)
    {
        $invoices = Invoice::where('block_id', $block['id'])->get();

        foreach ($invoices as $invoice){
            if (!is_null($invoice->file)){
                $invoice->delete();
            }
        }

        $block->delete();

        return redirect()->back()->withSuccess(__('block.block_was_deleted'));
    }

    public function makeBlockActive(Request $request){

        $project = Project::find($request->project_id);
        $project->active_block_id = $request->block_id;
        $project->save();

        return redirect()->back()->withSuccess(__('block.block_was_made_as_active'));

    }


}
