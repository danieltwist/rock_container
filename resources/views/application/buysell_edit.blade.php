@extends('layouts.project')
@section('title', 'Добавить заявку')
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Редактировать заявку</h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @include('layouts.info_block')
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-default">
                        <div class="card-header">
                            <h3 class="card-title">Изменить данные заявки</h3>
                        </div>
                        <form action="{{ route('application.update', $application->id) }}" method="POST" enctype="multipart/form-data">
                            @method('PUT')
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="name">Название заявки</label>
                                            <input type="text" class="form-control" name="name"
                                                   placeholder="Название заявки"
                                                   value="{{ $application->name }}"
                                                   required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="created_at">Дата заявки</label>
                                            <input type="text" class="form-control date_input" name="created_at"
                                                   placeholder="Дата заявки"
                                                   value="{{ $application->created_at->format('d.m.Y') }}"
                                                   required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="application_type">Тип заявки</label>
                                            <select class="form-control select2" name="type" id="application_type" required
                                                    data-placeholder="Тип заявки" style="width: 100%;" >
                                                <option></option>
                                                <option value="Покупка" {{ $application->type != 'Покупка' ?: 'selected' }}>Покупка</option>
                                                <option value="Продажа" {{ $application->type != 'Продажа' ?: 'selected' }}>Продажа</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="counterparty_type">Тип контрагента</label>
                                            <input type="text" class="form-control" name="counterparty_type" id="application_direction" required readonly="true"
                                                   placeholder="Выберите тип заявки"
                                                   value="{{ $application->counterparty_type }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group {{ $application->counterparty_type != 'Поставщик' ?: 'd-none' }}"
                                             id="client_group">
                                            <label for="client_id">{{ __('general.client') }}</label>
                                            <select class="form-control select2 application_load_contract" name="client_id"
                                                    id="application_client_select"
                                                    data-counterparty_type="client"
                                                    {{ $application->counterparty_type != 'Клиент' ?: 'required' }}
                                                    data-placeholder="{{ __('project.select_client') }}" style="width: 100%;">
                                                <option></option>
                                                @foreach($clients as $client)
                                                    <option value="{{ $client->id }}"
                                                        {{ $application->client_id != $client->id ?: 'selected' }}>{{ $client->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group {{ $application->counterparty_type != 'Клиент' ?: 'd-none' }}" id="supplier_group">
                                            <label for="supplier_id">{{ __('general.supplier') }}</label>
                                            <select class="form-control select2 application_load_contract" name="supplier_id"
                                                    id="application_supplier_select"
                                                    data-counterparty_type="supplier"
                                                    {{ $application->counterparty_type != 'Поставщик' ?: 'required' }}
                                                    data-placeholder="{{ __('project.select_supplier') }}" style="width: 100%;">
                                                <option></option>
                                                @foreach($suppliers as $supplier)
                                                    <option value="{{ $supplier->id }}"
                                                        {{ $application->supplier_id != $supplier->id ?: 'selected' }}>{{ $supplier->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div id="application_counterparty_contracts">
                                            <div class="form-group">
                                                <label for="contract_id">Договор</label>
                                                <select class="form-control select2" name="contract_id" required
                                                        data-placeholder="Выберите договор" style="width: 100%;">
                                                    <option></option>
                                                    @foreach($contracts as $contract)
                                                        <option value="{{ $contract->id }}"
                                                            {{ $contract->id != $application->contract_id ?: 'selected' }}>
                                                            {{$contract->name}} от {{ $contract->date_start->format('d.m.Y') }}, действует до {{ $contract->date_period->format('d.m.Y') }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="containers_amount">Количество ктк</label>
                                            <input type="text" class="form-control digits_only" name="containers_amount"
                                                   placeholder="Количество ктк"
                                                   value="{{ $application->containers_amount }}"
                                                   required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="price_amount">Стоимость 1 ктк</label>
                                            <input type="text" class="form-control rate_input" name="price_amount"
                                                   placeholder="Стоимость 1 ктк"
                                                   value="{{ $application->price_amount }}"
                                                   required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group" id="supplier_group">
                                            <label for="price_currency">Валюта</label>
                                            <select class="form-control select2"
                                                    name="price_currency"
                                                    id="price_currency"
                                                    required
                                                    data-placeholder="Выберите валюту" style="width: 100%;" >
                                                <option></option>
                                                @foreach(['RUB', 'USD', 'CNY'] as $currency)
                                                    <option value="{{ $currency }}" {{ $application->price_currency != $currency ?: 'selected' }}>{{ $currency }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Вставьте список контейнеров</label>
                                            <textarea class="form-control" rows="10" id="application_containers"
                                                      placeholder="Список контейнеров"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div id="dynamic_containers_div">
                                            @if(!is_null($application->containers))
                                                <div class="form-group">
                                                    <label for="containers_used">Список контейнеров (всего: {{ count($application->containers) }})</label>
                                                    <select class="form-control select2" name="containers[]" id="containers_used" multiple
                                                            data-placeholder="Выберите контейнеры" style="width: 100%;">
                                                        <option></option>
                                                        @foreach($application->containers as $container)
                                                            <option value="{{ $container }}" selected>{{ $container }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif
                                        </div>
                                        <div id="removed_containers_div">
                                            @if(!is_null($application->containers_removed))
                                                <div class="form-group">
                                                    <label for="containers_removed">Контейнеры на удаление, удалены пользователем {{ $application->removed_by }}</label>
                                                    <input class="form-control" type="text"
                                                           id="containers_removed"
                                                           placeholder="Контейнеры на удаление"
                                                           value="{{ implode(', ', $application->containers_removed) }}" disabled>
                                                    </select>
                                                </div>
                                                @if(in_array($role, ['director', 'super-admin']))
                                                    <a class="btn btn-danger" id="confirm_containers_remove"
                                                       data-application_id="{{ $application->id }}">
                                                        Подтвердить удаление
                                                    </a>
                                                @endif
                                                <a class="btn btn-success" id="cancel_containers_remove"
                                                   data-application_id="{{ $application->id }}">
                                                    Восстановить
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Тип контейнеров</label>
                                    <select class="form-control select2" name="containers_type"
                                            data-placeholder="Выберите тип контейнеров" style="width: 100%;" >
                                        <option></option>
                                        @foreach(['40HC', '20DC', '40OT', '20OT', '40DC', '40RF'] as $container_type)
                                            <option value="{{ $container_type }}"
                                                {{ $container_type != $application->containers_type ?: 'selected' }}>
                                                {{ $container_type }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>{{ __('general.additional_info') }}</label>
                                    <textarea class="form-control to_uppercase" rows="3" name="additional_info" placeholder="{{ __('general.additional_info') }}">{{ $application->additional_info }}</textarea>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
