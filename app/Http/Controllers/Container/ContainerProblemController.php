<?php

namespace App\Http\Controllers\Container;

use App\Http\Controllers\Controller;
use App\Http\Traits\ContainerTrait;
use App\Models\Container;
use App\Models\ContainerProblem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContainerProblemController extends Controller
{
    use ContainerTrait;

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
        $container = Container::find($_GET['container_id']);

        return view('container.problem.create',[
            'container' => $container
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Container $container
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        $container = Container::find($request->container_id);

        $today = Carbon::now()->format('Y-m-d');

        $new_problem = new ContainerProblem();

        $new_problem->container_id = $request->container_id;
        $new_problem->problem = $request->problem;
        $new_problem->problem_date = $today;
        $new_problem->who_fault = $request->who_fault;
        $new_problem->additional_info = $request->additional_info;

        if ($request->hasFile('problem_photos')){

            $container_number = preg_replace( "/[^(\w)|(\x7F-\xFF)|(\s)|(\-)]/", '', $container->name);
            $i=1;

            foreach ($request->file('problem_photos') as $photo){

                $filename = $container_number.'_problem_'.$i;
                $photo->storeAs('public/Проблемы с контейнерами/'.$container->name.'_'.$today.'/Фото проблемы/', $filename.'.'.$photo->extension());
                $i++;

            }

            $new_problem->problem_photos_folder = 'public/Проблемы с контейнерами/'.$container->name.'_'.$today.'/Фото проблемы/';

        }

        $new_problem->save();

        $container->problem_id = $new_problem->id;
        $container->save();

        return redirect()->route('container_problem.show', $new_problem->id)->withSuccess(__('container.container_problem_created_successfully'));

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function show($id)
    {

        $container_problem = ContainerProblem::find($id);
        $container = Container::find($container_problem->container_id);

        $problem_files = Storage::Files($container_problem->problem_photos_folder);
        $problem_solved_files = Storage::Files($container_problem->problem_photos_solved_folder);

        return view('container.problem.show',[
            'container_problem' => $container_problem,
            'container' => $container,
            'problem_files' => $problem_files,
            'problem_solved_files' => $problem_solved_files,
            'usage_dates' => $this->getContainerUsageDates($container->id)
        ]);

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
     * @param Request $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $today = Carbon::now()->format('Y-m-d');

        $container_problem = ContainerProblem::find($id);
        $container = Container::find($container_problem->container_id);

        if ($request->action == 'solve_problem'){

            $container_problem->problem_solved_date = Carbon::now()->format('Y-m-d');
            $container_problem->amount = $request->amount;
            $container_problem->additional_info = $request->additional_info;

            if ($request->hasFile('problem_photos_solved')){

                $container_number = preg_replace( "/[^(\w)|(\x7F-\xFF)|(\s)|(\-)]/", '', $container->name);
                $i=1;

                foreach ($request->file('problem_photos_solved') as $photo){

                    $filename = $container_number.'_'.$i;
                    $photo->storeAs('public/Проблемы с контейнерами/'.$container->name.'_'.$today.'/Фото решения/', $filename.'.'.$photo->extension());
                    $i++;

                }

                $container_problem->problem_photos_solved_folder = 'public/Проблемы с контейнерами/'.$container->name.'_'.$today.'/Фото решения/';

            }

            $container_problem->save();

            $container->problem_id = null;
            $container->save();

            return redirect()->back()->withSuccess(__('container.container_problem_status_updated_successfully'));
        }
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
