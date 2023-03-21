@extends('layouts.project')
@section('title', __('settings.countries_list'))
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">{{ __('settings.countries_list') }}</h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @include('layouts.info_block')
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('settings.available_countries') }}</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th style="width: 80%">{{ __('general.name') }}</th>
                                    <th>{{ __('general.removing') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($countries as $country)
                                    <tr>
                                        <td>{{ $country->name }}</td>
                                        <td>
                                            <form class="inline-block" action="{{ route('countries.destroy', $country->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm delete-btn">{{ __('general.remove') }}</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <form action="{{ route('countries.store') }}" method="POST">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('settings.add_new_country') }}</h3>
                                @csrf
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="name">{{ __('settings.country_name') }}</label>
                                    <input type="text" class="form-control" name="name"
                                           placeholder="{{ __('settings.country_name') }}">
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary float-right">{{ __('general.add') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection


