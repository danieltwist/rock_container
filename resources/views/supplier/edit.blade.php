@extends('layouts.project')
@section('title', __('supplier.edit_supplier'))
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">{{ __('general.edit') }} {{ $supplier->name }}
                        <a href="{{ route('supplier.show', $supplier->id) }}" class="btn btn-default">{{ __('supplier.open_supplier_summary') }}</a>
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
                            <h3 class="card-title">{{ __('supplier.edit_supplier') }}</h3>
                        </div>
                        <form action="{{ route('supplier.update', $supplier->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="name">{{ __('supplier.company_name') }}</label>
                                    <input type="text" class="form-control" name="name" placeholder="{{ __('supplier.company_name') }}" value="{{ $supplier->name }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="short_name">{{ __('supplier.short_company_name') }}</label>
                                    <input type="text" class="form-control" name="short_name" placeholder="{{ __('supplier.short_company_name') }}" value="{{ $supplier->short_name }}">
                                </div>

                                <div class="form-group">
                                    <label>{{ __('supplier.link_with_client') }}</label>
                                    <select class="form-control select2" name="linked" data-placeholder="{{ __('project.select_client') }}" style="width: 100%;">
                                        <option value="Отменить связь">{{ __('supplier.not_linked') }}</option>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}" {{ $client->id == $supplier->linked ? 'selected' : '' }}>{{$client->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>{{ __('general.requisites') }}</label>
                                    <textarea class="form-control" rows="7" name="requisites" placeholder="{{ __('general.requisites') }}">{{ $supplier->requisites }}</textarea>
                                </div>

                                <div class="form-group">
                                    <label for="inn">{{ __('general.inn_license_number') }}</label>
                                    <input type="text" class="form-control" name="inn" placeholder="{{ __('general.inn_license_number') }}" value="{{ $supplier->inn }}">
                                </div>

                                <div class="form-group">
                                    <label for="country">{{ __('general.country') }}</label>
                                    <select class="form-control" name="country">
                                        @foreach($countries as $country)
                                            <option value="{{ $country->name }}" {{ $supplier->country == $country->name ? 'selected' : '' }}>{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>{{ __('general.type') }}</label>
                                    <select class="select2" multiple="multiple" name="type[]" data-placeholder="Выберите тип" style="width: 100%;">
                                        <option value="Авто" {{ in_array('Авто', explode(', ',$supplier->type)) ? 'selected' : ''}}>{{ __('supplier.auto') }}</option>
                                        <option value="ТЭО" {{ in_array('ТЭО', explode(', ',$supplier->type)) ? 'selected' : ''}}>{{ __('supplier.teo') }}</option>
                                        <option value="Аренда" {{ in_array('Аренда', explode(', ',$supplier->type)) ? 'selected' : ''}}>{{ __('supplier.rent') }}</option>
                                        <option value="Прочее" {{ in_array('Прочее', explode(', ',$supplier->type)) ? 'selected' : ''}}>{{ __('supplier.another') }}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="card">{{ __('general.counterparty_card') }}</label>
                                    <input type="file" class="form-control-file" name="card">
                                </div>
                                <div class="contract-items">
                                    <div class="card card-body">
                                        <div class="form-group">
                                            <span class="btn btn-success add_contract_item">{{ __('supplier.add_contract') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="email">E-mail</label>
                                    <input type="email" class="form-control" name="email" placeholder="E-mail" value="{{ $supplier->email }}" >
                                </div>
                                <div class="form-group">
                                    <label for="director">Директор</label>
                                    <input type="text" class="form-control" name="director" placeholder="Директор" value="{{ $supplier->director }}">
                                </div>
                                <div class="form-group">
                                    <label>{{ __('general.additional_info') }}</label>
                                    <textarea class="form-control" rows="3" name="additional_info" placeholder="{{ __('general.additional_info') }}">
                                        {{ $supplier->additional_info }}
                                    </textarea>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">{{ __('general.update') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card card-default">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('supplier.supplier_contracts') }}</h3>
                        </div>
                        <div class="card-body">
                            @if ($supplier->contracts->isNotEmpty())
                                @foreach($supplier->contracts as $contract)
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
                                            <input type="hidden" name="action" value="supplier">
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label>{{ __('supplier.contract_number') }}</label>
                                                    <input type="text" class="form-control" name="name" placeholder="{{ __('supplier.contract_number') }}" value="{{ $contract->name }}">
                                                </div>
                                                <div class="form-group">
                                                    <label>{{ __('supplier.contract_sign_date') }}</label>
                                                    <input type="text" class="form-control" name="date_start" placeholder="{{ __('supplier.contract_sign_date') }}" value="{{ $contract->date_start }}">
                                                </div>
                                                <div class="form-group">
                                                    <label>{{ __('supplier.contract_validity_period') }}</label>
                                                    <input type="text" class="form-control" name="date_period" placeholder="{{ __('supplier.contract_validity_period') }}" value="{{ $contract->date_period }}">
                                                </div>
                                                <div class="form-group">
                                                    <label>{{ __('supplier.contract_type') }}</label>
                                                    <input type="text" class="form-control" name="additional_info" placeholder="{{ __('supplier.contract_type') }}" value="{{ $contract->additional_info }}">
                                                </div>
                                                <div class="form-group">
                                                    <label for="contract">{{ __('supplier.upload_new_contract_file') }}</label>
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
                            {{ __('supplier.no_contracts_for_this_supplier') }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
