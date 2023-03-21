@extends('layouts.project')
@section('title', __('client.all_clients'))
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('client.all_clients') }}</h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @include('layouts.info_block')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('client.all_clients_list') }}</h3>
                </div>
                <div class="card-body">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default btn-sm clients_filters"
                                data-filter="">{{ __('general.all') }}
                        </button>
                        @foreach($countries as $country)
                            <button type="button" class="btn btn-default btn-sm clients_filters"
                                    data-filter="{{ $country->name }}">
                                @include('settings.country_switch', ['country' => $country->name])
                            </button>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        <table id="clients_table" class="table table-striped">
                            <thead>
                            <tr>
                                <th style="width: 1%">
                                    #
                                </th>
                                <th style="width: 25%">
                                    {{ __('client.company_name') }}
                                </th>
                                <th style="width: 20%">
                                    {{ __('general.requisites') }}
                                </th>
                                <th style="width: 20%">
                                    {{ __('general.info') }}
                                </th>
                                <th style="width: 15%">
                                    {{ __('general.resources') }}
                                </th>
                                <th style="width: 15%">
                                    {{ __('general.actions') }}
                                </th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
