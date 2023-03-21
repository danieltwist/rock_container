@extends('layouts.project')
@section('title', __('report.summary_counterparty_report'))
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">{{ __('report.summary_counterparty_report') }}</h1>
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
                            <h3 class="card-title">{{ __('report.choose_counterparty') }}</h3>
                        </div>
                        <form action="{{ route('report_client_supplier_summary_load') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="client_id">{{ __('general.client') }}</label>
                                            <select class="form-control select2" name="client_id"
                                                    data-placeholder="{{ __('project.choose_client') }}" style="width: 100%;" required>
                                                <option></option>
                                                @foreach($clients as $client)
                                                    <option value="{{ $client->id }}">{{$client->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="supplier_id">{{ __('general.supplier') }}</label>
                                            <select class="form-control select2" name="supplier_id"
                                                    data-placeholder="{{ __('project.choose_supplier') }}" style="width: 100%;" required>
                                                <option></option>
                                                @foreach($suppliers as $supplier)
                                                    <option value="{{ $supplier->id }}">{{$supplier->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label>{{ __('general.date_range') }}</label>
                                        <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                            <i class="fa fa-calendar"></i>&nbsp;
                                            <span>Все</span> <i class="fa fa-caret-down"></i>
                                        </div>
                                        <input type="hidden" name="datarange" id="datarange" value="">
                                    </div>
                                </div>

                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary"
                                        data-action='{"update_div":{"div_id":"client-supplier_summary"}}'>
                                    {{ __('general.upload') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div id="client-supplier_summary"></div>
                </div>
            </div>
        </div>
    </section>
    @include('project.modals.confirm_invoice')
@endsection
