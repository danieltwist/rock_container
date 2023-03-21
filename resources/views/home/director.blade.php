@extends('layouts.project')
@section('title', __('home.home_page'))
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('home.home_page') }}</h1>
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
                            <h4>{{ $active_project_count }}</h4>
                            <p>{{ __('home.active_projects_count') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="small-box bg-gradient-navy">
                        <div class="inner">
                            <h4>{{ $this_month_projects_count }}</h4>
                            <p>{{ __('home.added_this_month_projects_count') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="small-box bg-gradient-indigo">
                        <div class="inner">
                            <h4>{{ number_format($active_projects_estimated_profit, 0, '.', ' ') }}р.</h4>
                            <p>{{ __('home.active_projects_estimated_profit') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="small-box bg-gradient-success">
                        <div class="inner">
                            <h4>{{ number_format($this_month_total_profit, 0, '.', ' ') }}р.</h4>
                            <p>{{ __('home.this_month_total_profit') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('home.active_projects_list') }}</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"
                                title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if($active_project_count != 0)
                        <div class="projects_homepage" data-type="active">
                            @include('project.layouts.projects_table_ajax_filter', ['filter' => 'active'])
                        </div>
                    @else
                        {{ __('home.no_active_projects') }}
                    @endif
                </div>
                @if($active_project_count != 0)
                    <div class="card-footer">
                        <form action="{{ route('projects_export_with_filter_to_excel') }}" method="GET">
                            @csrf
                            @php
                                $parameters = [
                                    'filename' => 'все_проекты_в_работе',
                                    'sorting_type' => 'Все проекты в работе',
                                ];
                            @endphp
                            <input type="hidden" name="filter" value="active">
                            <input type="hidden" name="parameters" value="{{ serialize($parameters) }}">
                            <button type="submit" class="btn btn-success download_file_directly">
                                <i class="fas fa-file-excel"></i>
                                {{ __('general.export_projects_to_excel') }}
                            </button>
                        </form>
                    </div>
                @endif
            </div>
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">{{ __('home.invoices_need_agree') }}</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"
                                title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if($invoices_count != 0)
                        <div class="invoices_homepage" data-type="for_approval">
                            @include('invoice.table.invoices_table_ajax_filter', ['filter' => 'for_approval'])
                        </div>
                    @else
                        {{ __('home.no_invoices_need_agree') }}
                    @endif
                </div>
                @if($invoices_count != 0)
                    <div class="card-footer">
                        <form action="{{ route('invoices_export_with_filter_to_excel') }}" method="GET">
                            @csrf
                            @php
                                $parameters = [
                                    'filename' => 'все_счета_на_согласовании',
                                    'sorting_type' => 'Все счета на согласовании',
                                    'export_type' => 'Cчета на согласовании'
                                ];
                            @endphp
                            <input type="hidden" name="filter" value="for_approval">
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
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('home.projects_finished_this_month') }}</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"
                                title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if($this_month_finished_projects_count != 0)
                        <div class="projects_homepage" data-type="finished_this_month">
                            @include('project.layouts.projects_table_ajax_filter', ['filter' => 'finished_this_month'])
                        </div>
                    @else
                        {{ __('home.no_projects_finished_this_month') }}
                    @endif
                </div>
                @if($this_month_finished_projects_count != 0)
                    <div class="card-footer">
                        <form action="{{ route('projects_export_with_filter_to_excel') }}" method="GET">
                            @csrf
                            @php
                                $parameters = [
                                    'filename' => 'проекты_завершенные_в_этом_месяцы',
                                    'sorting_type' => 'Все завершенные в этом месяце проекты',
                                ];
                            @endphp
                            <input type="hidden" name="filter" value="finished_this_month">
                            <input type="hidden" name="parameters" value="{{ serialize($parameters) }}">
                            <button type="submit" class="btn btn-success download_file_directly">
                                <i class="fas fa-file-excel"></i>
                                {{ __('general.export_projects_to_excel') }}
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('home.draft_projects') }}</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"
                                title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if($draft_projects_count != 0)
                        <div class="projects_homepage" data-type="draft">
                            @include('project.layouts.projects_table_ajax_filter', ['filter' => 'draft'])
                        </div>
                    @else
                        {{ __('home.no_draft_projects') }}
                    @endif
                </div>
                @if($draft_projects_count != 0)
                    <div class="card-footer">
                        <form action="{{ route('projects_export_with_filter_to_excel') }}" method="GET">
                            @csrf
                            @php
                                $parameters = [
                                    'filename' => 'черновики_проектов',
                                    'sorting_type' => 'Все черновики проектов',
                                ];
                            @endphp
                            <input type="hidden" name="filter" value="draft">
                            <input type="hidden" name="parameters" value="{{ serialize($parameters) }}">
                            <button type="submit" class="btn btn-success download_file_directly">
                                <i class="fas fa-file-excel"></i>
                                {{ __('general.export_projects_to_excel') }}
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
        @include('project.modals.confirm_invoice')
    </section>
@endsection
