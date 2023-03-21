@extends('layouts.project')
@section('title', __('container.show_container'))
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">{{ __('general.container') }} {{ $container->name }} {{ __('container.in_own') }}
                        <a href="{{ route('container.index').'?own' }}" class="btn btn-default">
                            {{ __('container.all_own_containers') }}
                        </a>
                    </h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @include('layouts.info_block')
            <div class="row">
                <div class="col-md-3 col-6">
                    <div class="small-box bg-gradient-primary">
                        <div class="inner">
                            <h4>{{ number_format($container->info['income'], 0, '.', ' ') }}р.</h4>
                            <p>{{ __('container.income') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="small-box bg-gradient-navy">
                        <div class="inner">
                            <h4>{{ number_format($container->info['outcome'], 0, '.', ' ') }}р.</h4>
                            <p>{{ __('container.outcome') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="small-box bg-gradient-indigo">
                        <div class="inner">
                            <h4>{{ number_format($container->info['profit'], 0, '.', ' ') }}р.</h4>
                            <p>{{ __('container.project_profit') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="small-box bg-gradient-success">
                        <div class="inner">
                            <h4>{{ number_format($container->info['profitability'], 0, '.', ' ') }}р.</h4>
                            <p>{{ __('container.profitability') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('container.container_info') }}</h3>
                        </div>
                        <div class="card-body">
                            <b>{{ __('general.container') }}:</b> {{ $container->name }}<br>
                            <b>{{ $container->type }}: </b>{{ optional($container->supplier)->name }}<br>
                            <b>{{ __('container.using_in_project') }}:</b>
                            @if(!is_null($container->project))
                                <a href="{{ route('project.show', optional($container->project)->id) }}">{{ optional($container->project)->name }}</a><br>
                            @else
                                {{ __('container.not_using_in_project') }}
                            @endif
                            <br>
                            <br>
                            @if($container->start_date_for_us != '')
                                <b>{{ __('container.start_date_for_us') }}:</b>
                                <a href="#" class="xedit"
                                   data-pk="{{$container->id}}"
                                   data-name="start_date_for_us"
                                   data-model="Container">
                                    {{ $container->start_date_for_us }}
                                </a>
                            @else
                                <form action="{{ route('container.update', $container->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="action" value="start_use_container_for_us">
                                    <input type="hidden" name="static" value="yes">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary btn-sm">{{ __('container.set_start_for_us') }}</button>
                                    </div>
                                </form>
                            @endif
                            @if($container->start_date_for_client != '')
                                <br>
                                <b>{{ __('container.start_date_for_client') }}:</b>
                                <a href="#" class="xedit"
                                   data-pk="{{$container->id}}"
                                   data-name="start_date_for_client"
                                   data-model="Container">
                                    {{ $container->start_date_for_client }}
                                </a>
                            @else
                                <form action="{{ route('container.update', $container->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="action" value="start_use_container_for_client">
                                    <input type="hidden" name="static" value="yes">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-info btn-sm">{{ __('container.set_start_for_client') }}</button>
                                    </div>
                                </form>
                            @endif
                            <br>
                            <br>
                            {{ __('container.using_conditions') }}: <br>
                            <b>{{ __('container.grace_period') }} {{ __('container.days') }}:</b>
                            {{ !is_null($container->grace_period_for_client) ? $container->grace_period_for_client : '-' }}
                            /
                            {{ !is_null($container->grace_period_for_us) ? $container->grace_period_for_us : '-' }}  <br>
                            @if ($container->start_date_for_us != '' || $container->start_date_for_client != '')
                                <b>{{ __('container.end_grace_period') }}:</b> {{ $container->usage_dates['end_grace_date']}} / {{ $container->usage_dates['end_grace_date_for_us']}}<br>
                                @if($container->usage_dates['overdue_days'] != 0 || $container->usage_dates['overdue_days_for_us'] != 0)
                                    <b>{{ __('container.overdue_days') }}:</b> {{ $container->usage_dates['overdue_days'] }} ({{ $container->usage_dates['snp_amount_for_client'] }})
                                    / {{ $container->usage_dates['overdue_days_for_us'] }} ({{ $container->usage_dates['snp_amount_for_us'] }})<br>
                                @endif
                            @endif
                            <br>
                            <b>{{ __('container.snp_amount_client') }}:</b>
                            @if($container->usage_dates['range_client'] != '' || $container->snp_amount_for_client != '' )
                                {{ $container->usage_dates['range_client'] != '' ? $container->usage_dates['range_client'].', ' . __('container.later') : '' }}
                                {{ $container->snp_amount_for_client }}{{ $container->snp_currency }} {{ __('container.in_day') }}
                            @else
                                -
                            @endif
                            <br>
                            <b>{{ __('container.snp_amount_us') }}:</b>
                            @if($container->usage_dates['range_us'] != '' || $container->snp_amount_for_us != '')
                                {{ $container->usage_dates['range_us'] != '' ? $container->usage_dates['range_us'].', ' . __('container.later') : '' }}
                                {{ $container->snp_amount_for_us }}{{ $container->snp_currency }} {{ __('container.in_day') }}
                            @else
                                -
                            @endif
                            <br><br>
                            <b>{{ __('container.border_date') }}:</b> {{ $container->border_date != '' ? $container->border_date : __('container.not_set') }}<br>
                            <b>{{ __('container.svv') }}: </b>
                            <a href="#"
                               class="xedit"
                               data-pk="{{$container->id}}"
                               data-name="svv"
                               data-model="Container">
                                {{ $container->svv == '' ? __('container.not_set') : $container->svv}}
                            </a>
                            <br>
                            @if(!is_null($container->problem))
                                <div class="mt-4">
                                    <b>{{ __('container.problem') }}:</b><br>
                                    {{ __('container.problem_type') }}: {{ $container->problem->problem }}<br>
                                    {{ __('container.problem_date') }}: {{ $container->problem->problem_date }}<br>
                                    {{ __('container.problem_who_fault') }}: {{ $container->problem->who_fault }}<br><br>

                                    @if ($container->problem->problem_solved_date !='')
                                        <b>{{ __('container.problem_solved_date') }}:</b> {{ $container->problem->problem_solved_date }}<br>
                                        <b>{{ __('container.problem_amount') }}:</b> {{ $container->problem->amount }}<br><br>
                                    @endif
                                    <b>{{ __('general.additional_info') }}:</b> {{ $container->problem->additional_info }}
                                    <br><br>
                                    <a class="btn btn-primary"
                                       href="{{ route('container_problem.show', $container->problem_id) }}">
                                        {{ __('container.show_problem') }}
                                    </a>
                                </div>
                            @endif
                            @can('edit own containers')
                            <div class="mt-4">
                                    <button class="btn btn-default btn-sm mt-2" type="button"
                                            data-toggle="collapse"
                                            data-target="#ownContainerInfo"
                                            aria-expanded="false"
                                            aria-controls="collapseExample">
                                        <i class="fa fa-angle-down"></i>
                                        {{ __('container.own_container_info') }}
                                    </button>
                                    <div class="collapse mt-2" id="ownContainerInfo">
                                        <div class="card card-body">
                                            <form action="{{ route('own_container.store') }}" method="POST">
                                                @csrf
                                            <input type="hidden" name="container_id" value="{{ $container->id }}">
                                            <div class="form-group">
                                                <label for="prime_cost">{{ __('container.own_container_prime_cost') }}</label>
                                                <input type="text" class="form-control digits_only"
                                                       name="prime_cost"
                                                       placeholder="{{ __('container.own_container_prime_cost') }}"
                                                       value="{{ !is_null($container->own) ? $container->own->prime_cost : '' }}">
                                            </div>
                                            <div class="form-group">
                                                <label for="date_of_purchase">{{ __('container.own_container_date_of_purchase') }}</label>
                                                <input type="text" class="form-control date_input"
                                                       name="date_of_purchase"
                                                       placeholder="{{ __('container.own_container_date_of_purchase') }}"
                                                       value="{{ !is_null($container->own) ? $container->own->date_of_purchase : '' }}">
                                            </div>
                                            <div class="form-group">
                                                <label for="place_of_purchase">{{ __('container.own_container_place_of_purchase') }}</label>
                                                <input type="text" class="form-control to_uppercase"
                                                       name="place_of_purchase"
                                                       placeholder="{{ __('container.own_container_place_of_purchase') }}"
                                                       value="{{ !is_null($container->own) ? $container->own->place_of_purchase : '' }}">
                                            </div>
                                            <div class="form-group">
                                                <label>{{ __('general.additional_info') }}</label>
                                                <textarea class="form-control to_uppercase" rows="3"
                                                          name="additional_info"
                                                          placeholder="{{ __('general.additional_info') }}">{{ !is_null($container->own) ? $container->own->additional_info : '' }}</textarea>
                                            </div>
                                                <button type="submit" class="btn btn-primary">{{ __('general.save') }}</button>
                                            </form>
                                        </div>
                                    </div>
                            </div>
                            @endcan
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('container.edit', $container->id) }}" class="btn btn-primary">
                                {{ __('container.edit_container') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    @if (canWorkWithProject($container->project_id))
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('container.prolong_svv') }}</h3>
                            </div>
                            <div class="card-body">
                                @if($container->project_id !='')
                                    <form action="{{ route('container.update', $container->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="action" value="prolong_svv">
                                        <div class="form-group">
                                            <label for="svv_prolong_to">{{ __('container.new_svv_date') }}</label>
                                            <input type="text" class="form-control date_input" name="svv_prolong_to"
                                                   placeholder="{{ __('container.new_svv_date') }}"
                                                   value="{{ $container->usage_dates['svv_date'] }}" required>
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                {{ __('general.update') }}
                                            </button>
                                        </div>
                                    </form>
                                @else
                                    {{ __('container.not_using_in_project') }}
                                @endif
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('container.make_return') }}</h3>
                            </div>
                            <div class="card-body">
                                @if($container->project_id !='')
                                    <form action="{{ route('container.update', $container->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="action" value="return_container">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-success btn-sm invoice-confirm-btn">{{ __('container.make_return') }}</button>
                                        </div>
                                    </form>
                                @elseif($container->problem_id !='')
                                    {{ __('container.problem_found') }}
                                @else
                                    {{ __('container.not_using_in_project') }}
                                @endif
                            </div>
                        </div>
                    @else
                        @include('error.not_used_now')
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('container.container_projects') }}</h3>
                            <div class="card-tools">
                                <a type="button" class="btn btn-primary btn-sm"
                                   href="{{ route('container_project.create').'?container_id='.$container->id }}">
                                    <i class="fas fa-plus"></i> {{ __('container.project_add') }}
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($container_projects->isNotEmpty())
                                @include('container.project.table')
                            @else
                                {{ __('container.projects_not_found') }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('container.used_before') }}</h3>
                        </div>
                        <div class="card-body">
                            @if($usage_statistics->isNotEmpty())
                                <table class="table table-striped data_tables">
                                    <thead>
                                    <tr>
                                        <th style="width: 20%">
                                            {{ __('project.project') }}
                                        </th>
                                        <th>
                                            {{ __('container.start_using_for_us_and_client') }}
                                        </th>
                                        <th>
                                            {{ __('container.border') }}
                                        </th>
                                        <th>
                                            {{ __('container.return') }}
                                        </th>
                                        <th>
                                            {{ __('container.snp_for_client_and_us') }}
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($usage_statistics as $statistic)
                                        <tr>
                                            <td>
                                                @if(!is_null($statistic->project))
                                                    <a href="{{ route('project.show', optional($statistic->project)->id) }}">{{ optional($statistic->project)->name }}</a>
                                                @else
                                                    {{ __('general.project_was_delete') }}
                                                @endif
                                            </td>
                                            <td>{{ $statistic->start_date_for_us }} / {{ $statistic->start_date_for_client }}</td>
                                            <td>{{ $statistic->border_date }}</td>
                                            <td>{{ $statistic->return_date }}</td>
                                            <td>{{ $statistic->snp_days_for_client }} дней ({{ $statistic->snp_total_amount_for_client }})
                                                / {{ $statistic->snp_days_for_us }} дней ({{ $statistic->snp_total_amount_for_us }})</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @else
                                {{ __('container.never_used_before') }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('container.problems_with_container') }}</h3>
                        </div>
                        <div class="card-body">
                            @if($container_problems->isNotEmpty())
                                <table class="table table-striped data_tables">
                                    <thead>
                                    <tr>
                                        <th style="width: 20%">
                                            {{ __('container.problem') }}
                                        </th>
                                        <th>
                                            {{ __('container.problem_date_table') }}
                                        </th>
                                        <th>
                                            {{ __('container.problem_solve_date_table') }}
                                        </th>
                                        <th>
                                            {{ __('container.problem_who_fault_table') }}
                                        </th>
                                        <th>
                                            {{ __('container.problem_solution_amount_table') }}
                                        </th>
                                        <th>
                                            {{ __('general.actions') }}
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($container_problems as $problem)
                                        <tr>
                                            <td>
                                                {{ $problem->problem }}<br>
                                                <small>{{ $problem->additional_info }}</small>
                                            </td>
                                            <td>{{ $problem->problem_date }}</td>
                                            <td>{{ $problem->problem_solved_date }}</td>
                                            <td>{{ $problem->who_fault }}</td>
                                            <td>{{ $problem->amount }}</td>
                                            <td> <a class="btn btn-primary" href="{{ route('container_problem.show', $problem->id) }}">
                                                    {{ __('container.show_problem') }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @else
                                {{ __('container.problems_with_container_not_found') }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
