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
{{--            <div class="row">--}}
{{--                <div class="col-md-3 col-6">--}}
{{--                    <div class="small-box bg-gradient-primary">--}}
{{--                        <div class="inner">--}}
{{--                            <h4>{{ $agreed_invoices_count }}</h4>--}}
{{--                            <p>{{ __('home.agreed_invoices_count') }}</p>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="col-md-3 col-6">--}}
{{--                    <div class="small-box bg-gradient-navy">--}}
{{--                        <div class="inner">--}}
{{--                            <h4>{{ number_format($debit, 0, '.', ' ') }}р.</h4>--}}
{{--                            <p>{{ __('home.income_invoices_amount') }}</p>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="col-md-3 col-6">--}}
{{--                    <div class="small-box bg-gradient-indigo">--}}
{{--                        <div class="inner">--}}
{{--                            <h4>{{ number_format($credit, 0, '.', ' ') }}р.</h4>--}}
{{--                            <p>{{ __('home.outcome_invoices_amount') }}</p>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="col-md-3 col-6">--}}
{{--                    <div class="small-box bg-gradient-success">--}}
{{--                        <div class="inner">--}}
{{--                            <h4>{{ number_format($this_month_total_profit, 0, '.', ' ') }}р.</h4>--}}
{{--                            <p>{{ __('home.this_month_total_profit') }}</p>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">{{ __('home.agreed_invoices') }}</h3>
                </div>
                <div class="card-body">
                    @if($agreed_invoices_count != 0)
                        <div class="invoices_homepage" data-type="agreed">
                            @include('invoice.table.invoices_table_ajax_filter', ['filter' => 'agreed'])
                        </div>
                    @else
                        {{ __('home.no_agreed_invoices') }}
                    @endif
                </div>
                @if($agreed_invoices_count != 0)
                    <div class="card-footer">
                        <form action="{{ route('invoices_export_with_filter_to_excel') }}" method="GET">
                            @csrf
                            @php
                                $parameters = [
                                    'filename' => 'согласованные_счета',
                                    'sorting_type' => 'Согласованные на оплату счета',
                                ];
                            @endphp
                            <input type="hidden" name="filter" value="agreed">
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
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">{{ __('home.income_invoices') }}</h3>
                </div>
                <div class="card-body">
                    @if($in_invoices_count != 0)
                        <div class="invoices_homepage" data-type="in">
                            @include('invoice.table.invoices_table_ajax_filter', ['filter' => 'in'])
                        </div>
                    @else
                        {{ __('home.no_income_invoices') }}
                    @endif
                </div>
                @if($in_invoices_count != 0)
                    <div class="card-footer">
                        <form action="{{ route('invoices_export_with_filter_to_excel') }}" method="GET">
                            @csrf
                            @php
                                $parameters = [
                                    'filename' => 'входящие_счета',
                                    'sorting_type' => 'Входящие счета',
                                ];
                            @endphp
                            <input type="hidden" name="filter" value="in">
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

            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">{{ __('home.outcome_invoices') }}</h3>
                </div>
                <div class="card-body">
                    @if($out_invoices_count != 0)
                        <div class="invoices_homepage" data-type="out">
                            @include('invoice.table.invoices_table_ajax_filter', ['filter' => 'out'])
                        </div>
                    @else
                        {{ __('home.no_outcome_invoices') }}
                    @endif
                </div>
                @if($out_invoices_count != 0)
                    <div class="card-footer">
                        <form action="{{ route('invoices_export_with_filter_to_excel') }}" method="GET">
                            @csrf
                            @php
                                $parameters = [
                                    'filename' => 'исходящие_счета',
                                    'sorting_type' => 'Исходящие счета',
                                ];
                            @endphp
                            <input type="hidden" name="filter" value="out">
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

            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">{{ __('general.waiting_for_invoice') }}</h3>
                </div>
                <div class="card-body">
                    @if($waiting_invoices_count != 0)
                        <div class="invoices_homepage" data-type="waiting_invoices">
                            @include('invoice.table.invoices_table_ajax_filter', ['filter' => 'waiting_invoices'])
                        </div>
                    @else
                        {{ __('general.no_waiting_for_invoice') }}
                    @endif
                </div>
                @if($waiting_invoices_count != 0)
                    <div class="card-footer">
                        <form action="{{ route('invoices_export_with_filter_to_excel') }}" method="GET">
                            @csrf
                            @php
                                $parameters = [
                                    'filename' => 'ожидается_инвойс',
                                    'sorting_type' => 'Ожидается создание инвойса',
                                ];
                            @endphp
                            <input type="hidden" name="filter" value="waiting_invoices">
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
        @include('project.modals.confirm_invoice')
    </section>
@endsection
