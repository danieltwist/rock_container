@extends('layouts.project')
@section('title', __('supplier.all_suppliers'))
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    @if (isset($_GET['trash']))
                        <h1 class="m-0">Удаленные поставщики</h1>
                    @else
                        <h1 class="m-0">{{ __('supplier.all_suppliers') }}</h1>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @include('layouts.info_block')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('supplier.all_suppliers_list') }}</h3>
                </div>
                <div class="card-body">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default btn-sm suppliers_filters_country"
                                data-filter="">{{ __('general.all') }}
                        </button>
                        @foreach($countries as $country)
                            <button type="button" class="btn btn-default btn-sm suppliers_filters_country"
                                    data-filter="{{ $country->name }}">
                                @include('settings.country_switch', ['country' => $country->name])
                            </button>
                        @endforeach
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-default btn-sm suppliers_filters"
                                data-filter="">{{ __('general.all') }}
                        </button>
                        <button type="button" class="btn btn-default btn-sm suppliers_filters"
                                data-filter="Авто">{{ __('supplier.auto') }}
                        </button>
                        <button type="button" class="btn btn-default btn-sm suppliers_filters"
                                data-filter="ТЭО">{{ __('supplier.teo') }}
                        </button>
                        <button type="button" class="btn btn-default btn-sm suppliers_filters"
                                data-filter="Аренда">{{ __('supplier.rent') }}
                        </button>
                        <button type="button" class="btn btn-default btn-sm suppliers_filters"
                                data-filter="Прочее">{{ __('supplier.another') }}
                        </button>
                    </div>
                    <div class="mt-4">
                        <table id="suppliers_table" class="table table-striped">
                            <thead>
                            <tr>
                                <th style="width: 1%">
                                    #
                                </th>
                                <th style="width: 25%">
                                    {{ __('supplier.company_name') }}
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
