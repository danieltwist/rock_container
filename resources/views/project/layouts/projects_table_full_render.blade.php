<table class="table table-striped projects" id="projects_table">
    <thead>
    <tr>
        <th style="width: 1%">
            #
        </th>
        <th style="width: 22%">
            {{ __('general.project') }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        </th>
        <th style="width: 25%">
            {{ __('project.about_shipment') }}
        </th>
        <th>
            {{ __('project.finances') }}
        </th>
        <th style="width: 5%" class="text-center">
            {{ __('general.status') }}
        </th>
        <th style="width: 24%">
            {{ __('general.actions') }}
        </th>
    </tr>
    </thead>
    <tbody>
    @foreach($projects as $project)
        @php
            switch($project->status){
                case 'В работе':
                    $class = 'primary';
                    break;
                case 'Черновик':
                    $class = 'info';
                    break;
                case 'Завершен':
                    $class = 'success';
                    break;
                default:
                    $class = 'secondary';
            }
        @endphp
        <tr>
            <td>
                {{ $project->id }}
            </td>
            <td>
                <b><a class="text-dark" href="{{ route('project.show', $project['id']) }}">
                        {{ $project->name }}
                    </a></b>
                @if ($project->status == 'Завершен')
                    @if($project->paid != 'Не оплачен')
                        <i class="fas fa-check-circle"></i>
                    @else
                        - {{ $project->paid }}
                    @endif
                @endif
                <br>
                    <small>
                        @if(!is_null(optional($project->user)->name))
                            {{ __('project.created') }}: <a class="text-dark" href="{{ route('get_user_statistic', optional($project->user)->id) }}">{{ optional($project->user)->name}}</a>
                        @endif
                        @if($project->manager_id != '')
                            @if(!is_null(optional($project->manager)->name))
                                / {{ __('project.manager') }}: <a class="text-dark" href="{{ route('get_user_statistic', optional($project->manager)->id) }}">{{ optional($project->manager)->name}}</a>
                            @endif
                        @endif
                        @if($project->logist_id != '')
                            @if(!is_null(optional($project->logist)->name))
                                / {{ __('project.logist') }}: <a class="text-dark" href="{{ route('get_user_statistic', optional($project->logist)->id) }}">{{ optional($project->logist)->name}}</a>
                            @endif
                        @endif
                    </small>
                    <br>
                <small>
                    {{ $project->created_at }}
                </small>
                <br>
                <small>{{ __('project.project_finished_percent') }} {{ $project->complete_level }}%</small>
                <div class="progress">
                    <div class="progress-bar bg-primary progress-bar-striped" role="progressbar"
                         aria-valuenow="{{ $project->complete_level }}" aria-valuemin="0" aria-valuemax="100"
                         style="width: {{ $project->complete_level }}%">
                        <span class="sr-only">{{ $project->complete_level }}% {{ __('project.project_finished_percent_simple') }}</span>
                    </div>
                </div>
                <div class="mt-2">
                @if($project->active == '1')
                    @if($project->status == 'Черновик')
                        @if(can_edit_this_project_price($project->id))
                            <form class="button-delete-inline" action="{{ route('set_status_in_work', $project['id']) }}"
                                  method="POST">
                                @csrf
                                <input type="hidden" name="project_id" value="{{ $project->id }}">
                                <button type="submit" class="btn btn-sm bg-primary">
                                    <i class="fas fa-check"></i>
                                    {{ __('project.set_status_in_work') }}
                                </button>
                            </form>
                        @endif
                    @else
                        @if ($project->active_block != '')
                            <small>
                                {{ __('project.current_stage') }}:
                                <b>
                                    @if ($project->active_block != '')
                                        {{ $project->active_block->name }} {{ $project->active_block->status }}
                                    @else
                                        {{ __('general.not_set') }}
                                    @endif
                                </b>
                            </small>
                        @endif
                    @endif
                @else
                    {{ __('project.finish_date') }}:
                    @if (canWorkWithProject($project->id))
                        <a href="#" class="xedit" data-pk="{{$project->id}}" data-name="finished_at" data-model="Project">
                            {{ $project->finished_at }}
                        </a>
                    @else
                        {{ $project->finished_at }}
                    @endif
                @endif
                </div>
            </td>
            <td>
                @if(!is_null($project->client))
                    <a class="text-dark" href="{{ route('client.show', $project->client->id) }}">Клиент: {{ $project->client->name }}</a><br>
                @else
                    {{ __('general.client_was_deleted') }}<br><br>
                @endif
                @if($project->additional_clients != '')
                    <button class="btn btn-primary btn-sm mt-2" type="button"
                            data-toggle="collapse"
                            data-target="#collapseAdditionalClients{{$project->id}}"
                            aria-expanded="false"
                            aria-controls="collapseExample">
                        <i class="fa fa-angle-down"></i>
                        {{ __('project.additional_clients') }}
                    </button>
                    <div class="collapse mt-2" id="collapseAdditionalClients{{$project->id}}">
                        <div class="card card-body">
                            <div class="text-muted">
                                <ul>
                                @foreach($project->additional_client() as $client)
                                    <li>{{ $client }}</li>
                                @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
                <br><small>{{ __('project.direction') }}: {{ $project->from }} - {{ $project->to }}<br>
                {{ __('project.goods_info') }}: {{ $project->freight_info }} {{ $project->freight_amount }}
                @if ($project->additional_info!='')
                    @if(mb_strlen($project->additional_info)>100)
                        <div id="collapse_task_text_compact_{{ $project->id }}">
                            <br>{{ __('general.additional_info') }}: {{ \Illuminate\Support\Str::limit($project->additional_info, 100, $end='...') }}
                            <a class="cursor-pointer collapse-trigger" data-div_id="collapse_task_text_full_{{ $project->id }}"><i class="fa fa-angle-down"></i> {{ __('general.expand') }}</a>
                        </div>
                        <div id="collapse_task_text_full_{{ $project->id }}" class="d-none">
                            <br>{{ __('general.additional_info') }}: {{ $project->additional_info }}
                            <a class="cursor-pointer collapse-trigger" data-div_id="collapse_task_text_compact_{{ $project->id }}"><i class="fa fa-angle-up"></i> {{ __('general.collapse') }}</a>
                        </div>
                    @else
                        <br>{{ __('general.additional_info') }}: {{ $project->additional_info }}
                    @endif
                @endif
                </small>
            </td>
            <td>
                @if(!is_null($project->expense))
                    {{ __('project.planned_income') }}: {{ number_format($project->expense->price_in_rub, 0, '.', ' ') }}р.<br>
                    @if ($project->expense->currency != 'RUB')
                        {{ __('project.in_currency') }}: {{ number_format($project->expense->price_in_currency, 0, '.', ' ') }} {{ $project->expense->currency }}<br>
                        {{ __('general.rate') }}: {{ $project->expense->cb_rate .' / '. $project->finance['today_rate'] }}
                        @else
                        {{ __('project.project_currency') }}: {{ __('general.ruble') }}
                    @endif
                    <br><br>{{ __('project.planned_profit') }}: {{ number_format($project->expense->planned_profit, 0, '.', ' ') }}р.<br>
                    <b>{{ __('project.now_profit') }}: {{ number_format($project->finance['profit'], 0, '.', ' ') }}р.</b>
                    <br>
                    <button class="btn btn-primary btn-sm mt-2" type="button"
                            data-toggle="collapse"
                            data-target="#collapseCosts{{$project->id}}"
                            aria-expanded="false"
                            aria-controls="collapseExample">
                        <i class="fa fa-angle-down"></i>
                        {{ __('project.planned_outcome') }} {{ number_format($project->expense->planned_costs, 0, '.', ' ') }}р.
                    </button>

                    <div class="collapse mt-2" id="collapseCosts{{$project->id}}">
                        <div class="card card-body">
                            <div class="text-muted">
                                @if (!is_null($project->expense->expenses_array))
                                    @foreach(unserialize($project->expense->expenses_array) as $expense)
                                        @php
                                            $type = $expense['type'];
                                        @endphp
                                        <p class="text-sm">{{ $expense[$type.'_name'] }}
                                            <b class="d-block">
                                                {{ $expense[$type.'_price_1pc'].$expense[$type.'_currency'] }}
                                                {{ $expense[$type.'_currency']!='RUB' ? '('. $expense[$type.'_rate'] .'р.)' : ''}} x
                                                {{ $expense[$type.'_amount'] }} =
                                                {{ $expense[$type.'_total_price_in_rub'] }}р.
                                            </b>
                                        </p>
                                    @endforeach
                                @else
                                    {{ __('project.cost_part_not_filled') }}
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    {{ __('project.block_not_filled') }}
                @endif
            </td>
            <td class="project-state">
                <span class="badge badge-{{ $class }}">
                    @include('project.status_switch', ['status' => $project->status])
                </span>
            </td>
            <td class="project-actions">
                <a class="btn btn-app bg-primary" href="{{ route('project.show', $project['id']) }}">
                    <i class="far fa-eye">
                    </i>
                    {{ __('general.go') }}
                </a>
                <a class="btn btn-app bg-success" href="{{ route('export_project', $project['id']) }}">
                    <i class="fas fa-file-excel"></i>
                    </i>
                    {{ __('general.export') }}
                </a>
                @can ('edit projects')
                    @if(can_edit_this_project($project->id))
                        <a class="btn btn-app bg-indigo" href="{{ route('project.edit', $project['id']) }}">
                            <i class="fas fa-pencil-alt">
                            </i>
                            {{ __('general.change') }}
                        </a>
                    @endif
                @endcan
                @can ('remove projects')
                    <button
                        class="btn btn-app bg-danger delete-btn ajax-delete-row"
                        data-action="delete_row"
                        data-object="project"
                        data-type="static"
                        data-object-id="{{ $project->id }}">
                        <i class="fas fa-trash">
                        </i>
                        {{ __('general.remove') }}
                    </button>
                @endcan
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
