@extends('layouts.project')
@section('title', __('client.new_client'))
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('client.new_client') }}</h1>
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
                            <h3 class="card-title">{{ __('client.add_new_client') }}</h3>
                        </div>
                        <form action="{{ route('client.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="country">{{ __('general.country') }}</label>
                                    <select class="form-control" name="country" id="client_country">
                                        @foreach($countries as $country)
                                            <option value="{{ $country->name }}">{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group client_status_div">
                                    <label for="name_1">{{ __('general.legal_status') }}</label>
                                    <select class="form-control" name="name_1">
                                        <option value="ООО">ООО</option>
                                        <option value="АО">АО</option>
                                        <option value="ЗАО">ЗАО</option>
                                        <option value="ОАО">ОАО</option>
                                        <option value="ИП">ИП</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="name_2">{{ __('client.company_name') }}</label>
                                    <input type="text" class="form-control" name="name_2"
                                           placeholder="{{ __('client.company_name') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="short_name">{{ __('client.short_company_name') }}</label>
                                    <input type="text" class="form-control" name="short_name"
                                           placeholder="{{ __('client.short_company_name') }}">
                                </div>
                                <div class="form-group">
                                    <label>{{ __('client.link_with_supplier') }}</label>
                                    <select class="form-control select2" name="linked"
                                            data-placeholder="{{ __('project.select_supplier') }}" style="width: 100%;">
                                        <option></option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}">{{$supplier->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mt-3">
                                    <label>{{ __('general.requisites') }}</label>
                                    <textarea class="form-control" rows="7" name="requisites"
                                              placeholder="{{ __('general.requisites') }}"></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="inn">{{ __('general.inn_license_number') }}</label>
                                    <input type="text" class="form-control" name="inn"
                                           placeholder="{{ __('general.inn_license_number') }}">
                                </div>

                                <div class="form-group">
                                    <label for="email">E-mail</label>
                                    <input type="email" class="form-control" name="email" placeholder="E-mail">
                                </div>
                                <div class="form-group">
                                    <label for="director">Директор</label>
                                    <input type="text" class="form-control" name="director" placeholder="Директор">
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
                                    <textarea class="form-control" rows="3" name="additional_info"
                                              placeholder="{{ __('general.additional_info') }}"></textarea>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">{{ __('client.add_client') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-12">
                    <form action="{{ route('upload_client') }}" method="POST" enctype="multipart/form-data">
                        <div class="card collapsed">
                            <div class="card-header cursor-pointer" data-card-widget="collapse">
                                <h3 class="card-title">{{ __('client.upload_clients_excel_list') }}</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                            class="fas fa-minus"></i></button>
                                </div>
                            </div>
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="clients_list">{{ __('client.clients_list_excel_file') }}</label>
                                    <input type="file" class="form-control-file" name="clients_list">
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">{{ __('client.upload_list') }}</button>
                                <a href="/storage/templates/clients_excel_template.xlsx" download
                                   class="btn btn-success float-right">{{ __('general.download_template') }}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
