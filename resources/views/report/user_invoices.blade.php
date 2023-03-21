@extends('layouts.project')
@section('title', __('report.user_invoices_report'))
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">
                        {{ __('report.user_invoices_report') }} {{ $user }} {{ __('report.per') }} {{ $range_text }}
                        <a href="{{ route('report_user_invoices_choose_type') }}" class="btn btn-default">
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
                <div class="col-md-4 col-6">
                    <div class="small-box bg-gradient-primary">
                        <div class="inner">
                            <h4>{{ $invoices_count }}</h4>
                            <p>{{ __('report.total_invoices_added') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-6">
                    <div class="small-box bg-gradient-navy">
                        <div class="inner">
                            <h4>{{ $in_invoices_count }} / {{ number_format($cost, 0, '.', ' ') }}р.</h4>
                            <p>{{ __('report.expenses') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-6">
                    <div class="small-box bg-gradient-indigo">
                        <div class="inner">
                            <h4>{{ $out_invoices_count }} / {{ number_format($price, 0, '.', ' ') }}р.</h4>
                            <p>{{ __('report.income') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="row">
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
                                <div class="invoices_with_filters"
                                     data-table_id="user_out_invoices"
                                     data-filter="out"
                                     data-data_range="{{ $range }}"
                                     data-user="{{ $user }}">
                                    @include('invoice.table.invoices_table_ajax_filter', ['filter' => 'user_out_invoices'])
                                </div>
                            </div>
                            <div class="card-footer">
                                <form action="{{ route('invoices_export_with_filter_to_excel') }}" method="GET">
                                    @csrf
                                    @php
                                        $parameters = [
                                            'filename' => $user.'_отчет_по_исходящим_счетам',
                                            'sorting_type' => $sorting_type_invoice_out
                                        ];
                                    @endphp

                                    <input type="hidden" name="filter" value="out">
                                    <input type="hidden" name="user" value="{{ $user }}">
                                    <input type="hidden" name="data_range" value="{{ $range }}">
                                    <input type="hidden" name="parameters" value="{{ serialize($parameters) }}">

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
                                <div class="invoices_with_filters"
                                     data-table_id="user_in_invoices"
                                     data-filter="in"
                                     data-data_range="{{ $range }}"
                                     data-user="{{ $user }}">
                                    @include('invoice.table.invoices_table_ajax_filter', ['filter' => 'user_in_invoices'])
                                </div>
                            </div>
                            <div class="card-footer">
                                <form action="{{ route('invoices_export_with_filter_to_excel') }}" method="GET">
                                    @csrf
                                    @php
                                        $parameters = [
                                            'filename' => $user.'_отчет_по_входящим_счетам',
                                            'sorting_type' => $sorting_type_invoice_in
                                        ];
                                    @endphp
                                    <input type="hidden" name="filter" value="out">
                                    <input type="hidden" name="user" value="{{ $user }}">
                                    <input type="hidden" name="data_range" value="{{ $range }}">
                                    <input type="hidden" name="parameters" value="{{ serialize($parameters) }}">
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
