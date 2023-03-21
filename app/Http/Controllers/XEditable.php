<?php

namespace App\Http\Controllers;

use App\Http\Traits\ContainerTrait;
use App\Http\Traits\FinanceTrait;
use App\Models\Container;
use App\Models\ContainerUsageStatistic;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\ProjectComment;
use App\Models\Setting;
use App\Models\Task;
use App\Models\WorkRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;

class XEditable extends Controller
{
    use FinanceTrait;
    use ContainerTrait;

    public function update(Request $request){
        if($request->ajax()){
            if ($request->input('model') == 'Container'){
                if(in_array($request->input('name'),['start_date_for_client','start_date_for_us','svv'])){
                    try {
                        Carbon::parse($request->input('value'))->format('Y-m-d');
                        Container::find($request->input('pk'))->update([$request->input('name') => $request->input('value')]);
                        return response()->json(['success' => true]);
                    } catch (\Exception $e) {
                        return response()->json(['success' => false]);
                    }
                }
                else {
                    Container::find($request->input('pk'))->update([$request->input('name') => $request->input('value')]);
                    return response()->json(['success' => true]);
                }

            }
            if ($request->input('model') == 'ContainerUsageStatistic'){
                if($request->input('name') == 'return_date'){
                    try {
                        Carbon::parse($request->input('value'))->format('Y-m-d');
                        $container_statistic = ContainerUsageStatistic::find($request->input('pk'));
                        $container_statistic->update([$request->input('name') => $request->input('value')]);

                        $usage_statistic = $this->getUpdatedContainerUsageDates($request->input('pk'));

                        $container_statistic->update([
                            'snp_days_for_client' => $usage_statistic['overdue_days'],
                            'snp_days_for_us' => $usage_statistic['overdue_days_for_us'],
                            'snp_total_amount_for_client' => $usage_statistic['snp_amount_for_client'],
                            'snp_total_amount_for_us' => $usage_statistic['snp_amount_for_us']
                        ]);

                        return response()->json(['success' => true]);

                    } catch (\Exception $e) {
                        return response()->json(['success' => false]);
                    }

                }
                else {
                    ContainerUsageStatistic::find($request->input('pk'))->update([$request->input('name') => $request->input('value')]);
                    return response()->json(['success' => true]);
                }

            }

            if ($request->input('model') == 'Project'){

                Project::find($request->input('pk'))->update([$request->input('name') => $request->input('value')]);
                return response()->json(['success' => true]);

            }

            if ($request->input('model') == 'ProjectComment'){

                $project_comment = ProjectComment::find($request->input('pk'));
                $project = Project::findOrFail($project_comment->project_id);

                $project_comment->update([
                    $request->input('name') => $request->input('value')
                ]);
                return response()->json([
                    'success' => true,
                    'div_id' => 'project_additional_info',
                    'ajax' => view('project.ajax.project_additional_info', [
                        'comments' => $project->comments
                    ])->render(),
                ]);

            }

            if ($request->input('model') == 'TaskComment'){

                $task = Task::find($request->input('task_id'));

                $comment = unserialize($task->comment);
                $comment[$request->input('pk')]['text'] = $request->input('value');

                $task->update([
                    $request->input('name') => serialize($comment)
                ]);

                return response()->json([
                    'success' => true,
                    'ajax' => view('task.ajax.chat',[
                        'task' => $task
                    ])->render(),
                    'div_id' => 'chat'
                ]);

            }

            if ($request->input('model') == 'WorkRequestComment'){

                $task = WorkRequest::find($request->input('task_id'));

                $comment = unserialize($task->comment);
                $comment[$request->input('pk')]['text'] = $request->input('value');

                $task->update([
                    $request->input('name') => serialize($comment)
                ]);

                return response()->json([
                    'success' => true,
                    'ajax' => view('work_request.ajax.chat',[
                        'task' => $task
                    ])->render(),
                    'div_id' => 'chat'
                ]);

            }

            if ($request->input('model') == 'Invoice'){

                $invoice = Invoice::find($request->input('pk'));
                $invoice->update([$request->input('name') => $request->input('value')]);

                $this->updateProjectFinance($invoice->project_id);

                return response()->json(['success' => true]);

            }

            if ($request->input('model') == 'Setting'){

                $invoice = Setting::find($request->input('pk'));
                $invoice->update([
                    $request->input('name') => $request->input('value')
                ]);

                return response()->json(['success' => true]);

            }
        }
    }
}
