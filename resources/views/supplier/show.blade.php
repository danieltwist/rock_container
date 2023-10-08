@extends('layouts.project')
@section('title', __('supplier.supplier_statistic'))
@section('content')
    @if(!is_null($supplier))
        <div class="content-header">
            <div class="container-fluid">
                @include('layouts.info_block')
                <div class="row mb-2">
                    <div class="col-sm-12">
                        <h1 class="m-0">{{ __('general.supplier') }} {{ $supplier->name }} {{ $supplier->short != '' ? '('.$supplier->short.')' : '' }}
                            <a href="{{ route('supplier.edit', $supplier->id) }}" class="btn btn-default">
                                {{ __('supplier.edit_supplier') }}
                            </a>
                            @if(!is_null($supplier->linked))
                                <a href="{{ route('client.show', $supplier->linked) }}" class="btn btn-default">
                                    {{ __('supplier.switch_to_client') }}
                                </a>
                            @endif
                        </h1>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
            <div class="container-fluid">
            @include('layouts.info_block')
                <input type="hidden" id="counterparty_id" value="{{ $supplier->id }}">
                <div class="row">
                    <div class="col-md-3 col-6">
                        <div class="small-box bg-gradient-primary">
                            <div class="inner">
                                <h4>{{ $projects->count() }}</h4>
                                <p>{{ __('supplier.supplier_projects_count') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="small-box bg-gradient-navy">
                            <div class="inner">
                                <h4>{{ $invoices->count() }}</h4>
                                <p>{{ __('supplier.supplier_invoices_count') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="small-box bg-gradient-indigo">
                            <div class="inner">
                                <h4>{{ $not_paid_invoices_count }}</h4>
                                <p>{{ __('supplier.supplier_not_paid_invoices_count') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="small-box bg-gradient-success">
                            <div class="inner">
                                <h4>{{ number_format($invoices_sum, 0, '.', ' ') }}р.</h4>
                                <p>{{ __('supplier.total_paid_to_supplier') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card collapsed collapsed-card">
                            <div class="card-header cursor-pointer {{ is_null($supplier->deleted_at) ?: 'bg-danger' }}" data-card-widget="collapse">
                                <h3 class="card-title">{{ __('general.requisites') }}</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                                </div>
                            </div>
                            @csrf
                            <div class="card-body">
                                @nl2br($supplier->requisites)
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card collapsed collapsed-card">
                            <div class="card-header cursor-pointer {{ is_null($supplier->deleted_at) ?: 'bg-danger' }}" data-card-widget="collapse">
                                <h3 class="card-title">{{ __('general.contracts') }}</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                                </div>
                            </div>
                            @csrf
                            <div class="card-body">
                                @if ($supplier->contracts->isNotEmpty())
                                    <ul class="nav flex-column">
                                        @foreach($supplier->contracts as $contract)
                                            <div class="mt-2">
                                                <li class="nav-item">
                                                    <a class="nav-link" href="{{ Storage::url($contract->file) }}"
                                                       download>
                                                        <i class="far fa-file-word"></i>&nbsp;{{ $contract->name }} от {{ $contract->date_start }},
                                                        срок действия {{ $contract->date_period }}
                                                    </a>
                                                </li>
                                            </div>
                                        @endforeach
                                    </ul>
                                @else
                                    {{ __('supplier.no_contracts_for_this_supplier') }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card collapsed collapsed-card">
                    <div class="card-header cursor-pointer" data-card-widget="collapse">
                        <h3 class="card-title">Заявки с поставщиком</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool form-inline" data-card-widget="collapse"
                                    title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped applications_table" id="applications_table" data-filter_type="supplier" data-supplier_id="{{ $supplier->id }}">
                            <thead>
                            <tr>
                                <th style="width: 1%">
                                    #
                                </th>
                                <th style="width: 9%">
                                    Название
                                </th>
                                <th style="width: 20%">
                                    Контрагент
                                </th>
                                <th style="width: 15%">
                                    Маршрут
                                </th>
                                <th style="width: 15%">
                                    Условия
                                </th>
                                <th style="width: 15%">
                                    Контейнеры
                                </th>
                                <th style="width: 5%">
                                    Статус
                                </th>
                                <th style="width: 24%">
                                    {{ __('general.actions') }}
                                </th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="card collapsed collapsed-card">
                    <div class="card-header cursor-pointer" data-card-widget="collapse">
                        <h3 class="card-title">{{ __('supplier.supplier_projects') }}</h3>
                        <div class="card-tools">
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool form-inline" data-card-widget="collapse"
                                        title="Collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @include('project.layouts.projects_table_full_render')
                    </div>
                    @if(in_array($role, ['director', 'accountant', 'super-admin']))
                        <div class="card-footer">
                            <form action="{{ route('projects_counterparty_export') }}" method="GET">
                                @csrf
                                @php
                                    $parameters = [
                                        'filename' => $supplier->name.'_все_проекты',
                                        'sorting_type' => 'Все проекты с участием поставщика '.$supplier->name,
                                    ];
                                @endphp
                                <input type="hidden" name="type" value="supplier">
                                <input type="hidden" name="supplier_id" value="{{ $supplier->id }}">
                                <input type="hidden" name="parameters" value="{{ serialize($parameters) }}">
                                <button type="submit" class="btn btn-success download_file_directly"
                                        data-action='{"download_file":{"need_download": "true"}}'>
                                    <i class="fas fa-file-excel"></i>
                                    {{ __('general.export_projects_to_excel') }}
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
                <div class="card collapsed collapsed-card">
                    <div class="card-header cursor-pointer" data-card-widget="collapse">
                        <h3 class="card-title">{{ __('supplier.invoices_list') }}</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool form-inline" data-card-widget="collapse"
                                    title="Collapse">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @include('invoice.table.filters_object', ['filter' => 'status'])
                        <div class="invoices_counterparty filter_table_div mt-4"
                             @if((config('app.prefix_view') == 'rl_' || config('app.prefix_view') == 'rc_') && !in_array($role,['super-admin','director','accountant']) && ($supplier->id == '311' || $supplier->id == '377'))
                                 data-personal="true"
                             @elseif(config('app.prefix_view') == 'blc_' && !in_array($role,['super-admin','director','accountant']) && ($supplier->id == '13' || $supplier->id == '12'))
                                 data-personal="true"
                             @endif
                             data-type="supplier"
                             data-object_id="{{ $supplier->id }}">
                            @include('invoice.table.invoices_table_ajax_filter', ['filter' => 'supplier'])
                        </div>
                    </div>
                    @if(in_array($role, ['director', 'accountant', 'super-admin']))
                        <div class="card-footer">
                            <form action="{{ route('invoices_export_with_filter_to_excel') }}" method="GET">
                                @csrf
                                @php
                                    $parameters = [
                                        'filename' => $supplier->name.'_все_счета',
                                        'sorting_type' => 'Все счета поставщика '.$supplier->name
                                    ];
                                @endphp
                                <input type="hidden" name="supplier" value="{{ $supplier->id }}">
                                @if(!in_array($role,['super-admin','director','accountant']) && $supplier->id == '311')
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
            @include('project.modals.confirm_invoice')
            @include('audit.component_history_modal')
        </section>
    @endif
@endsection
