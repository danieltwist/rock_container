<?php

namespace App\Http\Controllers\Container;

use App\Filters\ContainerProjectFilter;
use App\Http\Controllers\Controller;
use App\Http\Traits\ContainerTrait;
use App\Models\Client;
use App\Models\ContainerProject;
use App\Models\CurrencyRate;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContainerProjectController extends Controller
{

    use ContainerTrait;

    public function index(ContainerProjectFilter $request)
    {
        $container_projects = ContainerProject::filter($request)->get();

        foreach ($container_projects as $project){
            switch ($project->status){
                case 'Завершен и оплачен':
                    $project->class = 'success';
                    break;
                case 'Ожидается оплата':
                    $project->class = 'warning';
                    break;
                case 'В работе':
                $project->class = 'primary';
                    break;
                case 'Добавлен автоматически': case 'Добавлен вручную':
                $project->class = 'info';
                    break;
                default:
                    $project->class = 'secondary';
                    break;
            }
            $project->info = $this->getContainerProjectInfo($project->id);
        }

        return view('container.project.index',[
            'container_projects' => $container_projects
        ]);
    }

    public function create()
    {
        isset($_GET['container_id']) ? $container_id = $_GET['container_id'] : $container_id = null;

        if($container_id){
            $latest_place = null;
            if (ContainerProject::where('container_id', $container_id)->orderBy('created_at', 'desc')->count() > 0){

                $container_project = ContainerProject::where('container_id', $container_id)->orderBy('created_at', 'desc')->first();

                if ($container_project->drop_off_location != '') {
                    $latest_place = $container_project->drop_off_location;
                }
                elseif ($container_project->place_of_arrival != '') {
                    $latest_place = $container_project->place_of_arrival;
                }
                else $latest_place = null;

            }

            $new_container_project = new ContainerProject();
            $new_container_project->start_place = $latest_place;
            $new_container_project->container_id = $container_id;
            $new_container_project->status = 'Добавлен вручную';
            $new_container_project->save();


            return redirect()->route('container_project.show', $new_container_project->id)->withSuccess(__('container.project_created_successfully'));
        }
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        $container_project = ContainerProject::find($id);
        $projects = Project::all();
        $clients = Client::all();
        $currency_rates = CurrencyRate::orderBy('created_at', 'desc')->first();

        return view('container.project.show',[
            'container_project' => $container_project,
            'projects' => $projects,
            'clients' => $clients,
            'currency_rates' => $currency_rates,
            'photos' => Storage::Files($container_project->photos),
            'info' => $this->getContainerProjectInfo($container_project->id)
        ]);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, ContainerProject $containerProject)
    {

        $root_folder = 'public/Контейнерные проекты/'.optional($containerProject->container)->name.'/'.$containerProject->id.'/';

        if($request->action == 'change_places'){

            $containerProject->update([
                'start_place' => $request->start_place,
                'place_of_arrival' => $request->place_of_arrival,
                'drop_off_location' => $request->drop_off_location
            ]);

            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'ajax' => view('container.project.ajax.places',[
                    'container_project' => $containerProject
                ])->render(),
                'message' => __('container.project_change_places_successfully')
            ]);

        }

        if($request->action == 'update_dates'){

            $containerProject->update([
                'date_departure' => $request->date_departure,
                'date_of_arrival' => $request->date_of_arrival,
                'svv' => $request->svv
            ]);

            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'ajax' => view('container.project.ajax.dates',[
                    'container_project' => $containerProject
                ])->render(),
                'message' => __('container.project_update_dates_successfully')
            ]);
        }

        if($request->action == 'upload_contract_with_terminal'){

            if($request->hasFile('contract_with_terminal')) {

                $containerProject->update([
                    'contract_with_terminal' => $request->contract_with_terminal
                        ->storeAs($root_folder.'Договор с терминалом отправления/', $request->contract_with_terminal->getClientOriginalName())
                ]);

                return response()->json([
                    'bg-class' => 'bg-success',
                    'from' => 'Система',
                    'ajax' => view('container.project.ajax.contract_with_terminal',[
                        'container_project' => $containerProject
                    ])->render(),
                    'message' => __('container.project_upload_contract_with_terminal_successfully')
                ]);
            }
            else {
                return response()->json([
                    'bg-class' => 'bg-danger',
                    'from' => 'Система',
                    'ajax' => view('container.project.ajax.contract_with_terminal',[
                        'container_project' => $containerProject
                    ])->render(),
                    'message' => __('general.first_choose_file')
                ]);
            }

        }

        if($request->action == 'change_additional_info'){

            $containerProject->update([
                'additional_info' => $request->additional_info,
            ]);

            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'ajax' => view('container.project.ajax.additional_info',[
                    'container_project' => $containerProject
                ])->render(),
                'message' => __('container.project_change_additional_info_successfully')
            ]);
        }

        if($request->action == 'change_project_id'){

            $containerProject->update([
                'project_id' => $request->project_id,
            ]);

            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'ajax' => view('container.project.ajax.project',[
                    'container_project' => $containerProject
                ])->render(),
                'message' => __('container.project_change_project_id_successfully')
            ]);
        }

        if($request->action == 'change_client_id'){

            $containerProject->update([
                'client_id' => $request->client_id,
            ]);

            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'ajax' => view('container.project.ajax.client',[
                    'container_project' => $containerProject
                ])->render(),
                'message' => __('container.project_change_client_id_successfully')
            ]);
        }

        if($request->action == 'upload_application_from_client'){

            if($request->hasFile('application_from_client')) {

                $containerProject->update([
                    'application_from_client' => $request->application_from_client
                        ->storeAs($root_folder.'Заявка от клиента/', $request->application_from_client->getClientOriginalName())
                ]);

                return response()->json([
                    'bg-class' => 'bg-success',
                    'from' => 'Система',
                    'ajax' => view('container.project.ajax.client_application',[
                        'container_project' => $containerProject
                    ])->render(),
                    'message' => __('container.project_application_from_client_successfully')
                ]);
            }
            else {
                return response()->json([
                    'bg-class' => 'bg-danger',
                    'from' => 'Система',
                    'ajax' => view('container.project.ajax.client_application',[
                        'container_project' => $containerProject
                    ])->render(),
                    'message' => __('general.first_choose_file')
                ]);
            }

        }

        if($request->action == 'update_rate_for_client'){

            $containerProject->update([
                'rate_for_client_usd' => $request->rate_for_client_usd,
                'rate_for_client_bank' => $request->rate_for_client_bank,
                'rate_for_client_rub' => $request->rate_for_client_rub,
            ]);

            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'ajax' => view('container.project.ajax.rate_for_client',[
                    'container_project' => $containerProject
                ])->render(),
                'message' => __('container.project_update_rate_for_client_successfully')
            ]);
        }

        if($request->action == 'update_snp'){

            $containerProject->update([
                'grace_period' => $request->grace_period,
                'snp_amount_usd' => $request->snp_amount_usd,
                'snp_bank' => $request->snp_bank,
                'snp_rub' => $request->snp_rub,
            ]);

            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'ajax' => view('container.project.ajax.snp_for_client',[
                    'container_project' => $containerProject,
                    'info' => $this->getContainerProjectInfo($containerProject->id)
                ])->render(),
                'message' => __('container.project_update_snp_successfully')
            ]);
        }

        if($request->action == 'upload_contract_with_arrival_terminal'){

            if($request->hasFile('contract_with_arrival_terminal')) {

                $containerProject->update([
                    'contract_with_arrival_terminal' => $request->contract_with_arrival_terminal
                        ->storeAs($root_folder.'Договор с терминалом прибытия/', $request->contract_with_arrival_terminal->getClientOriginalName())
                ]);

                return response()->json([
                    'bg-class' => 'bg-success',
                    'from' => 'Система',
                    'ajax' => view('container.project.ajax.contract_with_arrival_terminal',[
                        'container_project' => $containerProject
                    ])->render(),
                    'message' => __('container.project_upload_contract_with_arrival_terminal_successfully')
                ]);

            }
            else {
                return response()->json([
                    'bg-class' => 'bg-danger',
                    'from' => 'Система',
                    'ajax' => view('container.project.ajax.contract_with_arrival_terminal',[
                        'container_project' => $containerProject
                    ])->render(),
                    'message' => __('general.first_choose_file')
                ]);
            }

        }

        if($request->action == 'upload_inspection_report'){

            if($request->hasFile('inspection_report')) {

                $containerProject->update([
                    'inspection_report' => $request->inspection_report
                        ->storeAs($root_folder.'Акт осмотра/', $request->inspection_report->getClientOriginalName())
                ]);
                return response()->json([
                    'bg-class' => 'bg-success',
                    'from' => 'Система',
                    'ajax' => view('container.project.ajax.inspection_report',[
                        'container_project' => $containerProject
                    ])->render(),
                    'message' => __('container.project_upload_inspection_report_successfully')
                ]);
            }
            else {
                return response()->json([
                    'bg-class' => 'bg-danger',
                    'from' => 'Система',
                    'ajax' => view('container.project.ajax.inspection_report',[
                        'container_project' => $containerProject
                    ])->render(),
                    'message' => __('general.first_choose_file')
                ]);
            }

        }

        if($request->action == 'update_repair'){

            $containerProject->update([
                'need_repair' => $request->need_repair
            ]);

            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'ajax' => view('container.project.ajax.repair',[
                    'container_project' => $containerProject
                ])->render(),
                'message' => __('container.project_update_repair_successfully')
            ]);
        }

        if($request->action == 'update_moving'){

            $containerProject->update([
                'moving' => $request->moving
            ]);

            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'ajax' => view('container.project.ajax.moving',[
                    'container_project' => $containerProject
                ])->render(),
                'message' => __('container.project_update_moving_successfully')
            ]);
        }

        if($request->action == 'upload_photos'){

            if($request->hasFile('photos')) {

                $i=1;
                foreach ($request->file('photos') as $photo){

                    $filename = $containerProject->container->name.'_'.$i;
                    $photo->storeAs($root_folder.'Фото по прибытию/', $filename.'.'.$photo->extension());
                    $i++;

                }

                $containerProject->update([
                    'photos' => $root_folder.'Фото по прибытию/'
                ]);

                return response()->json([
                    'bg-class' => 'bg-success',
                    'from' => 'Система',
                    'ajax' => view('container.project.ajax.photos',[
                        'container_project' => $containerProject,
                        'photos' => Storage::Files($containerProject->photos)
                    ])->render(),
                    'message' => __('container.project_upload_photos_successfully')
                ]);
            }
            else {
                return response()->json([
                    'bg-class' => 'bg-danger',
                    'from' => 'Система',
                    'ajax' => view('container.project.ajax.photos',[
                        'container_project' => $containerProject,
                        'photos' => Storage::Files($containerProject->photos)
                    ])->render(),
                    'message' => __('general.first_choose_file')
                ]);
            }

        }

        if($request->action == 'update_paid'){

            $containerProject->update([
                'paid_usd' => $request->paid_usd,
                'paid_bank' => $request->paid_bank,
                'paid_rub' => $request->paid_rub
            ]);

            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'ajax' => view('container.project.ajax.paid',[
                    'container_project' => $containerProject,
                    'currency_rates' => CurrencyRate::orderBy('created_at', 'desc')->first()
                ])->render(),
                'top_panel' => view('container.project.ajax.top_panel',[
                    'info' => $this->getContainerProjectInfo($containerProject->id)
                ])->render(),
                'message' => __('container.project_update_paid_successfully')
            ]);
        }

        if($request->action == 'update_project_status'){

            $containerProject->update([
                'status' => $request->status
            ]);

            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'ajax' => view('container.project.ajax.status',[
                    'container_project' => $containerProject
                ])->render(),
                'message' => __('container.project_update_project_status_successfully')
            ]);
        }

        if($request->action == 'update_expenses'){

            if(!is_null($request->expenses_array)){
                if(!is_null($containerProject->expenses)){
                    $containerProject->update([
                        'expenses' => array_merge($containerProject->expenses, $request->expenses_array)
                    ]);
                }
                else {
                    $containerProject->update([
                        'expenses' => $request->expenses_array
                    ]);
                }

            }

            $info = $this->getContainerProjectInfo($containerProject->id);

            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'ajax' => view('container.project.ajax.expenses',[
                    'container_project' => $containerProject,
                    'currency_rates' => CurrencyRate::orderBy('created_at', 'desc')->first(),
                    'info' => $info
                ])->render(),
                'top_panel' => view('container.project.ajax.top_panel',[
                    'info' => $info
                ])->render(),
                'message' => __('container.project_update_expenses_successfully')
            ]);
        }

    }

    public function destroy(Request $request, ContainerProject $containerProject)
    {
        !is_null($containerProject->container)
            ? $root_folder = 'public/Контейнерные проекты/'.$containerProject->container->name.'/'.$containerProject->id.'/'
            : $root_folder = null;

        if($request->action == 'delete_contract_with_terminal'){

            $containerProject->update([
                'contract_with_terminal' => null
            ]);

            if($root_folder) Storage::deleteDirectory($root_folder.'Договор с терминалом отправления/');

            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'ajax' => view('container.project.ajax.contract_with_terminal',[
                    'container_project' => $containerProject
                ])->render(),
                'message' => __('container.project_delete_contract_with_terminal_successfully')
            ]);
        }

        if($request->action == 'delete_application_from_client'){

            $containerProject->update([
                'application_from_client' => null
            ]);

            if($root_folder) Storage::deleteDirectory($root_folder.'Заявка от клиента/');

            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'ajax' => view('container.project.ajax.client_application',[
                    'container_project' => $containerProject
                ])->render(),
                'message' => __('container.project_delete_application_from_client_successfully')
            ]);
        }

        if($request->action == 'delete_rate_for_client'){

            $containerProject->update([
                'rate_for_client_usd' => null,
                'rate_for_client_bank' => null,
                'rate_for_client_rub' => null
            ]);

            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'ajax' => view('container.project.ajax.rate_for_client',[
                    'container_project' => $containerProject,
                    'currency_rates' => CurrencyRate::orderBy('created_at', 'desc')->first()
                ])->render(),
                'message' => __('container.project_delete_rate_for_client_successfully')
            ]);
        }

        if($request->action == 'delete_snp'){

            $containerProject->update([
                'grace_period' => null,
                'snp_amount_usd' => null,
                'snp_bank' => null,
                'snp_rub' => null,
            ]);

            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'ajax' => view('container.project.ajax.snp_for_client',[
                    'container_project' => $containerProject,
                    'currency_rates' => CurrencyRate::orderBy('created_at', 'desc')->first()
                ])->render(),
                'message' => __('container.project_delete_snp_successfully')
            ]);
        }

        if($request->action == 'delete_contract_with_arrival_terminal'){

            $containerProject->update([
                'contract_with_arrival_terminal' => null
            ]);

            if($root_folder) Storage::deleteDirectory($root_folder.'Договор с терминалом прибытия/');

            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'ajax' => view('container.project.ajax.contract_with_arrival_terminal',[
                    'container_project' => $containerProject
                ])->render(),
                'message' => __('container.project_delete_contract_with_arrival_terminal_successfully')
            ]);
        }

        if($request->action == 'delete_inspection_report'){

            $containerProject->update([
                'inspection_report' => null
            ]);

            if($root_folder) Storage::deleteDirectory($root_folder.'Акт осмотра/');

            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'ajax' => view('container.project.ajax.inspection_report',[
                    'container_project' => $containerProject
                ])->render(),
                'message' => __('container.project_delete_inspection_report_successfully')
            ]);

        }

        if($request->action == 'delete_repair'){

            $containerProject->update([
                'need_repair' => null
            ]);

            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'ajax' => view('container.project.ajax.repair',[
                    'container_project' => $containerProject
                ])->render(),
                'message' => __('container.project_delete_repair_successfully')
            ]);
        }

        if($request->action == 'delete_moving'){

            $containerProject->update([
                'moving' => null
            ]);

            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'ajax' => view('container.project.ajax.moving',[
                    'container_project' => $containerProject
                ])->render(),
                'message' => __('container.project_delete_moving_successfully')
            ]);
        }

        if($request->action == 'delete_project_id'){

            $containerProject->update([
                'project_id' => null,
            ]);

            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'ajax' => view('container.project.ajax.project',[
                    'container_project' => $containerProject,
                    'projects' => Project::all()
                ])->render(),
                'message' => __('container.project_delete_project_id_successfully')
            ]);
        }

        if($request->action == 'delete_client_id'){

            $containerProject->update([
                'client_id' => null,
            ]);

            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'ajax' => view('container.project.ajax.client',[
                    'container_project' => $containerProject,
                    'clients' => Client::all()
                ])->render(),
                'message' => __('container.project_delete_client_id_successfully')
            ]);
        }

        if($request->action == 'delete_photos'){

            $containerProject->update([
                'photos' => null
            ]);

            if($root_folder) Storage::deleteDirectory($root_folder.'Фото по прибытию/');

            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'ajax' => view('container.project.ajax.photos',[
                    'container_project' => $containerProject
                ])->render(),
                'message' => __('container.project_delete_photos_successfully')
            ]);


        }

        if($request->action == 'remove_container_project'){

            $containerProject->delete();

            if($root_folder) Storage::deleteDirectory($root_folder);

            return redirect()->back()->withSuccess(__('container.project_remove_container_project_successfully'));

        }

        if($request->action == 'remove_expense'){

            $expenses = $containerProject->expenses;

            unset($expenses[$request->array_key]);

            $containerProject->update([
                'expenses' => $expenses
            ]);

            $info = $this->getContainerProjectInfo($containerProject->id);

            return response()->json([
                'bg-class' => 'bg-success',
                'from' => 'Система',
                'ajax' => view('container.project.ajax.expenses',[
                    'container_project' => $containerProject,
                    'currency_rates' => CurrencyRate::orderBy('created_at', 'desc')->first(),
                    'info' => $info
                ])->render(),
                'top_panel' => view('container.project.ajax.top_panel',[
                    'info' => $info
                ])->render(),
                'message' => __('container.project_remove_expense_successfully')
            ]);
        }
    }
}
