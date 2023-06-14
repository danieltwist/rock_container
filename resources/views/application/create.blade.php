@extends('layouts.project')
@section('title', 'Добавить заявку')
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Добавить заявку</h1>
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
                            <h3 class="card-title">Новая заявка</h3>
                        </div>
                        <form action="{{ route('application.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="application_type">Тип заявки</label>
                                            <select class="form-control select2" name="application_type" id="application_type" required
                                                    data-placeholder="Тип заявки" style="width: 100%;" >
                                                <option></option>
                                                <option value="Поставщик">Взять в аренду</option>
                                                <option value="Клиент">Выдать в аренду</option>
                                                <option value="Подсыл">Подсыл</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="counterparty_type">Тип контрагента</label>
                                            <select class="form-control select2" name="counterparty_type" id="application_direction" required
                                                    data-placeholder="Выберите тип контрагента" style="width: 100%;">
                                                <option></option>
                                                <option value="Поставщик">Поставщик</option>
                                                <option value="Клиент">Клиент</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group d-none" id="client_group">
                                            <label for="client_id">{{ __('general.client') }}</label>
                                            <select class="form-control select2 application_load_contract" name="client_id"
                                                    id="application_client_select"
                                                    data-counterparty_type="client"
                                                    data-placeholder="{{ __('project.select_client') }}" style="width: 100%;">
                                                <option></option>
                                                @foreach($clients as $client)
                                                    <option value="{{ $client->id }}">{{$client->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group d-none" id="supplier_group">
                                            <label for="supplier_id">{{ __('general.supplier') }}</label>
                                            <select class="form-control select2 application_load_contract" required name="supplier_id"
                                                    id="application_supplier_select"
                                                    data-counterparty_type="supplier"
                                                    data-placeholder="{{ __('project.select_supplier') }}" style="width: 100%;" >
                                                <option></option>
                                                @foreach($suppliers as $supplier)
                                                    <option value="{{ $supplier->id }}">{{$supplier->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div id="application_counterparty_contracts"></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Название заявки</label>
                                            <input type="text" class="form-control" name="name"
                                                   placeholder="Название заявки"
                                                   value="{{ $latest_application_id }}"
                                                   required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="created_at">Дата заявки</label>
                                            <input type="text" class="form-control date_input" name="created_at"
                                                   placeholder="Дата заявки"
                                                   value="{{ \Carbon\Carbon::now()->format('d.m.Y') }}"
                                                   required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="containers_amount">Количество ктк</label>
                                            <input type="text" class="form-control digits_only" name="containers_amount"
                                                   placeholder="Количество ктк" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="price_amount">Стоимость услуг</label>
                                            <input type="text" class="form-control digits_only" name="price_amount"
                                                   placeholder="Стоимость услуг" required>
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
                                                <option value="RUB">RUB</option>
                                                <option value="USD">USD</option>
                                                <option value="CNY">CNY</option>
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
                                                    <option value="{{ $country->name }}">{{ $country->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-8" id="send_from_country_div"></div>
                                </div>
                                <div class="d-none" id="send_to_div">
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
                                                        <option value="{{ $country->name }}">{{ $country->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-8" id="send_to_country_div"></div>
                                    </div>
                                </div>
                                <div class="d-none" id="place_of_delivery_div">
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
                                                        <option value="{{ $country->name }}">{{ $country->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-8" id="place_of_delivery_country_div"></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="grace_period_for_us">Кол-во суток льготного пользования</label>
                                            <input type="text" class="form-control digits_only" name="grace_period"
                                                   placeholder="Кол-во суток льготного пользования">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Валюта СНП</label>
                                            <select class="form-control select2" name="snp_currency" id="snp_currency" required
                                                    data-placeholder="Выберите валюту" style="width: 100%;" >
                                                <option></option>
                                                <option value="RUB">RUB</option>
                                                <option value="USD">USD</option>
                                                <option value="CNY">CNY</option>
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
                                    <div class="row mt-4 snp-for-application-blocks"></div>
                                    <div class="form-group">
                                        <label for="snp_after_range">Ставка СНП после диапазонов в день</label>
                                        <input type="text" class="form-control digits_only" name="snp_after_range"
                                               placeholder="Ставка СНП после диапазонов в день">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Вставьте список контейнеров</label>
                                            <textarea class="form-control" rows="10" id="application_containers"
                                                      placeholder="Список контейнеров"
                                                      oninput="this.value = this.value.toUpperCase()"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-8" id="dynamic_containers_div"></div>
                                </div>
                                <div class="form-group">
                                    <label>Тип контейнеров</label>
                                    <select class="form-control select2" name="containers_type"
                                            data-placeholder="Выберите тип контейнеров" style="width: 100%;" required>
                                        <option></option>
                                        <option value="40HC">40HC</option>
                                        <option value="20DC">20DC</option>
                                        <option value="40OT">40OT</option>
                                        <option value="20OT">20OT</option>
                                        <option value="40DC">40DC</option>
                                        <option value="40RF">40RF</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>{{ __('general.additional_info') }}</label>
                                    <textarea class="form-control to_uppercase" rows="3" name="additional_info" placeholder="{{ __('general.additional_info') }}"></textarea>
                                </div>
                                <div class="form-group clearfix">
                                    <div class="icheck-primary d-inline">
                                        <input type="checkbox" id="surcharge" name="surcharge">
                                        <label for="surcharge">Доплатная заявка</label>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Добавить заявку</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
