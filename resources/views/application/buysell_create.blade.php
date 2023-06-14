@extends('layouts.project')
@section('title', 'Добавить заявку')
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Добавить заявку покупка / продажа</h1>
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
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="name">Название заявки</label>
                                            <input type="text" class="form-control" name="name"
                                                   placeholder="Название заявки"
                                                   value="{{ $latest_application_id }}"
                                                   required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="created_at">Дата заявки</label>
                                            <input type="text" class="form-control date_input" name="created_at"
                                                   placeholder="Дата заявки"
                                                   value="{{ \Carbon\Carbon::now()->format('d.m.Y') }}"
                                                   required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="application_type">Тип заявки</label>
                                            <select class="form-control select2" name="application_type" id="application_type" required
                                                    data-placeholder="Тип заявки" style="width: 100%;" >
                                                <option></option>
                                                <option value="Покупка">Покупка</option>
                                                <option value="Продажа">Продажа</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="counterparty_type">Тип контрагента</label>
                                            <input type="text" class="form-control" name="counterparty_type" id="application_direction" required readonly="true"
                                                   placeholder="Выберите тип заявки">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
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
                                    <div class="col-md-4">
                                        <div id="application_counterparty_contracts"></div>
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
                                            <label for="price_amount">Стоимость 1 ктк</label>
                                            <input type="text" class="form-control rate_input" name="price_amount"
                                                   placeholder="Стоимость 1 ктк" required>
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
