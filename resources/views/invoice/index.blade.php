@extends('layouts.project')
@section('title', __('invoice.invoices'))
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    @if (isset($title))
                        <h1 class="m-0">{{ $title }}</h1>
                    @elseif (isset($_GET['client']))
                        <h1 class="m-0">{{ __('invoice.invoices_to_client') }}</h1>
                    @elseif (isset($_GET['supplier']))
                        <h1 class="m-0">{{ __('invoice.invoices_from_supplier') }}</h1>
                    @elseif (isset($_GET['for_approval']))
                        <h1 class="m-0">{{ __('invoice.waiting_approval') }}</h1>
                    @elseif (isset($_GET['agreed']))
                        <h1 class="m-0">{{ __('invoice.agreed_invoices') }}</h1>
                    @elseif (isset($_GET['paid']))
                        <h1 class="m-0">{{ __('invoice.paid_invoices') }}</h1>
                    @elseif (isset($_GET['my']))
                        <h1 class="m-0">{{ __('invoice.my_invoices') }}</h1>
                    @elseif (isset($_GET['on_approval']))
                        <h1 class="m-0">{{ __('invoice.on_approval') }}</h1>
                    @elseif (isset($_GET['in']))
                        <h1 class="m-0">{{ __('invoice.income_invoices') }}</h1>
                    @elseif (isset($_GET['out']))
                        <h1 class="m-0">{{ __('invoice.outcome_invoices') }}</h1>
                    @elseif (isset($_GET['trash']))
                        <h1 class="m-0">Удаленные счета</h1>
                    @else
                        <h1 class="m-0">{{ __('invoice.all_invoices') }}</h1>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @include('layouts.info_block')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('invoice.invoices_list') }}</h3>
                    <div id="loading_spinner"></div>
                </div>
                <div class="card-body">
                    <div id="invoices_agree_amount"></div>
                    @if (!isset($_GET['client']) && !isset($_GET['supplier']))
                        <div class="row">
                            <div class="col-md-8">
                                <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                    <i class="fa fa-calendar"></i>&nbsp;
                                    <span>Все</span> <i class="fa fa-caret-down"></i>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-default" onclick="sortInvoicesByDate();" id="invoice_sort_by_date">
                                    <i class="fa fa-search"></i> {{ __('general.upload') }}
                                </button>
                            </div>
                        </div>
                    @endif
                    @if(isset($_GET['for_approval']) || isset($_GET['agreed']) || isset($_GET['on_approval']) || isset($_GET['partially_paid']) || isset($_GET['paid']))
                        @include('invoice.table.filters_agree_invoice')
                    @else
                        @include('invoice.table.filters')
                    @endif
                    <div class='mt-4' id="search_results">
                        @include('invoice.table.invoice_ajax_table')
                    </div>
                </div>
                <div class="card-footer">
                    <form action="{{ route('invoices_export_with_filter_to_excel') }}" id="get_excel_invoices" method="GET">
                        @csrf
                        @if (isset($_GET['for_approval']))
                            @php
                                $parameters = [
                                    'filename' => 'счета_на_согласовании',
                                    'sorting_type' => 'Ожидается согласование'
                                ];
                                $filter = 'for_approval';
                            @endphp
                        @elseif (isset($_GET['agreed']))
                            @php
                                $parameters = [
                                    'filename' => 'согласованные_счета',
                                    'sorting_type' => 'Согласованы на оплату'
                                ];
                                $filter = 'agreed';
                            @endphp
                        @elseif (isset($_GET['paid']))
                            @php
                                $parameters = [
                                    'filename' => 'оплаченные_счета',
                                    'sorting_type' => 'Оплаченные счета'
                                ];
                                $filter = 'paid';
                            @endphp
                        @elseif (isset($_GET['my']))
                            @php
                                $parameters = [
                                    'filename' => 'мои_счета',
                                    'sorting_type' => 'Счета, добавленные пользователем '.auth()->user()->name
                                ];
                                $filter = 'my';
                            @endphp
                        @elseif (isset($_GET['on_approval']))
                            @php
                                $parameters = [
                                    'filename' => 'в_процессе_согласования',
                                    'sorting_type' => 'В процессе согласования'
                                ];
                                $filter = 'on_approval';
                            @endphp
                        @elseif (isset($_GET['in']))
                            @php
                                $parameters = [
                                    'filename' => 'входящие_счета',
                                    'sorting_type' => 'Входящие счета'
                                ];
                                $filter = 'in';
                            @endphp
                        @elseif (isset($_GET['out']))
                            @php
                                $parameters = [
                                    'filename' => 'исходящие_счета',
                                    'sorting_type' => 'Исходящие счета'
                                ];
                                $filter = 'out';
                            @endphp
                        @else
                            @php
                                $parameters = [
                                    'filename' => 'все_счета',
                                    'sorting_type' => 'Все счета',
                                ];
                            @endphp
                        @endif

                        @if(isset($filter))
                            <input type="hidden" name="filter" value="{{ $filter }}">
                        @endif
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
    </section>
    @include('project.modals.confirm_invoice')
    @include('audit.component_history_modal')
@endsection
