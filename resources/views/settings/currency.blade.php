@extends('layouts.project')
@section('title', __('settings.rate_adjustment_factor'))
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">{{ __('settings.rate_adjustment_factor') }}</h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @include('layouts.info_block')
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('settings.latest_rate_updates') }}</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th style="width: 20%">{{ __('general.date') }}</th>
                                    <th>{{ __('general.usd') }}</th>
                                    <th>{{ __('settings.factor_usd') }}</th>
                                    <th>{{ __('general.cny') }}</th>
                                    <th>{{ __('settings.factor_cny') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($currency_rates as $rate)
                                        <tr>
                                            <td>
                                                {{ $rate->created_at }}
                                            </td>
                                            <td>
                                                {{ $rate->USD }} / {{ $rate->usd_divided }}
                                            </td>
                                            <td>
                                                {{ $rate->usd_ratio }}
                                            </td>
                                            <td>
                                                {{ $rate->CNY }} / {{ $rate->cny_divided }}
                                            </td>
                                            <td>
                                                {{ $rate->cny_ratio }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('settings.factor') }}</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th style="width: 80%">{{ __('general.currency') }}</th>
                                    <th>{{ __('settings.factor') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>USD</td>
                                    <td>
                                        <a href="#" class="xedit" data-pk="1" data-name="value"
                                           data-model="Setting">
                                            {{ $usd_ratio }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>CNY</td>
                                    <td>
                                        <a href="#" class="xedit" data-pk="2" data-name="value"
                                           data-model="Setting">
                                            {{ $cny_ratio }}
                                        </a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer">
                            <form action="{{ route('update_currency_rates') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary float-right">
                                    {{ __('settings.update_currency_rates') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection


