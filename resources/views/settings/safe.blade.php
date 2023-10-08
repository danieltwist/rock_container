@extends('layouts.project')
@section('title', 'Настройки сейфа')
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">Настройки сейфа</h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @include('layouts.info_block')
            <form action="{{ route('update_safe_settings') }}" method="POST">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Настройки сейфа</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('general.client') }}</label>
                                    <select class="form-control select2" name="client_id"
                                            data-placeholder="{{ __('general.client') }}" style="width: 100%;" required>
                                        <option></option>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}" {{ $client_id == $client->id ? "selected" : "" }}>
                                                {{$client->name}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('general.supplier') }}</label>
                                    <select class="form-control select2" name="supplier_id"
                                            data-placeholder="{{ __('general.supplier') }}" style="width: 100%;" required>
                                        <option></option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}"  {{ $supplier_id == $supplier->id ? "selected" : "" }}>
                                                {{$supplier->name}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Дата начала подсчета</label>
                                    <input type="text"
                                           class="form-control date_input invoice_deadline"
                                           name="balance_date"
                                           placeholder="Дата начала подсчета"
                                           value="{{ \Carbon\Carbon::parse($balance_date)->format('d.m.Y') }}"
                                           required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Начальное сальдо</label>
                                    <input class="form-control rate_input"
                                           type="text"
                                           name="balance"
                                           placeholder="Начальное сальдо"
                                           value="{{ $balance }}"
                                           required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">{{ __('general.save') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
