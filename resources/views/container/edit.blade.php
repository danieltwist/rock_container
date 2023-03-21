@extends('layouts.project')
@section('title', __('container.edit_container'))
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">{{ __('container.edit_container') }} {{ $container->name }}</h1>
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
                            <h3 class="card-title">{{ __('container.change_container_details') }}</h3>
                        </div>
                        <form action="{{ route('container.update', $container->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="action" value="edit_container">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="name">{{ __('container.container_number') }}</label>
                                    <input type="text" class="form-control" name="name" id="container_name" placeholder="{{ __('container.container_number') }}" value="{{ $container->name }}" required>
                                </div>
                                <div class="form-group">
                                    <label>{{ __('container.size') }}</label>
                                    <select class="form-control" name="size">
                                        <option value="40 футов" {{ $container->size == '40 футов' ? 'selected' : '' }}>40 {{ __('container.foots') }}</option>
                                        <option value="20 футов" {{ $container->size == '20 футов' ? 'selected' : '' }}>20 {{ __('container.foots') }}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>{{ __('container.using') }}</label>
                                    <select class="form-control" name="type" id="create_container_usage_type" required>
                                        <option value="Аренда" {{ $container->type == 'Аренда' ? 'selected' : '' }}>{{ __('container.rent') }}</option>
                                        <option value="В собственности" {{ $container->type == 'В собственности' ? 'selected' : '' }}>{{ __('container.own') }}</option>
                                    </select>
                                </div>
                                <div class="form-group" id="create_container_supplier_group">
                                    <label>{{ __('container.owner') }}</label>
                                    <select class="form-control select2" name="supplier_id" id="create_container_supplier" data-placeholder="{{ __('container.owner') }}">
                                        <option></option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}"  {{ $container->supplier_id == $supplier->id ? 'selected' : '' }}>{{$supplier->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="start_date_for_us">{{ __('container.start_date_for_us') }}</label>
                                    <input type="text" class="form-control date_input invoice_deadline" name="start_date_for_us"
                                           placeholder="{{ __('container.start_date_for_us') }}"
                                           value="{{ $container->start_date_for_us }}">
                                </div>
                                <div class="form-group">
                                    <label for="start_date_for_client">{{ __('container.start_date_for_client') }}</label>
                                    <input type="text" class="form-control date_input invoice_deadline" name="start_date_for_client"
                                           placeholder="{{ __('container.start_date_for_client') }}"
                                           value="{{ $container->start_date_for_client }}">
                                </div>
                                <div class="form-group">
                                    <label for="border_date">{{ __('container.border_date') }}</label>
                                    <input type="text" class="form-control date_input invoice_deadline" name="border_date" placeholder="{{ __('container.border_date') }}" value="{{ $container->border_date }}">
                                </div>
                                <div class="form-group">
                                    <label for="svv">{{ __('container.svv') }}</label>
                                    <input type="text" class="form-control date_input invoice_deadline" name="svv" placeholder="{{ __('container.svv') }}" value="{{ $container->svv }}">
                                </div>
                                <div class="form-group">
                                    <label for="grace_period_for_client">{{ __('container.grace_period_for_client') }}</label>
                                    <input type="text" class="form-control digits_only" name="grace_period_for_client" placeholder="{{ __('container.grace_period_for_client') }}" value="{{ $container->grace_period_for_client }}">
                                </div>
                                <div class="form-group">
                                    <label for="grace_period_for_us">{{ __('container.grace_period_for_us') }}</label>
                                    <input type="text" class="form-control digits_only" name="grace_period_for_us" placeholder="{{ __('container.grace_period_for_us') }}" value="{{ $container->grace_period_for_us }}">
                                </div>
                                <div class="form-group">
                                    <label>Валюта СНП</label>
                                    <select class="form-control" name="snp_currency">
                                        <option value="RUB" {{ $container->snp_currency == 'RUB' ? 'selected' : '' }}>{{ __('general.ruble') }}</option>
                                        <option value="USD" {{ $container->snp_currency == 'USD' ? 'selected' : '' }}>{{ __('general.usd') }}</option>
                                        <option value="CNY" {{ $container->snp_currency == 'CNY' ? 'selected' : '' }}>{{ __('general.cny') }}</option>
                                    </select>
                                </div>
                                <div class="callout callout-info">
                                    <h5>{{ __('container.snp_amount_for_client') }}</h5>
                                    <p>{{ __('container.snp_amount_info') }}</p>
                                    <div class="form-group">
                                        <a class="btn btn-primary text-white text-decoration-none" id="snp_client_add">
                                            {{ __('container.snp_client_add') }}
                                        </a>
                                    </div>
                                    <div class="row mt-4 snp-for-client-blocks">
                                        @if(!is_null($container->snp_range_for_client))
                                            @foreach(unserialize($container->snp_range_for_client) as $key => $value)
                                                <div class="col-md-4" id="snp_client_{{ $key }}">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h3 class="card-title">{{ __('container.snp_range_for_client') }}</h3>
                                                            <div class="card-tools">
                                                                <button type="button" class="btn btn-tool remove_expense_block" data-block-delete="snp_client_{{ $key }}">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="form-group">
                                                                <label>{{ __('container.range_days') }}</label>
                                                                <input class="form-control" type="text" name="snp_client_array[{{ $key }}][range]"
                                                                       placeholder="{{ __('container.range_days') }}"
                                                                       value="{{ $value['range'] }}">
                                                            </div>
                                                            <div class="form-group">
                                                                <label>{{ __('container.rate') }}</label>
                                                                <input class="form-control digits_only" type="text" name="snp_client_array[{{ $key }}][price]"
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
                                        <label for="snp_amount_for_client">{{ __('container.snp_amount_for_client_after_range') }}</label>
                                        <input type="text" class="form-control digits_only" name="snp_amount_for_client" placeholder="{{ __('container.snp_amount_for_client_after_range') }}"
                                               value="{{ $container->snp_amount_for_client }}">
                                    </div>
                                </div>

                                <div class="callout callout-success">
                                    <h5>{{ __('container.snp_amount_for_us') }}</h5>
                                    <p>{{ __('container.snp_amount_info') }}</p>
                                    <div class="form-group">
                                        <a class="btn btn-primary text-white text-decoration-none" id="snp_us_add">
                                            {{ __('container.snp_us_add') }}
                                        </a>
                                    </div>
                                    <div class="row mt-4 snp-for-us-blocks">
                                        @if(!is_null($container->snp_range_for_us))
                                            @foreach(unserialize($container->snp_range_for_us) as $key => $value)
                                                <div class="col-md-4" id="snp_us_{{ $key }}">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h3 class="card-title">{{ __('container.snp_range_for_us') }}</h3>
                                                            <div class="card-tools">
                                                                <button type="button" class="btn btn-tool remove_expense_block" data-block-delete="snp_us_{{ $key }}">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="form-group">
                                                                <label>{{ __('container.range_days') }}</label>
                                                                <input class="form-control" type="text" name="snp_us_array[{{ $key }}][range]"
                                                                       placeholder="{{ __('container.range_days') }}"
                                                                       value="{{ $value['range'] }}">
                                                            </div>
                                                            <div class="form-group">
                                                                <label>{{ __('container.rate') }}</label>
                                                                <input class="form-control digits_only" type="text" name="snp_us_array[{{ $key }}][price]"
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
                                        <label for="snp_amount_for_us">{{ __('container.snp_amount_for_us_after_range') }}</label>
                                        <input type="text" class="form-control digits_only" name="snp_amount_for_us"
                                               placeholder="{{ __('container.snp_amount_for_us_after_range') }}"
                                               value="{{ $container->snp_amount_for_us }}">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>{{ __('general.additional_info') }}</label>
                                    <textarea class="form-control to_uppercase" rows="3" name="additional_info"
                                              placeholder="{{ __('general.additional_info') }}">{{ $container->additional_info }}</textarea>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">{{ __('general.update') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
