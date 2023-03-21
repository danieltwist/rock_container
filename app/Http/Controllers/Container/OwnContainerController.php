<?php

namespace App\Http\Controllers\Container;

use App\Http\Controllers\Controller;
use App\Models\OwnContainer;
use Illuminate\Http\Request;

class OwnContainerController extends Controller
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
        if(OwnContainer::where('container_id', $request->container_id)->count() > 0){
            $own_container = OwnContainer::where('container_id', $request->container_id)->orderBy('created_at', 'desc')->first();
            $own_container->update([
                'prime_cost' => $request->prime_cost,
                'date_of_purchase' => $request->date_of_purchase,
                'place_of_purchase' => $request->place_of_purchase,
                'additional_info' => $request->additional_info
            ]);
        }
        else {
            $new_own_container = new OwnContainer();

            $new_own_container->container_id = $request->container_id;
            $new_own_container->prime_cost = $request->prime_cost;
            $new_own_container->date_of_purchase = $request->date_of_purchase;
            $new_own_container->place_of_purchase = $request->place_of_purchase;
            $new_own_container->additional_info = $request->additional_info;

            $new_own_container->save();
        }

        return redirect()->back()->withSuccess(__('container.own_container_updated_successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
