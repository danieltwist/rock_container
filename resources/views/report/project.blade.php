@extends('layouts.project')
@section('title', __('report.projects_report'))
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">{{ __('report.projects_report') }}
                        <a href="{{ route('report_project_choose_type') }}" class="btn btn-default">
                            {{ __('report.back_to_parameters') }}
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
                            <h4>{{ $project_count }}</h4>
                            <p>{{ __('report.projects_count') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="small-box bg-gradient-navy">
                        <div class="inner">
                            <h4>{{ number_format($cost, 0, '.', ' ') }}р.</h4>
                            <p>{{ __('report.expenses') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="small-box bg-gradient-indigo">
                        <div class="inner">
                            <h4>{{ number_format($price, 0, '.', ' ') }}р.</h4>
                            <p>{{ __('report.income') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="small-box bg-gradient-success">
                        <div class="inner">
                            <h4>{{ number_format($profit, 0, '.', ' ') }}р.</h4>
                            <p>{{ __('report.profit') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('report.projects_list') }}</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"
                                            title="Collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="projects_report"
                                     data-type="{{ $filter_type }}"
                                     data-range="{{ $range }}"
                                     data-user_id="{{ $user_id }}"
                                     data-manager_id="{{ $manager_id }}">
                                    @include('project.layouts.projects_table_ajax_filter', ['filter' => $filter_type])
                                </div>
                            </div>
                            <div class="card-footer">
                                <form action="{{ route('projects_export_with_filter_to_excel') }}" method="GET">
                                    @csrf
                                    @php
                                        $parameters = [
                                            'filename' => 'отчет_по_проектам',
                                            'sorting_type' => $sorting_type_project,
                                        ];
                                    @endphp
                                    <input type="hidden" name="data_range" id="data_range" value="{{ $range }}">
                                    <input type="hidden" name="filter" value="{{ $filter_type }}">
                                    <input type="hidden" name="user_id" value="{{ $user_id }}">
                                    <input type="hidden" name="manager_id" value="{{ $manager_id }}">
                                    <input type="hidden" name="parameters" value="{{ serialize($parameters) }}">
                                    <button type="submit" class="btn btn-success download_file_directly"
                                            data-action='{"download_file":{"need_download": "true"}}'>
                                        <i class="fas fa-file-excel"></i>
                                        {{ __('general.export_projects_to_excel') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('report.income_list') }}</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"
                                            title="Collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="invoices_project_report"
                                     data-type="out"
                                     data-project_type="{{ $filter_type }}"
                                     data-range="{{ $range }}"
                                     data-user_id="{{ $user_id }}"
                                     data-manager_id="{{ $manager_id }}">
                                    @include('invoice.table.invoices_table_ajax_filter', ['filter' => 'out'])
                                </div>
                            </div>
                            <div class="card-footer">
                                <form action="{{ route('export_project_report_invoices_to_excel') }}" method="GET">
                                    @csrf
                                    @php
                                        $parameters = [
                                            'filename' => 'отчет_по_проектам_исходящие_счета',
                                            'sorting_type' => $sorting_type_invoice_out
                                        ];
                                        $project_filter_array = [
                                            'filter' => $filter_type,
                                            'user_id' => $user_id,
                                            'manager_id' => $manager_id
                                        ];
                                    @endphp

                                    <input type="hidden" name="filter" value="out">
                                    <input type="hidden" name="data_range" value="{{ $range }}">
                                    <input type="hidden" name="parameters" value="{{ serialize($parameters) }}">
                                    <input type="hidden" name="project_filter_array" value="{{ serialize($project_filter_array) }}">

                                    <button type="submit" class="btn btn-success download_file_directly"
                                            data-action='{"download_file":{"need_download": "true"}}'>
                                        <i class="fas fa-file-excel"></i>
                                        {{ __('general.export_invoice_to_excel') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('report.outcome_list') }}</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"
                                            title="Collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="invoices_project_report"
                                     data-type="in"
                                     data-project_type="{{ $filter_type }}"
                                     data-range="{{ $range }}"
                                     data-user_id="{{ $user_id }}"
                                     data-manager_id="{{ $manager_id }}">
                                    @include('invoice.table.invoices_table_ajax_filter', ['filter' => 'in'])
                                </div>
                            </div>
                            <div class="card-footer">
                                <form action="{{ route('export_project_report_invoices_to_excel') }}" method="GET">
                                    @csrf
                                    @php
                                        $parameters = [
                                            'filename' => 'отчет_по_проектам_входящие_счета',
                                            'sorting_type' => $sorting_type_invoice_out
                                        ];
                                        $project_filter_array = [
                                            'filter' => $filter_type,
                                            'user_id' => $user_id,
                                            'manager_id' => $manager_id
                                        ];
                                    @endphp

                                    <input type="hidden" name="filter" value="in">
                                    <input type="hidden" name="data_range" value="{{ $range }}">
                                    <input type="hidden" name="parameters" value="{{ serialize($parameters) }}">
                                    <input type="hidden" name="project_filter_array" value="{{ serialize($project_filter_array) }}">

                                    <button type="submit" class="btn btn-success download_file_directly"
                                            data-action='{"download_file":{"need_download": "true"}}'>
                                        <i class="fas fa-file-excel"></i>
                                        {{ __('general.export_invoice_to_excel') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        @include('project.modals.confirm_invoice')
    </section>
@endsection
