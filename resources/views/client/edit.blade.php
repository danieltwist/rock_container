@extends('layouts.project')
@section('title', __('client.edit_client'))
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">{{ __('general.edit') }} {{ $client->name }}
                        <a href="{{ route('client.show', $client->id) }}" class="btn btn-default">{{ __('client.open_client_summary') }}</a>
                    </h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @include('layouts.info_block')
            <div class="row">
                <div class="col-md-6">
                    <div class="card card-default">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('client.edit_client') }}</h3>
                        </div>
                        <form action="{{ route('client.update', $client->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="name">{{ __('client.company_name') }}</label>
                                    <input type="text" class="form-control" name="name" placeholder="{{ __('client.company_name') }}" value="{{ $client->name }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="short_name">{{ __('client.short_company_name') }}</label>
                                    <input type="text" class="form-control" name="short_name" placeholder="{{ __('client.short_company_name') }}" value="{{ $client->short_name }}">
                                </div>
                                <div class="form-group">
                                    <label>{{ __('client.link_with_supplier') }}</label>
                                    <select class="form-control select2" name="linked" data-placeholder="{{ __('client.link_with_supplier') }}" style="width: 100%;">
                                        <option value="Отменить связь">{{ __('client.not_linked') }}</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" {{ $supplier->id == $client->linked ? 'selected' : '' }}>{{$supplier->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>{{ __('general.requisites') }}</label>
                                    <textarea class="form-control" rows="7" name="requisites" placeholder="{{ __('general.requisites') }}">{{ $client->requisites }}</textarea>
                                </div>

                                <div class="form-group">
                                    <label for="inn">{{ __('general.inn_license_number') }}</label>
                                    <input type="text" class="form-control" name="inn" placeholder="{{ __('general.inn_license_number') }}" value="{{ $client->inn }}">
                                </div>

                                <div class="form-group">
                                    <label for="country">{{ __('general.country') }}</label>
                                    <select class="form-control" name="country">
                                        @foreach($countries as $country)
                                            <option value="{{ $country->name }}" {{ $client->country == $country->name ? 'selected' : '' }}>{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="email">E-mail</label>
                                    <input type="email" class="form-control" name="email" placeholder="Адрес E-mail" value="{{ $client->email }}">
                                </div>
                                <div class="form-group">
                                    <label for="director">Директор</label>
                                    <input type="text" class="form-control" name="director" placeholder="Директор" value="{{ $client->director }}">
                                </div>
                                <div class="form-group">
                                    <label for="card">{{ __('general.counterparty_card') }}</label>
                                    <input type="file" class="form-control-file" name="card">
                                </div>
                                <div class="contract-items">
                                    <div class="card card-body">
                                        <div class="form-group">
                                            <span class="btn btn-success add_contract_item">{{ __('client.add_contract') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>{{ __('general.additional_info') }}</label>
                                    <textarea class="form-control" rows="3" name="additional_info" placeholder="{{ __('general.additional_info') }}"> {{ $client->additional_info }}</textarea>
                                </div>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">{{ __('general.update') }}</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card card-default">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('client.client_contracts') }}</h3>
                        </div>
                        <div class="card-body">
                            @if ($client->contracts->isNotEmpty())
                                @foreach($client->contracts as $contract)
                                    <div class="card card-default">
                                        <div class="card-header">
                                            <h3 class="card-title">№{{ $contract->name }}</h3>
                                            <div class="card-tools">
                                                <a href="{{ Storage::url($contract->file) }}" download class="btn btn-block btn-primary btn-xs">
                                                    {{ __('general.download_contract') }}
                                                </a>
                                            </div>
                                        </div>
                                        <form class="button-delete-inline" action="{{ route('contract.update', $contract->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="action" value="client">
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label>{{ __('client.contract_number') }}</label>
                                                <input type="text" class="form-control" name="name" placeholder="{{ __('client.contract_number') }}"
                                                       value="{{ $contract->name }}">
                                            </div>
                                            <div class="form-group">
                                                <label>{{ __('client.contract_sign_date') }}</label>
                                                <input type="text" class="form-control" name="date_start" placeholder="{{ __('client.contract_sign_date') }}"
                                                       value="{{ $contract->date_start }}">
                                            </div>
                                            <div class="form-group">
                                                <label>{{ __('client.contract_validity_period') }}</label>
                                                <input type="text" class="form-control" name="date_period" placeholder="{{ __('client.contract_validity_period') }}"
                                                       value="{{ $contract->date_period }}">
                                            </div>
                                            <div class="form-group">
                                                <label>{{ __('client.contract_type') }}</label>
                                                <input type="text" class="form-control" name="additional_info" placeholder="{{ __('client.contract_type') }}"
                                                       value="{{ $contract->additional_info }}">
                                            </div>
                                            <div class="form-group">
                                                <label for="contract">{{ __('client.upload_new_contract_file') }}</label>
                                                <input type="file" class="form-control-file" name="file">
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                                <button type="submit" class="btn btn-primary">{{ __('general.update') }}</button>
                                            </form>
                                            <form class="button-delete-inline float-right" action="{{ route('contract.destroy', $contract->id) }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger delete-btn">{{ __('general.remove') }}</button>&nbsp;
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                {{ __('client.no_contracts_for_this_client') }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
