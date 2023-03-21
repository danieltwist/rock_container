@extends('layouts.project')

@section('title', __('report.user_invoices_report'))

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">{{ __('report.user_invoices_report') }}</h1>
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
                            <h3 class="card-title">{{ __('report.report_parameters') }}</h3>
                        </div>
                        <form action="{{ route('get_report_user_invoices') }}" method="GET" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="datarange" id="datarange" value="">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="report_type">{{ __('report.interval') }}</label>
                                            <select class="form-control select2" name="report_type" id="report_project_type"
                                                    data-placeholder="{{ __('report.choose_report_type') }}" style="width: 100%;" required>
                                                <option></option>
                                                <option value="this_year">{{ __('report.this_year') }}</option>
                                                <option value="last_year">{{ __('report.last_year') }}</option>
                                                <option value="date_range">{{ __('report.date_range') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="user">{{ __('report.created') }}</label>
                                            <select class="form-control select2" name="user"
                                                    data-placeholder="{{ __('report.choose_user') }}" style="width: 100%;">
                                                @foreach($users as $user)
                                                    <option value="{{ $user->name }}">{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4 d-none" id="report_project_date_range">
                                        <label>{{ __('general.date_range') }}</label>
                                        <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                            <i class="fa fa-calendar"></i>&nbsp;
                                            <span>Все</span> <i class="fa fa-caret-down"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">{{ __('general.upload') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
