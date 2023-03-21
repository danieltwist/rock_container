@extends('layouts.project')
@section('title', __('project.analytics'))
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            @include('layouts.info_block')
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('project.analytics') }}
                        <a href="{{ route('project.index').'?finished' }}" class="btn btn-default">
                            {{ __('project.back_to_finished_projects') }}
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
                            <p>{{ __('project.chosen_projects_count') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="small-box bg-gradient-navy">
                        <div class="inner">
                            <h4>{{ number_format($cost, 0, '.', ' ') }}р.</h4>
                            <p>{{ __('project.expenses') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="small-box bg-gradient-indigo">
                        <div class="inner">
                            <h4>{{ number_format($price, 0, '.', ' ') }}р.</h4>
                            <p>{{ __('project.income') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="small-box bg-gradient-success">
                        <div class="inner">
                            <h4>{{ number_format($profit, 0, '.', ' ') }}р.</h4>
                            <p>{{ __('project.profit') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('project.chosen_projects') }}</h3>
                            <div class="card-tools">
                                <form action="{{ route('export_projects_list') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="data_range" id="data_range" value="{{ $data_range }}">
                                    <button type="submit" class="btn btn-block btn-success btn-xs"
                                            data-action='{"download_file":{"need_download": "true"}}'>
                                        <i class="fas fa-file-excel"></i>
                                        {{ __('project.export') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="projects_analytics" data-type="{{ $filter_type }}">
                                @include('project.layouts.projects_table_ajax_filter', ['filter' => $filter_type])
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('project.income_list') }}</h3>
                        </div>
                        <div class="card-body">
                            @php
                                $invoices = $out_invoices;
                            @endphp
                            <div class="invoices_analytics" data-type="out">
                                @include('invoice.table.invoices_table_ajax_filter', ['filter' => 'out'])
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('project.outcome_list') }}</h3>
                        </div>
                        <div class="card-body">
                            @php
                                $invoices = $in_invoices;
                            @endphp
                            <div class="invoices_analytics" data-type="in">
                                @include('invoice.table.invoices_table_ajax_filter', ['filter' => 'in'])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('project.modals.confirm_invoice')
    </section>
@endsection

