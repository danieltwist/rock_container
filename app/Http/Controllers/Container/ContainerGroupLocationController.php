<?php

namespace App\Http\Controllers\Container;

use App\Http\Controllers\Controller;
use App\Http\Traits\ContainerTrait;
use App\Models\Container;
use App\Models\ContainerGroup;
use App\Models\ContainerGroupLocation;
use App\Models\ContainerUsageStatistic;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ContainerGroupLocationController extends Controller
{

    use ContainerTrait;
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
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $new_location = new ContainerGroupLocation();

        $new_location->container_group_id = $request->container_group_id;
        $new_location->date = Carbon::now()->format('Y-m-d');
        $new_location->country = $request->country;
        $new_location->city = $request->city;
        $new_location->additional_info = $request->additional_info;

        $new_location->save();


        $group = ContainerGroup::find($request->container_group_id);
        $containers = unserialize($group->containers);

        $containers_list = array();

        $group->container_group_locations_list = ContainerGroupLocation::where('container_group_id', $group->id)->get();

        foreach ($containers as $container){
            $container = Container::find($container);
            $container->country = $request->country;
            $container->city = $request->city;
            $container->save();

            $container->usage_statistic = ContainerUsageStatistic::where('project_id', $group->project_id)->where('container_id', $container->id)->get();
            $container->usage_dates = $this->getContainerUsageDates($container->id);
            $containers_list[] = $container;

        }

        $group->containers_list = $containers_list;

        return response()->json([
            'bg-class' => 'bg-success',
            'from' => 'Система',
            'message' => __('container.container_group_locations_successfully'),
            'group_id' => $group->id,
            'locations' => view('project.ajax.project_group_location', [
                'group' => $group
            ])->render(),
            'table' => view('project.layouts.containers_table', [
                'group' => $group
            ])->render()
        ]);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
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
     * @param Request $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }


}
