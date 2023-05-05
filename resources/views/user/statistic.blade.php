@extends('layouts.project')
@section('title', __('user.user_statistic'))
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">{{ __('user.user_statistic') }} {{ $user->name }}
                        <a href="{{ route('all_users') }}" class="btn btn-default">
                            {{ __('user.all_users') }}
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
                            <h4>{{ $active_projects_count }} / {{ number_format($active_projects_profit, 0, '.', ' ') }}р.</h4>
                            <p>{{ __('user.active_projects') }}</p>
                        </div>
                        <a href="#active_projects" class="small-box-footer smooth-scroll">
                            {{ __('general.show') }} <i class="fas fa-arrow-circle-down"></i>
                        </a>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="small-box bg-gradient-navy">
                        <div class="inner">
                            <h4>{{ $finished_projects_count }} / {{ number_format($finished_projects_profit, 0, '.', ' ') }}р.</h4>
                            <p>{{ __('user.finished_projects_count') }}</p>
                        </div>
                        <a href="#finished_projects" class="small-box-footer smooth-scroll">
                            {{ __('general.show') }} <i class="fas fa-arrow-circle-down"></i>
                        </a>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="small-box bg-gradient-success">
                        <div class="inner">
                            <h4>{{ $all_finished_projects_count }} / {{ number_format($all_finished_projects_profit, 0, '.', ' ') }}р.</h4>
                            <p>{{ __('user.total_finished_projects_count') }}</p>
                        </div>
                        <a href="#all_finished_projects" class="small-box-footer smooth-scroll">
                            {{ __('general.show') }} <i class="fas fa-arrow-circle-down"></i>
                        </a>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="small-box bg-gradient-indigo">
                        <div class="inner">
                            <h4>{{ $tasks_count }}</h4>
                            <p>{{ __('user.total_tasks_count') }}</p>
                        </div>
                        <a href="#user_tasks" class="small-box-footer smooth-scroll">
                            {{ __('general.show') }} <i class="fas fa-arrow-circle-down"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">История действий</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"
                                title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped audits_table" id="audits_table" data-filter_type="user" data-id="{{ $user->id }}">
                        <thead>
                        <tr>
                            <th style="width: 1%">
                                #
                            </th>
                            <th style="width: 10%">
                                Дата
                            </th>
                            <th style="width: 10%">
                                Пользователь
                            </th>
                            <th style="width: 15%">
                                Элемент
                            </th>
                            <th style="width: 10%">
                                Действие
                            </th>
                            <th style="width: 30%">
                                Версия до
                            </th>
                            <th style="width: 30%">
                                Версия после
                            </th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('user.all_projects') }}</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"
                                title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="projects_user"
                         data-filter_type="{{ $user->id }}_all_projects"
                         data-user_id="{{ $user->id }}">
                        @include('project.layouts.projects_table_ajax_filter', ['filter' => $user->id.'_all_projects'])
                    </div>
                </div>
                <div class="card-footer">
                    <form action="{{ route('projects_export_with_filter_to_excel') }}" method="GET">
                        @csrf
                        @php
                            $parameters = [
                                'filename' => $user->name.'_все_проекты',
                                'sorting_type' => 'Все проекты, созданные пользователем '.$user->name,
                            ];
                        @endphp
                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                        <input type="hidden" name="parameters" value="{{ serialize($parameters) }}">
                        <button type="submit" class="btn btn-success download_file_directly"
                                data-action='{"download_file":{"need_download": "true"}}'>
                            <i class="fas fa-file-excel"></i>
                            {{ __('general.export_projects_to_excel') }}
                        </button>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('user.all_invoices') }}</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"
                                title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="invoices_user" data-user_name="{{ $user->name }}">
                        @include('invoice.table.invoices_table_ajax_filter', ['filter' => 'user'])
                    </div>
                </div>
                <div class="card-footer">
                    <form action="{{ route('invoices_export_with_filter_to_excel') }}" method="GET">
                        @csrf
                        @php
                            $parameters = [
                                'filename' => $user->name.'_все_счета',
                                'sorting_type' => 'Все счета пользователя '.$user->name
                            ];
                        @endphp
                        <input type="hidden" name="user" value="{{ $user->name }}">
                        <input type="hidden" name="parameters" value="{{ serialize($parameters) }}">
                        <button type="submit" class="btn btn-success download_file_directly"
                                data-action='{"download_file":{"need_download": "true"}}'>
                            <i class="fas fa-file-excel"></i>
                            {{ __('general.export_invoice_to_excel') }}
                        </button>
                    </form>
                </div>
            </div>
            <div class="card" id="active_projects">
                <div class="card-header">
                    <h3 class="card-title">{{ __('project.active_projects') }}</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"
                                title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="projects_user"
                         data-filter_type="{{ $user->id }}_active_projects"
                         data-user_id="{{ $user->id }}"
                         data-filter="active">
                        @include('project.layouts.projects_table_ajax_filter', ['filter' => $user->id.'_active_projects'])
                    </div>
                </div>
                <div class="card-footer">
                    <form action="{{ route('projects_export_with_filter_to_excel') }}" method="GET">
                        @csrf
                        @php
                            $parameters = [
                                'filename' => $user->name.'_проекты_в_работе',
                                'sorting_type' => 'Проекты в работе, созданные пользователем '.$user->name,
                            ];
                        @endphp
                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                        <input type="hidden" name="filter" value="active">
                        <input type="hidden" name="parameters" value="{{ serialize($parameters) }}">
                        <button type="submit" class="btn btn-success download_file_directly">
                            <i class="fas fa-file-excel"></i>
                            {{ __('general.export_projects_to_excel') }}
                        </button>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('user.user_as_manager') }}</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"
                                title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="projects_user"
                         data-filter_type="{{ $user->id }}_active_manager"
                         data-manager_id="{{ $user->id }}"
                         data-filter="active">
                        @include('project.layouts.projects_table_ajax_filter', ['filter' => $user->id.'_active_manager'])
                    </div>
                </div>
                <div class="card-footer">
                    <form action="{{ route('projects_export_with_filter_to_excel') }}" method="GET">
                        @csrf
                        @php
                            $parameters = [
                                'filename' => $user->name.'_менеджер_проекты_в_работе',
                                'sorting_type' => 'Проекты в работе, в которых пользователь '.$user->name.' указан в качестве менеджера',
                            ];
                        @endphp
                        <input type="hidden" name="manager_id" value="{{ $user->id }}">
                        <input type="hidden" name="filter" value="active">
                        <input type="hidden" name="parameters" value="{{ serialize($parameters) }}">
                        <button type="submit" class="btn btn-success download_file_directly">
                            <i class="fas fa-file-excel"></i>
                            {{ __('general.export_projects_to_excel') }}
                        </button>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('user.user_as_logist') }}</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"
                                title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="projects_user"
                         data-filter_type="{{ $user->id }}_active_logist"
                         data-logist_id="{{ $user->id }}"
                         data-filter="active">
                        @include('project.layouts.projects_table_ajax_filter', ['filter' => $user->id.'_active_logist'])
                    </div>
                </div>
                <div class="card-footer">
                    <form action="{{ route('projects_export_with_filter_to_excel') }}" method="GET">
                        @csrf
                        @php
                            $parameters = [
                                'filename' => $user->name.'_логист_проекты_в_работе',
                                'sorting_type' => 'Проекты в работе, в которых пользователь '.$user->name.' указан в качестве логиста',
                            ];
                        @endphp
                        <input type="hidden" name="logist_id" value="{{ $user->id }}">
                        <input type="hidden" name="filter" value="active">
                        <input type="hidden" name="parameters" value="{{ serialize($parameters) }}">
                        <button type="submit" class="btn btn-success download_file_directly">
                            <i class="fas fa-file-excel"></i>
                            {{ __('general.export_projects_to_excel') }}
                        </button>
                    </form>
                </div>
            </div>
            <div class="card" id="finished_projects">
                <div class="card-header">
                    <h3 class="card-title">{{ __('user.finished_this_month') }}</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"
                                title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="projects_user"
                         data-filter_type="{{ $user->id }}_finished_this_month"
                         data-user_id="{{ $user->id }}"
                         data-filter="finished_this_month">
                        @include('project.layouts.projects_table_ajax_filter', ['filter' => $user->id.'_finished_this_month'])
                    </div>
                </div>
                <div class="card-footer">
                    <form action="{{ route('projects_export_with_filter_to_excel') }}" method="GET">
                        @csrf
                        @php
                            $parameters = [
                                'filename' => $user->name.'_проекты_завершенные_в_этом_месяце',
                                'sorting_type' => 'Проекты, созданные '.$user->name.' и завершенные в этом месяце',
                            ];
                        @endphp
                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                        <input type="hidden" name="filter" value="finished_this_month">
                        <input type="hidden" name="parameters" value="{{ serialize($parameters) }}">
                        <button type="submit" class="btn btn-success download_file_directly">
                            <i class="fas fa-file-excel"></i>
                            {{ __('general.export_projects_to_excel') }}
                        </button>
                    </form>
                </div>
            </div>
            <div class="card" id="all_finished_projects">
                <div class="card-header">
                    <h3 class="card-title">{{ __('user.all_finished_and_paid') }}</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"
                                title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="projects_user"
                         data-filter_type="{{ $user->id }}_all_finished"
                         data-user_id="{{ $user->id }}"
                         data-filter="finished">
                        @include('project.layouts.projects_table_ajax_filter', ['filter' => $user->id.'_all_finished'])
                    </div>
                </div>
                <div class="card-footer">
                    <form action="{{ route('projects_export_with_filter_to_excel') }}" method="GET">
                        @csrf
                        @php
                            $parameters = [
                                'filename' => $user->name.'_все_завершенные_и_оплаченные_проекты',
                                'sorting_type' => 'Все завершенные и оплаченные проекты, созданные '.$user->name,
                            ];
                        @endphp
                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                        <input type="hidden" name="filter" value="finished">
                        <input type="hidden" name="parameters" value="{{ serialize($parameters) }}">
                        <button type="submit" class="btn btn-success download_file_directly">
                            <i class="fas fa-file-excel"></i>
                            {{ __('general.export_projects_to_excel') }}
                        </button>
                    </form>
                </div>
            </div>
            <div class="card" id="user_tasks">
                <div class="card-header">
                    <h3 class="card-title">{{ __('user.all_tasks_with_user') }}</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"
                                title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @include('task.task_filters')
                    <div class="tasks_user mt-4"
                         data-user_id="{{ $user->id }}">
                        @include('task.table.task_ajax_table')
                    </div>
                </div>
            </div>
        </div>
        @include('project.modals.confirm_invoice')
    </section>
@endsection
