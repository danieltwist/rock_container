@extends('layouts.project')

@section('title', __('project.show_project'))

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">{{ __('project.project') }} {{ $project->name }}
                        <a href="{{ route('project.index') }}" class="btn btn-default">{{ __('project.all_projects') }}</a>
                    </h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @include('layouts.info_block')
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header {{ is_null($project->deleted_at) ?: 'bg-danger' }}">
                            <h3 class="card-title">{{ __('project.information') }}</h3>
                            @can ('edit projects paid status')
                                <div class="card-tools">
                                    <form action="{{ route('project.update', $project->id) }}" method="post">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="action" value="update_paid_status">
                                        <div class="input-group input-group-sm">
                                            <select class="custom-select rounded-0" name="paid">
                                                <option
                                                    value="Не оплачен" {{ $project->paid =='Не оплачен' ? 'selected' : '' }}>
                                                    {{ __('project.not_paid') }}
                                                </option>
                                                <option
                                                    value="Оплачен" {{ $project->paid =='Оплачен' ? 'selected' : '' }}>
                                                    {{ __('project.paid') }}
                                                </option>
                                            </select>
                                            <span class="input-group-append">
                                                <button type="submit" class="btn btn-info btn-flat"
                                                    data-action='{"do_nothing":{"value": "true"}}'>{{ __('general.save') }}</button>
                                                <button type="button" data-toggle="modal" data-target="#view_component_history"
                                                        class="btn btn-default btn-sm float-right"
                                                        data-component="project"
                                                        data-id="{{ $project->id }}">
                                                    <i class="fas fa-history"></i>
                                                    История
                                                </button>
                                            </span>
                                        </div>
                                    </form>

                                </div>
                            @endcan
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="info-box bg-light">
                                        <div class="info-box-content">
                                            <span class="info-box-text text-center text-muted">{{ __('project.planned_income') }}</span>
                                            <span class="info-box-number text-center text-muted mb-0">
                                                {{ !is_null($project->expense) ? number_format($project->expense->price_in_rub, 0, '.', ' ').'р.' : 'Не установлено' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-box bg-light">
                                        <div class="info-box-content">
                                            <span class="info-box-text text-center text-muted">{{ __('project.planned_outcome') }}</span>
                                            <span class="info-box-number text-center text-muted mb-0">
                                                {{ !is_null($project->expense) ? number_format($project->expense->planned_costs, 0, '.', ' ').'р.' : 'Не установлено' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-box bg-light">
                                        <div class="info-box-content">
                                            <span class="info-box-text text-center text-muted">{{ __('project.planned_profit') }}</span>
                                            <span class="info-box-number text-center text-muted mb-0">
                                                {{ !is_null($project->expense) ? number_format($project->expense->planned_profit, 0, '.', ' ').'р.' : 'Не установлено' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="info-box bg-light">
                                        <div class="info-box-content">
                                            <span
                                                class="info-box-text text-center text-muted">{{ __('project.fact_income') }}</span>
                                            <span class="info-box-number text-center text-muted mb-0">
                                                {{ number_format($finance['total_price'], 0, '.', ' ') }}р.
                                                @if($finance['total_price'] != $finance['price'])
                                                    / {{ number_format($finance['price'], 0, '.', ' ') }}р.
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-box bg-light">
                                        <div class="info-box-content">
                                            <span
                                                class="info-box-text text-center text-muted">{{ __('project.fact_outcome') }}</span>
                                            <span class="info-box-number text-center text-muted mb-0">
                                                {{ number_format($finance['total_cost'], 0, '.', ' ') }}р.
                                                @if($finance['total_cost'] != $finance['cost'])
                                                    / {{ number_format($finance['cost'], 0, '.', ' ') }}р.
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-box bg-light">
                                        <div class="info-box-content">
                                            <span
                                                class="info-box-text text-center text-muted">{{ __('project.fact_profit') }}</span>
                                            <span class="info-box-number text-center text-muted mb-0">
                                                {{ number_format($finance['total_profit'], 0, '.', ' ') }}р.
                                                @if($finance['total_profit'] != $finance['profit'])
                                                    / {{ number_format($finance['profit'], 0, '.', ' ') }}р.
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="text-muted mt-3">
                                        <p class="text-sm">{{ __('project.client_company') }}
                                            @if(!is_null(optional($project->client)->id))
                                                <b class="d-block">
                                                    <a class="text-dark" href="{{ route('client.show', optional($project->client)->id) }}">
                                                        {{ optional($project->client)->name}}
                                                    </a>
                                                </b>
                                            @endif
                                        </p>
                                        @if ($project->additional_clients != '')
                                            <p class="text-sm">{{ __('project.additional_clients') }}
                                                @foreach($project->additional_client() as $client)
                                                    <b class="d-block">{{ $client }}</b>
                                                @endforeach
                                            </p>
                                        @endif
                                        @if(!is_null ($project->freight_info))
                                            <p class="text-sm">{{ __('project.goods_info') }}
                                                <b class="d-block">{{ $project->freight_info }} {{ $project->freight_amount }}</b>
                                            </p>
                                        @endif
                                        <p class="text-sm">Создан / Менеджер / Доступ к проекту
                                            <b class="d-block">
                                                <a class="text-dark" href="{{ route('get_user_statistic', optional($project->user)->id) }}">{{ optional($project->user)->name}}</a>
                                                /
                                                @if($project->manager_id != '')
                                                     <a class="text-dark" href="{{ route('get_user_statistic', optional($project->manager)->id) }}">{{ optional($project->manager)->name}}</a>
                                                @else
                                                    Не выбран
                                                @endif
                                                /
                                                {!! $access_to_project !!}
                                            </b>
                                        </p>
                                        <p class="text-sm">{{ __('project.current_stage') }}
                                            <b class="d-block">
                                                @if ($project->active_block!='')
                                                    {{ $project->active_block->name }}
                                                @else
                                                    {{ __('general.not_set') }}
                                                @endif
                                            </b>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="text-muted mt-3">
                                        <p class="text-sm">{{ __('project.pre_payment') }}
                                            <b class="d-block">{{ !is_null($project->prepayment) ? $project->prepayment : __('general.not_set') }}</b>
                                        </p>
                                        <p class="text-sm">{{ __('project.planned_payment_date') }}
                                            <b class="d-block">{{ !is_null($project->planned_payment_date) ? $project->planned_payment_date->format('d.m.Y') : __('general.not_set') }}</b>
                                        </p>
                                        <p class="text-sm">{{ __('project.planned_income_in_rub') }}
                                            <b class="d-block">{{ !is_null($project->expense) ? number_format($project->expense->price_in_rub, 0, '.', ' ').'р.' : __('general.not_set') }}</b>
                                        </p>
                                        @if(!is_null($project->expense))
                                            @if ($project->expense->currency != 'RUB')
                                                <p class="text-sm">{{ __('project.planned_income_in_currency') }}
                                                    <b class="d-block">{{ !is_null($project->expense) ? $project->expense->price_in_currency.$project->expense->currency.'('.
                                                    $project->expense->price_1pc.$project->currency.'x'.$project->expense->amount : __('general.not_set') }}
                                                        )</b>
                                                </p>
                                                <p class="text-sm">{{ __('project.exchange_rate_create_date_today') }}
                                                    <b class="d-block">{{ !is_null($project->expense) ? $project->expense->cb_rate : __('general.not_set') }}
                                                        / {{$finance['today_rate']}}</b>
                                                </p>
                                            @endif
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="text-muted mt-3">
                                        @if (!is_null($project->expense->expenses_array))
                                            @foreach(unserialize($project->expense->expenses_array) as $expense)
                                                @php
                                                    $type = $expense['type'];
                                                @endphp
                                                <p class="text-sm">{{ $expense[$type.'_name'] }}
                                                    <b class="d-block">
                                                        {{ $expense[$type.'_price_1pc'].$expense[$type.'_currency'] }}
                                                        {{ $expense[$type.'_currency']!='RUB' ? '('. $expense[$type.'_rate'] .'р.)' : ''}}
                                                        x
                                                        {{ $expense[$type.'_amount'] }} =
                                                        {{ $expense[$type.'_total_price_in_rub'] }}р.
                                                    </b>
                                                </p>
                                            @endforeach
                                        @else
                                            <p class="text-sm">
                                                {{ __('project.cost_part_not_filled') }}
                                            </p>
                                        @endif
                                        @if(!is_null($project->expense->snp_amount))
                                            @if($project->expense->snp_amount['snp_for_us_usd'] != 0 || $project->expense->snp_amount['snp_for_us_cny'] != 0
                                                    || $project->expense->snp_amount['snp_for_us_rub'] != 0)
                                                <p class="text-sm">
                                                    {{ __('project.snp_for_us') }}:
                                                    <b class="d-block">
                                                        @if($project->expense->snp_amount['snp_for_us_usd'] != 0)
                                                            {{ number_format($project->expense->snp_amount['snp_for_us_usd'], 0, '.', ' ') }}USD ({{ number_format($rates->USD*$project->expense->snp_amount['snp_for_us_usd'], 0, '.', ' ') }}р.)
                                                        @endif
                                                        @if($project->expense->snp_amount['snp_for_us_cny'] != 0)
                                                            {{ number_format($project->expense->snp_amount['snp_for_us_cny'], 0, '.', ' ') }}CNY ({{ number_format($rates->CNY*$project->expense->snp_amount['snp_for_us_cny'], 0, '.', ' ') }}р.)
                                                        @endif
                                                        @if($project->expense->snp_amount['snp_for_us_rub'] != 0)
                                                            {{ number_format($project->expense->snp_amount['snp_for_us_rub'], 0, '.', ' ') }}р.
                                                        @endif
                                                    </b>
                                                </p>
                                            @endif
                                            @if($project->expense->snp_amount['snp_for_client_usd'] != 0 || $project->expense->snp_amount['snp_for_client_cny'] != 0
                                                || $project->expense->snp_amount['snp_for_client_rub'] != 0)
                                                <p class="text-sm">
                                                    {{ __('project.snp_for_client') }}:
                                                    <b class="d-block">
                                                        @if($project->expense->snp_amount['snp_for_client_usd'] != 0)
                                                            {{ number_format($project->expense->snp_amount['snp_for_client_usd'], 0, '.', ' ') }}USD ({{ number_format($rates->USD*$project->expense->snp_amount['snp_for_client_usd'], 0, '.', ' ') }}р.)
                                                        @endif
                                                        @if($project->expense->snp_amount['snp_for_client_cny'] != 0)
                                                            {{ number_format($project->expense->snp_amount['snp_for_client_cny'], 0, '.', ' ') }}CNY ({{ number_format($rates->CNY*$project->expense->snp_amount['snp_for_client_cny'], 0, '.', ' ') }}р.)
                                                        @endif
                                                        @if($project->expense->snp_amount['snp_for_client_rub'] != 0)
                                                            {{ number_format($project->expense->snp_amount['snp_for_client_rub'], 0, '.', ' ') }}р.
                                                        @endif
                                                    </b>
                                                </p>
                                            @endif
                                        @endif
                                        @if($exchange_difference != 0)
                                            <p class="text-sm">Курсовая разница
                                                <b class="d-block">
                                                    {{ number_format($exchange_difference, 2, '.', ' ').'р.' }}
                                                </b>
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    @if($project->from != '' || $project->pogranperehod != '' || $project->to != '')
                                        <h6 class="text-primary"><i
                                                class="fas fa-exchange-alt"></i> {{ $project->from }}
                                            - {{ $project->pogranperehod }}
                                            - {{ $project->to }}
                                        </h6>
                                    @endif
                                    {{ __('project.current_project_plan') }}
                                    <div class="text-muted mt-5">
                                        @if (count($blocks) !== 0)
                                            {{ __('project.added') }}:
                                            @foreach($blocks as $block)
                                                <li>
                                                    <b>{{ $block->name }} </b>
                                                    @if ($block->status == 'Завершен')
                                                        <i class="fas fa-check-circle"></i>
                                                    @endif
                                                </li>
                                            @endforeach
                                        @else
                                            {{ __('project.project_plan_not_created') }} <br>
                                        @endif
                                        <br>
                                        @if (canWorkWithProject($project->id) && $project->status != 'Завершен')
                                            @if (count($blocks) === 0)
                                                <a class="btn btn-primary"
                                                   href="{{ route('project_create_plan', $project->id) }}">
                                                    <i class="fas fa-list"></i>
                                                    {{ __('project.create_project_plan') }}
                                                </a>
                                            @else
                                                <a class="btn btn-primary"
                                                   href="{{ route('project_create_plan', $project->id) }}">
                                                    <i class="fas fa-list"></i>
                                                    {{ __('project.edit_project_plan') }}
                                                </a>
                                            @endif
                                        @endif
                                        <br><br>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 text-muted">
                                @if($project->additional_info!='')
                                    <p class="text-sm">{{ __('general.additional_info') }}
                                        <b class="d-block">{{ $project->additional_info }}</b>
                                    </p>
                                @endif
                                    {{ __('project.start_date') }}:
                                    @if (canWorkWithProject($project->id))
                                        <a href="#" class="xedit" data-pk="{{$project->id}}" data-name="created_at"
                                           data-model="Project">
                                            {{ $project->created_at->format('d.m.Y H:i:s')}}
                                        </a>
                                    @else
                                        {{ $project->created_at->format('d.m.Y H:i:s') }}
                                    @endif
                                    @if(!is_null($project->finished_at))
                                        <br>{{ __('project.finish_date') }}:
                                        @if (canWorkWithProject($project->id))
                                            <a href="#" class="xedit" data-pk="{{$project->id}}" data-name="finished_at"
                                               data-model="Project">
                                                {{ $project->finished_at->format('d.m.Y H:i:s') }}
                                            </a>
                                        @else
                                            {{ $project->finished_at->format('d.m.Y H:i:s') }}
                                        @endif
                                    @endif
                                    @if(!is_null($project->paid_at))
                                        <br>{{ __('project.payment_date') }}:
                                        @if (canWorkWithProject($project->id))
                                            <a href="#" class="xedit" data-pk="{{$project->id}}" data-name="paid_at"
                                               data-model="Project">
                                                {{ $project->paid_at->format('d.m.Y H:i:s') }}
                                            </a>
                                        @else
                                            {{ $project->paid_at->format('d.m.Y H:i:s') }}
                                        @endif
                                    @endif
                                </div>
                                <div class="col-12 mt-2">
                                    {{ __('project.project_finished_percent') }} {{ $complete_level }}%
                                    <div class="progress">
                                        <div class="progress-bar bg-primary progress-bar-striped"
                                             role="progressbar" aria-valuenow="{{ $complete_level }}"
                                             aria-valuemin="0" aria-valuemax="100"
                                             style="width: {{ $complete_level }}%">
                                            <span class="sr-only">{{ $complete_level }}% {{ __('project.project_finished_percent_simple') }}</span>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        @if(in_array($role, ['director', 'accountant', 'super-admin']))
                                            <a class="btn bg-success mt-2" href="{{ route('export_project', $project['id']) }}">
                                                <i class="fas fa-file-excel"></i>
                                                {{ __('general.export_to_excel') }}
                                            </a>
                                        @endif
                                        <a class="btn bg-primary mt-2"
                                           href="{{ route('recalculate_project_finance', $project['id']) }}">
                                            <i class="fas fa-calculator"></i>
                                            {{ __('project.recalculate') }}
                                        </a>
                                        @if ($can_finish_project && canWorkWithProject($project->id) && $project->status != 'Черновик')
                                            <form class="float-right" action="{{ route('finish_project') }}"
                                                  method="POST">
                                                @csrf
                                                <input type="hidden" name="project_id"
                                                       value="{{ $project->id }}">
                                                <button type="submit" class="btn btn-success confirm-btn mt-2">
                                                    <i class="fas fa-check"></i>
                                                    {{ __('project.finish_project') }}
                                                </button>
                                            </form>
                                        @endif

                                        @can ('edit projects')
                                            @if(can_edit_this_project($project->id))
                                                @if (!can_edit_this_project_price($project->id))
                                                    <a class="btn bg-orange mt-2"
                                                       data-toggle="modal"
                                                       data-target="#create_task_modal"
                                                       data-model="project"
                                                       data-text="Измените цену на проект"
                                                       data-user="Группа Директоры"
                                                       data-send_to="Группа Директоры"
                                                       data-model-id="{{ $project->id }}">
                                                        <i class="fas fa-briefcase"></i>
                                                        {{ __('project.ask_change_price') }}
                                                    </a>
                                                @else
                                                    <a class="btn bg-indigo mt-2"
                                                       href="{{ route('project.edit', $project['id']) }}">
                                                        <i class="fas fa-pencil-alt">
                                                        </i>
                                                        {{ __('project.edit_project') }}
                                                    </a>
                                                @endif
                                            @endif
                                        @endcan
                                        @if($role !='accountant')
                                            @php
                                                $task_to = 'Группа Бухгалтеры';
                                                $send_to = 'Группа Бухгалтеры';
                                            @endphp
                                        @else
                                            @php
                                                $task_to = optional($project->logist)->name;
                                                $send_to = optional($project->logist)->id;
                                            @endphp
                                        @endif
                                        <a class="btn bg-orange mt-2"
                                           data-toggle="modal"
                                           data-target="#create_task_modal"
                                           data-model="upd"
                                           data-text="Загрузите УПД для выбранных счетов"
                                           data-user="{{ $task_to }}"
                                           data-send_to="{{ $send_to }}"
                                           data-model-id="{{ $project->id }}">
                                            <i class="fas fa-briefcase"></i>
                                            {{ __('project.ask_upload_upd') }}
                                        </a>

                                        @if($project->status == 'Черновик' || $project->status == 'Завершен')
                                            @if(can_edit_this_project_price($project->id))
                                                <form class="button-delete-inline"
                                                      action="{{ route('set_status_in_work', $project['id']) }}"
                                                      method="POST">
                                                    @csrf
                                                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                                                    <button type="submit" class="btn bg-primary mt-2">
                                                        <i class="fas fa-check"></i>
                                                        {{ __('project.set_status_in_work') }}
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if (canWorkWithProject($project->id))
                        @can ('work with projects')
                            <div class="row">
                                @foreach($blocks as $block)
                                    <div class="col-md-4">
                                        @php
                                            $class = 'info'
                                        @endphp

                                        @switch($block->status)
                                            @case('В ожидании')
                                            @php $class = 'secondary' @endphp
                                            @break

                                            @case('В работе')
                                            @php  $class = 'primary' @endphp
                                            @break

                                            @case('Завершен')
                                            @php  $class = 'success' @endphp
                                            @break
                                        @endswitch

                                        <div class="card card-{{ $class }}">
                                            <div class="card-header">
                                                <h3 class="card-title">{{ $block->name }}
                                                    <br><small>{{ $block->status }}</small></h3>
                                                @if ($project->active_block_id == $block->id)
                                                    <div class="card-tools">
                                                        <i class="fas fa-check"></i>
                                                    </div>
                                                @elseif (!in_array($block->status, ['Завершен', 'В ожидании']))
                                                    <div class="card-tools">
                                                        <form action="{{ route('make_block_active') }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="project_id"
                                                                   value="{{ $project->id }}">
                                                            <input type="hidden" name="block_id"
                                                                   value="{{ $block->id }}">
                                                            <button type="submit"
                                                                    title="Установить в качестве текущего этапа проекта"
                                                                    class="btn btn-block btn-default btn-xs">
                                                                {{ __('project.block_make_current') }}
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="card-body">
                                                @if ($block->supplier_id == '')
                                                    {{ __('project.block_supplier_not_chose') }}<br>
                                                @else
                                                    {{ __('project.block_supplier') }}: {{ optional($block->supplier)->name }} <br>
                                                @endif
                                                <br>
                                                @if (!is_null($block->invoices))
                                                    @if ($block->invoices->sum('amount')!=0)
                                                        {{ __('project.block_planned_outcome') }}: {{ $block->invoices->sum('amount') }}р.<br>
                                                    @endif

                                                    @if ($block->invoices->sum('amount_actual')!=0)
                                                        {{ __('project.block_invoice_from_supplier') }}: {{ $block->invoices->sum('amount_actual') }}р.                                                        <br>
                                                    @endif

                                                    @if ($block->invoices->sum('amount_paid')!=0)
                                                        {{ __('project.block_paid') }}: {{ $block->invoices->sum('amount_paid') }}р. <br>
                                                    @endif
                                                @endif
                                                @if ($block->additional_info != '')
                                                    {{ __('project.block_additional_info') }}: @nl2br($block->additional_info) <br>
                                                @endif
                                            </div>
                                            <div class="card-footer">
                                                <a href="{{ route('block.show', $block->id) }}"
                                                   class="btn btn-default btn-block">{{ __('project.block_work_with_this') }}</a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endcan
                    @endif
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __('project.invoices_list') }}</h3>
                                    @if (canWorkWithProject($project->id))
                                        <div class="card-tools">
                                            <button type="button" data-toggle="modal" data-target="#make_invoice"
                                                    class="btn btn-primary btn-sm">
                                                <i class="fas fa-plus"></i>
                                                {{ __('project.add_invoice') }}
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-body">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-default btn-sm invoices_object_filters show_invoices_table"
                                                data-filter=""
                                                data-filter_type="direction">
                                            {{ __('project.show_all') }}
                                        </button>
                                        <button type="button" class="btn btn-default btn-sm invoices_object_filters show_invoices_table"
                                                data-filter="Доход"
                                                data-filter_type="direction"
                                                @if($project->management_expenses == 'on' && !in_array($role,['super-admin','director','accountant']))
                                                    data-personal="true"
                                                @endif
                                                >{{ __('project.show_income') }}
                                        </button>
                                        <button type="button" class="btn btn-default btn-sm invoices_object_filters show_invoices_table"
                                                data-filter="Расход"
                                                data-filter_type="direction"
                                                @if($project->management_expenses == 'on' && !in_array($role,['super-admin','director','accountant']))
                                                    data-personal="true"
                                                @endif
                                                >{{ __('project.show_outcome') }}
                                        </button>
                                        <button type="button" class="btn btn-default btn-sm show_alternative_block_invoices" data-filter="">
                                            2 столбца
                                        </button>
                                    </div>
                                    <div class="invoices_project mt-4 filter_table_div"
                                         id="standard_block_invoices"
                                         data-type="project"
                                         @if($project->management_expenses == 'on' && !in_array($role,['super-admin','director','accountant']))
                                            data-personal="true"
                                         @endif
                                         data-object_id="{{ $project->id }}">
                                        @include('invoice.table.invoices_table_ajax_filter', ['filter' => 'project'])
                                    </div>
                                    <div id="alternative_block_invoices" class="d-none mt-4">
                                        @include('project.layouts.invoices_two_columns.invoices_two_columns')
                                    </div>
                                </div>
                                @if(in_array($role, ['director', 'accountant', 'super-admin']))
                                    <div class="card-footer">
                                    <form action="{{ route('invoices_export_with_filter_to_excel') }}" method="GET">
                                        @csrf
                                        @php
                                            $parameters = [
                                                'filename' => $project->name.'_все_счета_проекта',
                                                'sorting_type' => 'Проект '.$project->name,
                                                'export_type' => 'Все счета проекта'
                                            ];
                                        @endphp
                                        <input type="hidden" name="project" value="{{ $project->id }}">
                                        @if(!in_array($role,['super-admin','director']))
                                            <input type="hidden" name="second_filter" value="my">
                                        @endif
                                        <input type="hidden" name="parameters" value="{{ serialize($parameters) }}">
                                        <button type="submit" class="btn btn-success download_file_directly"
                                                data-action='{"download_file":{"need_download": "true"}}'>
                                            <i class="fas fa-file-excel"></i>
                                            {{ __('general.export_invoice_to_excel') }}
                                        </button>
                                    </form>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <!-- Default box -->
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __('project.files') }}</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse"
                                                title="Collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row" id="project_files">
                                        @include('project.ajax.project_files')
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <a class="btn btn-primary float-right"
                                       onclick="window.open('/filemanager','','Toolbar=0,Location=0,Directories=0,Status=0,Menubar=0,Scrollbars=0,Resizable=0,Width=1400,Height=740');">
                                        {{ __('project.open_file_manager') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if (canWorkWithProject($project->id))
                        @can ('work with projects')
                            <div class="row">
                                <div class="col-md-12" id="upload_files_project">
                                    <input type="hidden" id="upload_files_project_id" value="{{ $project->id }}">
                                    @livewire('upload-files')
                                </div>
                            </div>
                        @endcan
                    @endif
                    <div class="fixed-bottom" id="project_comment_button">
                        @include('project.ajax.project_comments_button')
                    </div>
                </div>
        @include('project.modals.confirm_invoice')
        @include('project.modals.make_invoice_model')
        @include('project.modals.add_file')
        @include('audit.component_history_modal')
    </section>
@endsection
