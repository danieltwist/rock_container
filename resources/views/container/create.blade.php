@extends('layouts.project')
@section('title', __('container.new_container'))
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('container.new_container') }}</h1>
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
                            <h3 class="card-title">{{ __('container.add_container') }}</h3>
                        </div>
                        <form action="{{ route('container.store') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="name">{{ __('container.container_number') }}</label>
                                    <input type="text" class="form-control" name="name" id="container_name" placeholder="{{ __('container.container_number') }}" required>
                                </div>
                                <div class="form-group">
                                    <label>{{ __('container.size') }}</label>
                                    <select class="form-control" name="size">
                                        <option value="40 футов">40 {{ __('container.foots') }}</option>
                                        <option value="40 футов">20 {{ __('container.foots') }}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>{{ __('container.using') }}</label>
                                    <select class="form-control" name="type" id="create_container_usage_type" required>
                                        <option value="Аренда">{{ __('container.rent') }}</option>
                                        <option value="В собственности">{{ __('container.own') }}</option>
                                    </select>
                                </div>
                                <div class="form-group" id="create_container_supplier_group">
                                    <label>{{ __('container.owner') }}</label>
                                    <select class="form-control select2" name="supplier_id" id="create_container_supplier" data-placeholder="{{ __('container.own') }}">
                                        <option></option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}">{{$supplier->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="start_date_for_us">{{ __('container.start_date_for_us') }}</label>
                                    <input type="text" class="form-control date_input invoice_deadline" name="start_date_for_us" placeholder="{{ __('container.start_date_for_us') }}">
                                </div>
                                <div class="form-group">
                                    <label for="start_date_for_client">{{ __('container.start_date_for_client') }}</label>
                                    <input type="text" class="form-control date_input invoice_deadline" name="start_date_for_client" placeholder="{{ __('container.start_date_for_client') }}">
                                </div>
                                <div class="form-group">
                                    <label for="svv">{{ __('container.svv') }}</label>
                                    <input type="text" class="form-control date_input invoice_deadline" name="svv" placeholder="{{ __('container.svv') }}">
                                </div>
                                <div class="form-group">
                                    <label for="grace_period_for_client">{{ __('container.grace_period_for_client') }}</label>
                                    <input type="text" class="form-control digits_only" name="grace_period_for_client" placeholder="{{ __('container.grace_period_for_client') }}">
                                </div>
                                <div class="form-group">
                                    <label for="grace_period_for_us">{{ __('container.grace_period_for_us') }}</label>
                                    <input type="text" class="form-control digits_only" name="grace_period_for_us" placeholder="{{ __('container.grace_period_for_us') }}">
                                </div>
                                <div class="form-group">
                                    <label>{{ __('container.snp_currency') }}</label>
                                    <select class="form-control" name="snp_currency">
                                        <option value="RUB">{{ __('general.ruble') }}</option>
                                        <option value="USD">{{ __('general.usd') }}</option>
                                        <option value="CNY">{{ __('general.cny') }}</option>
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
                                    <div class="row mt-4 snp-for-client-blocks"></div>
                                    <div class="form-group">
                                        <label for="snp_amount_for_client">{{ __('container.snp_amount_for_client_after_range') }}</label>
                                        <input type="text" class="form-control digits_only" name="snp_amount_for_client" placeholder="{{ __('container.snp_amount_for_client_after_range') }}">
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
                                    <div class="row mt-4 snp-for-us-blocks"></div>
                                    <div class="form-group">
                                        <label for="snp_amount_for_us">{{ __('container.snp_amount_for_us_after_range') }}</label>
                                        <input type="text" class="form-control digits_only" name="snp_amount_for_us" placeholder="{{ __('container.snp_amount_for_us_after_range') }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>{{ __('general.additional_info') }}</label>
                                    <textarea class="form-control to_uppercase" rows="3" name="additional_info" placeholder="{{ __('general.additional_info') }}"></textarea>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">{{ __('container.add_container') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
