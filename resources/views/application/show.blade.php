@extends('layouts.project')
@section('title', 'Просмотр заявки')
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">Просмотр заявки {{ $application->name }}
                        <a href="{{ route('application.index') }}" class="btn btn-default">Все заявки</a>
                    </h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @include('layouts.info_block')
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header {{ is_null($application->deleted_at) ?: 'bg-danger' }}">
                            <h3 class="card-title">Информация по заявке</h3>
                            <div class="card-tools">
                                @can ('edit projects paid status')
                                    <button type="button" data-toggle="modal" data-target="#view_component_history"
                                            class="btn btn-default btn-sm"
                                            data-component="application"
                                            data-id="{{ $application->id }}">
                                        <i class="fas fa-history"></i>
                                        История
                                    </button>
                                @endcan
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-box bg-light">
                                        <div class="info-box-content">
                                            <span class="info-box-text text-center text-muted">Расходы расчетные / фактические</span>
                                            <span class="info-box-number text-center text-muted mb-0">
                                                {{ number_format($planned_in, 0, '.', ' ') }}р. / {{ number_format($fact_in, 0, '.', ' ') }}р.
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-box bg-light">
                                        <div class="info-box-content">
                                            <span class="info-box-text text-center text-muted">Доходы расчетные / фактические</span>
                                            <span class="info-box-number text-center text-muted mb-0">
                                                {{ number_format($planned_out, 0, '.', ' ') }}р. / {{ number_format($fact_out, 0, '.', ' ') }}р.
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="text-muted mt-3">
                                        <p class="text-sm">Тип заявки
                                            <b class="d-block">
                                                @switch($application->type)
                                                    @case('Поставщик')
                                                        Взять в аренду
                                                        @break
                                                    @case('Клиент')
                                                        Выдать в аренду
                                                        @break
                                                    @default
                                                        {{ $application->type }}
                                                @endswitch
                                                    {{ !is_null($application->surcharge) ? ' / Доплатная' : '' }}
                                            </b>
                                        </p>
                                    </div>
                                    <div class="text-muted mt-3">
                                        <p class="text-sm">Дата
                                            <b class="d-block">
                                                {{ $application->created_at->format('d.m.Y') }}
                                            </b>
                                        </p>
                                    </div>
                                    @if(!is_null($application->user_name))
                                        <div class="text-muted mt-3">
                                            <p class="text-sm">Добавил
                                                <b class="d-block">
                                                    {{ $application->user_name }}
                                                </b>
                                            </p>
                                        </div>
                                    @endif

                                    <div class="text-muted mt-3">
                                        <p class="text-sm">Контрагент
                                            <b class="d-block">
                                                @if($application->counterparty_type == 'Клиент')
                                                    {{ $application->counterparty_type }}: {{ $application->client_name }}
                                                @endif
                                                @if($application->counterparty_type == 'Поставщик')
                                                        {{ $application->counterparty_type }}: {{ $application->supplier_name }}
                                                @endif
                                            </b>
                                        </p>
                                    </div>
                                    <div class="text-muted mt-3">
                                        <p class="text-sm">Договор
                                            <b class="d-block">
                                                {{ $application->contract_info['name'] }} от {{ is_null($application->contract_info['date']) ?: \Carbon\Carbon::parse($application->contract_info['date'])->format('d.m.Y') }}
                                            </b>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="text-muted mt-3">
                                        <p class="text-sm">Количество контейнеров
                                            <b class="d-block">{{ $application->containers_amount }}
                                                <a class="cursor-pointer text-dark" data-toggle="collapse" data-target="#containers_collapse" aria-expanded="false">
                                                    Показать
                                                </a>
                                            </b>
                                        </p>
                                            <div class="collapse mt-2" id="containers_collapse">
                                                <p class="text-sm">
                                                    @if(!is_null($application->containers))
                                                        {{ implode(', ', $application->containers) }}
                                                    @endif
                                                </p>
                                            </div>
                                    </div>
                                    <div class="text-muted mt-3">
                                        <p class="text-sm">Стоимость
                                            <b class="d-block">{{ $application->price_amount }}{{ $application->price_currency }}
                                                @if($application->price_currency != 'RUB')
                                                    / {{ round($application->price_amount*$application->currency_rate) }}р. ({{ $application->currency_rate }})
                                                @endif
                                            </b>
                                        </p>
                                    </div>
                                    <div class="text-muted mt-3">
                                        <p class="text-sm">Общая стоимость услуг
                                            <b class="d-block">{{ $application->price_amount*$application->containers_amount }}{{ $application->price_currency }}
                                                @if($application->price_currency != 'RUB')
                                                    / {{ round($application->price_amount*$application->containers_amount*$application->currency_rate) }}р. ({{ $application->currency_rate }})
                                                @endif
                                            </b>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    @if(!is_null($application->grace_period))
                                        <div class="text-muted mt-3">
                                            <p class="text-sm">Льготный период
                                                <b class="d-block">{{ $application->grace_period }} дней</b>
                                            </p>
                                        </div>
                                    @endif
                                    @if(!is_null($application->snp_after_range))
                                        <div class="text-muted mt-3">
                                            <p class="text-sm">СНП
                                                @if(!is_null($application->snp_range))
                                                    <b class="d-block">
                                                        @foreach($application->snp_range as $range)
                                                            {{ $range['range'] }} день - {{ $range['price'] }}{{ $application->snp_currency }}<br>
                                                        @endforeach
                                                        Далее - {{ $application->snp_after_range }}{{ $application->snp_currency }}
                                                    </b>
                                                @else
                                                    <b class="d-block">{{ $application->snp_after_range }}{{ $application->snp_currency }}</b>
                                                @endif
                                            </p>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-3">
                                    <div class="text-muted mt-3">
                                        @if(!is_null($application->send_from_country))
                                            <p class="text-sm">Откуда
                                                <b class="d-block">
                                                    {{ $application->send_from_country }}
                                                    @if(!is_null($application->send_from_city))
                                                        , {{ implode('/', $application->send_from_city) }}
                                                    @endif
                                                </b>
                                            </p>
                                        @endif
                                        @if(!is_null($application->send_to_country))
                                            <p class="text-sm">Куда
                                                <b class="d-block">
                                                    {{ $application->send_to_country }}
                                                    @if(!is_null($application->send_to_city))
                                                        , {{ implode('/', $application->send_to_city) }}
                                                    @endif
                                                </b>
                                            </p>
                                        @endif
                                        @if(!is_null($application->place_of_delivery_country))
                                            <p class="text-sm">Депо сдачи
                                                <b class="d-block">
                                                    {{ $application->place_of_delivery_country }}
                                                    @if(!is_null($application->place_of_delivery_city))
                                                        , {{ implode('/', $application->place_of_delivery_city) }}
                                                    @endif
                                                </b>
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 text-muted">
                                    @if(!is_null($application->additional_info))
                                        <p class="text-sm">Дополнительная информация
                                            <b class="d-block">{{ $application->additional_info }}</b>
                                        </p>
                                    @endif
                                </div>
                                <div class="col-12 mt-2">
                                    <a class="btn bg-indigo mt-2" href="
                                        @if(in_array($application->type, ['Покупка', 'Продажа']))
                                            {{ route('buy_sell_edit', $application->id) }}
                                        @else
                                            {{ route('application.edit', $application->id) }}
                                        @endif
                                        ">
                                        <i class="fas fa-edit"></i>
                                        Редактировать заявку
                                    </a>
                                    <a class="btn bg-primary mt-2"
                                       id="preview_invoices"
                                       data-application_id="{{ $application->id }}"
                                       data-toggle="modal"
                                       data-target="#preview_application_invoices">
                                        <i class="fas fa-calculator"></i>
                                        Сгенерировать расходы / доходы
                                    </a>
                                    <div class="btn-group mt-2" role="group">
                                        <button id="download_application_word" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Скачать заявку
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="download_application_word">
                                            <a class="dropdown-item download_application" href="#"
                                               data-application_id="{{ $application->id }}"
                                               data-application_template="1">
                                                Мы даем ктк рус-кит
                                            </a>
                                            <a class="dropdown-item download_application" href="#"
                                               data-application_id="{{ $application->id }}"
                                               data-application_template="2">
                                                ТЭО нам платят
                                            </a>
                                            <a class="dropdown-item download_application" href="#"
                                               data-application_id="{{ $application->id }}"
                                               data-application_template="3">
                                                Заявка наша к договору предоставления ктк платят нам
                                            </a>
                                        </div>
                                    </div>
                                    @include('application.ajax.finish_application')
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            @if(is_null($application->containers))
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Список контейнеров</h3>
                                    </div>
                                    <div class="card-body">
                                        Контейнеры по данной заявке еще не были добавлены
                                    </div>
                                </div>
                            @endif
                            @if(!empty($load_from_containers))
                                @include('container.table_extended.table_card_layout', ['table_filter_type' => 'application', 'application_id' => $application->id, 'load_from_containers' => serialize($load_from_containers)])
                            @endif
                            @if(!empty($load_from_archive))
                                @include('container.table_extended.containers_history_table', ['table_filter_type' => 'application', 'application_id' => $application->id, 'load_from_archive' => serialize($load_from_archive)])
                            @endif
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Список доходов и расходов по заявке</h3>
                                    <div class="card-tools">
                                        <button type="button" data-toggle="modal" data-target="#make_invoice"
                                                class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus"></i>
                                            {{ __('project.add_invoice') }}
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-default btn-sm invoices_object_filters show_invoices_table"
                                                data-filter="Доход"
                                                data-filter_type="direction">
                                            {{ __('project.show_income') }}
                                        </button>
                                        <button type="button" class="btn btn-default btn-sm invoices_object_filters show_invoices_table"
                                                data-filter="Расход"
                                                data-filter_type="direction">
                                            {{ __('project.show_outcome') }}
                                        </button>
                                        <button type="button" class="btn btn-default btn-sm invoices_object_filters show_invoices_table"
                                                data-filter="">
                                            {{ __('project.show_all') }}
                                        </button>
                                    </div>
                                    <div class="invoices_application mt-4 filter_table_div"
                                         id="standard_block_invoices"
                                         data-type="application"
                                         data-object_id="{{ $application->id }}">
                                        @include('invoice.table.invoices_table_ajax_filter', ['filter' => 'application'])
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <form action="{{ route('invoices_export_with_filter_to_excel') }}" method="GET">
                                        @csrf
                                        @php
                                            $parameters = [
                                                'filename' => 'заявка_'.$application->id.'_все_счета',
                                                'sorting_type' => 'Заявка '.$application->name,
                                                'export_type' => 'Все счета по заявке'
                                            ];
                                        @endphp
                                        <input type="hidden" name="application" value="{{ $application->id }}">
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
            </div>
        </div>
        @include('project.modals.confirm_invoice')
        @include('project.modals.make_invoice_model')
        @include('application.modals.add_invoices')
        @include('audit.component_history_modal')
        @include('application.modals.not_allowed_finish_reason')
    </section>
@endsection
