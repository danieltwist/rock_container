@extends('layouts.project')
@section('title', 'Потенциальные убытки')
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">{{ __('report.potential_losses') }}</h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @include('layouts.info_block')
            <div class="row">
                <div class="col-md-4 col-6">
                    <div class="small-box bg-gradient-primary">
                        <div class="inner">
                            <h4>{{ $invoices_count }}</h4>
                            <p>{{ __('report.invoices_count') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-6">
                    <div class="small-box bg-gradient-navy">
                        <div class="inner">
                            <h4>{{ $projects_count }}</h4>
                            <p>{{ __('report.projects_with_potential_losses_count') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-6">
                    <div class="small-box bg-gradient-danger">
                        <div class="inner">
                            <h4>{{ number_format($total_amount, 0, '.', ' ') }}р.</h4>
                            <p>{{ __('report.potential_losses_total_amount') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('invoice.invoices_list') }}</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool form-inline" data-card-widget="collapse"
                                title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="invoices_debit_credit" data-type="potential_losses">
                        @include('invoice.table.invoices_table_ajax_filter', ['filter' => 'potential_losses'])
                    </div>
                </div>
                <div class="card-footer">
                    <form action="{{ route('losses_table_invoices_download') }}" method="GET">
                        @csrf
                        @php
                            $parameters = [
                                'filename' => 'потенциальные_убытки',
                                'sorting_type' => 'Потенциальные убытки'
                            ];
                        @endphp
                        <input type="hidden" name="filter" value="potential_losses">
                        <input type="hidden" name="parameters" value="{{ serialize($parameters) }}">
                        <button type="submit" class="btn btn-success download_file_directly"
                                data-action='{"download_file":{"need_download": "true"}}'>
                            <i class="fas fa-file-excel"></i>
                            {{ __('general.export_invoice_to_excel') }}
                        </button>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('report.projects_with_potential_losses') }}</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool form-inline" data-card-widget="collapse"
                                title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @include('project.layouts.projects_table_full_render')
                </div>
            </div>
        </div>
        @include('project.modals.confirm_invoice')
    </section>
@endsection
