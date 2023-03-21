<?php

namespace App\Http\Controllers\Block;

use App\Http\Controllers\Controller;
use App\Models\BlockItem;
use Illuminate\Http\Request;

class BlockItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        $block_items = BlockItem::all();
        return view('project.plan.create_new_item',[
            'block_items' => $block_items
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $new_item = new BlockItem();

        $new_item->name = $request->name;
        $new_item->save();

        return redirect()->back()->withSuccess(__('block.block_was_added'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BlockItem  $blockItem
     * @return \Illuminate\Http\Response
     */
    public function show(BlockItem $blockItem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BlockItem  $blockItem
     * @return \Illuminate\Http\Response
     */
    public function edit(BlockItem $blockItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BlockItem  $blockItem
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BlockItem $blockItem)
    {
        $blockItem = BlockItem::find($blockItem->id);
        $blockItem->statuses = $request->statuses;
        $blockItem->save();

        return redirect()->back()->withSuccess(__('block.statuses_was_updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\BlockItem $blockItem
     * @return void
     * @throws \Exception
     */
    public function destroy(BlockItem $blockItem)
    {
        $blockItem->delete();

        return redirect()->back()->withSuccess(__('block.item_was_deleted'));
    }
}
