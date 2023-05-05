@extends('layouts.project')
@section('title', 'Редактировать заявку')
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Редактировать заявку {{ $application->name }}</h1>
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
                                            <label for="counterparty_type">Тип контрагента</label>
                                            <select class="form-control select2" name="counterparty_type" id="application_direction" required
                                                    data-placeholder="Выберите тип контрагента" style="width: 100%;">
                                                <option></option>
                                                @foreach(['Поставщик', 'Клиент'] as $counterparty_type)
                                                    <option value="{{ $counterparty_type }}"
                                                        {{ $counterparty_type != $application->counterparty_type ?: 'selected' }}>
                                                        {{ $counterparty_type }}
                                                    </option>
                                                @endforeach
                                            </select>
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
                                                @foreach(['Поставщик', 'Клиент', 'Подсыл'] as $application_type)
                                                    <option value="{{ $application_type }}"
                                                        {{ $application_type != $application->type ?: 'selected' }}>
                                                        {{ $application_type }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="containers_amount">Количество ктк</label>
                                            <input type="text" class="form-control digits_only"
                                                   name="containers_amount"
                                                   value="{{ $application->containers_amount }}"
                                                   placeholder="Количество ктк" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="price_amount">Стоимость услуг</label>
                                            <input type="text" class="form-control digits_only"
                                                   name="price_amount"
                                                   value="{{ $application->price_amount }}"
                                                   placeholder="Стоимость услуг" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="price_currency">Валюта</label>
                                            <select class="form-control select2"
                                                    name="price_currency"
                                                    id="price_currency"
                                                    required
                                                    data-placeholder="Выберите валюту" style="width: 100%;" >
                                                <option></option>
                                                @foreach(['RUB','USD','CNY'] as $price_currency)
                                                    <option value="{{ $price_currency }}"
                                                        {{ $price_currency != $application->price_currency ?: 'selected' }}>
                                                        {{ $price_currency }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <strong>Маршрут предоставления - Откуда</strong>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="send_from_country">Страна</label>
                                            <select class="form-control select2"
                                                    name="send_from_country"
                                                    id="send_from_country"
                                                    data-type="send_from"
                                                    required
                                                    data-placeholder="Выберите страну" style="width: 100%;" >
                                                <option></option>
                                                @foreach($countries as $country)
                                                    <option value="{{ $country->name }}"
                                                        {{ $country->name != $application->send_from_country ?: 'selected' }}>
                                                        {{ $country->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-8" id="send_from_country_div">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="send_from_city">Город / Станция</label>
                                                    <select class="form-control select2"
                                                            name="send_from_city[]"
                                                            id="send_from_city"
                                                            required
                                                            multiple
                                                            data-placeholder="Выберите города" style="width: 100%;" >
                                                        <option></option>
                                                        @if(!empty($cities_from[0]))
                                                            @foreach($cities_from[0] as $city)
                                                                <option value="{{ $city }}"
                                                                    {{ !in_array($city, $application->send_from_city) ?: 'selected' }}>
                                                                    {{ $city }}
                                                                </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Добавить в список</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control to_uppercase" id="send_from_city_add_city" placeholder="Добавить в список" aria-label="Добавить в список">
                                                        <div class="input-group-append">
                                                            <button class="btn btn-outline-secondary add_city_to_country" data-country_type="send_from" type="button">Добавить</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div id="send_to_div" {{ $application->type == 'Клиент' ? 'class=d-none' : '' }}>
                                    <strong>Маршрут предоставления - Куда</strong>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="send_to_country">Страна</label>
                                                <select class="form-control select2"
                                                        name="send_to_country"
                                                        id="send_to_country"
                                                        data-type="send_to"
                                                        data-placeholder="Выберите страну" style="width: 100%;" >
                                                    <option></option>
                                                    @foreach($countries as $country)
                                                        <option value="{{ $country->name }}"
                                                            {{ $country->name != $application->send_to_country ?: 'selected' }}>
                                                            {{ $country->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-8" id="send_to_country_div">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="send_to_city">Город / Станция</label>
                                                        <select class="form-control select2"
                                                                name="send_to_city[]"
                                                                id="send_to_city"
                                                                multiple
                                                                data-placeholder="Выберите города" style="width: 100%;" >
                                                            <option></option>
                                                            @if($cities_to->isNotEmpty())
                                                                @if(!is_null($cities_to[0]))
                                                                    @foreach($cities_to[0] as $city)
                                                                        <option value="{{ $city }}"
                                                                            {{ !in_array($city, $application->send_to_city) ?: 'selected' }}>
                                                                            {{ $city }}
                                                                        </option>
                                                                    @endforeach
                                                                @endif
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Добавить в список</label>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control to_uppercase" id="send_to_city_add_city" placeholder="Добавить в список" aria-label="Добавить в список">
                                                            <div class="input-group-append">
                                                                <button class="btn btn-outline-secondary add_city_to_country" data-country_type="send_to" type="button">Добавить</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="place_of_delivery_div" {{ $application->type == 'Подсыл' ? 'class=d-none' : '' }} >
                                    <strong>Маршрут предоставления - Депо сдачи</strong>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="place_of_delivery_country">Страна</label>
                                                <select class="form-control select2"
                                                        name="place_of_delivery_country"
                                                        id="place_of_delivery_country"
                                                        data-type="place_of_delivery"
                                                        data-placeholder="Выберите страну" style="width: 100%;" >
                                                    <option></option>
                                                    @foreach($countries as $country)
                                                        <option value="{{ $country->name }}"
                                                            {{ $country->name != $application->place_of_delivery_country ?: 'selected' }}>
                                                            {{ $country->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-8" id="place_of_delivery_country_div">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="place_of_delivery_city">Город / Станция</label>
                                                        <select class="form-control select2"
                                                                name="place_of_delivery_city[]"
                                                                id="place_of_delivery_city"
                                                                multiple
                                                                data-placeholder="Выберите города" style="width: 100%;" >
                                                            <option></option>
                                                            @if($cities_place_of_delivery->isNotEmpty())
                                                                @if(!is_null($cities_place_of_delivery[0]))
                                                                    @foreach($cities_place_of_delivery[0] as $city)
                                                                        <option value="{{ $city }}"
                                                                            {{ !in_array($city, $application->place_of_delivery_city) ?: 'selected' }}>
                                                                            {{ $city }}
                                                                        </option>
                                                                    @endforeach
                                                                @endif
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Добавить в список</label>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control to_uppercase" id="place_of_delivery_city_add_city" placeholder="Добавить в список" aria-label="Добавить в список">
                                                            <div class="input-group-append">
                                                                <button class="btn btn-outline-secondary add_city_to_country" data-country_type="place_of_delivery" type="button">Добавить</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="grace_period">Кол-во суток льготного пользования</label>
                                            <input type="text" class="form-control digits_only"
                                                   name="grace_period"
                                                   value="{{ $application->grace_period }}"
                                                   placeholder="Кол-во суток льготного пользования">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Валюта СНП</label>
                                            <select class="form-control select2" name="snp_currency" id="snp_currency" required
                                                    data-placeholder="Выберите валюту" style="width: 100%;" >
                                                <option></option>
                                                @foreach(['RUB','USD','CNY'] as $snp_currency)
                                                    <option value="{{ $snp_currency }}"
                                                        {{ $snp_currency != $application->snp_currency ?: 'selected' }}>
                                                        {{ $snp_currency }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="callout callout-info">
                                    <h5>Диапазоны дней и ставки СНП</h5>
                                    <p>В формате 1-5 (с первого по пятый день), 6-10 (с шестого по десятый дни), без пробелов и других знаков, ставка без указания валюты, только цифра</p>
                                    <div class="form-group">
                                        <a class="btn btn-primary text-white text-decoration-none" id="snp_application_add">
                                            Добавить диапазон СНП
                                        </a>
                                    </div>
                                    <div class="row mt-4 snp-for-application-blocks">
                                        @if(!is_null($application->snp_range))
                                        @foreach($application->snp_range as $key => $value)
                                            <div class="col-md-4" id="snp_application_{{ $key }}">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Диапазон СНП</h3>
                                                        <div class="card-tools">
                                                            <button type="button" class="btn btn-tool remove_expense_block" data-block-delete="snp_application_{{ $key }}">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="form-group">
                                                            <label>{{ __('container.range_days') }}</label>
                                                            <input class="form-control" type="text" name="snp_application_array[{{ $key }}][range]"
                                                                   placeholder="{{ __('container.range_days') }}"
                                                                   value="{{ $value['range'] }}">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>{{ __('container.rate') }}</label>
                                                            <input class="form-control digits_only" type="text" name="snp_application_array[{{ $key }}][price]"
                                                                   placeholder="{{ __('container.rate') }}"
                                                                   value="{{ $value['price'] }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    </div>
                                    <div class="form-group">
                                        <label for="snp_after_range">Ставка СНП после диапазонов в день</label>
                                        <input type="text" class="form-control digits_only"
                                               value="{{ $application->snp_after_range }}"
                                               name="snp_after_range"
                                               placeholder="Ставка СНП после диапазонов в день">
                                    </div>
                                </div>
                                @if(is_null($application->containers_archived))
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
                                @endif
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
