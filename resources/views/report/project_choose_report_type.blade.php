@extends('layouts.project')
@section('title', __('report.projects_report'))
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">{{ __('report.projects_report') }}</h1>
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
                        <form action="{{ route('get_report_project') }}" method="GET" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="datarange" id="datarange" value="">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="client">{{ __('report.interval') }}</label>
                                            <select class="form-control select2" name="report_type" id="report_project_type"
                                                    data-placeholder="{{ __('report.interval') }}" style="width: 100%;" required>
                                                <option></option>
                                                <option value="this_year">{{ __('report.this_year') }}</option>
                                                <option value="last_year">{{ __('report.last_year') }}</option>
                                                <option value="date_range">{{ __('report.date_range') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="client">{{ __('general.status') }}</label>
                                            <select class="form-control select2" name="filter"
                                                    data-placeholder="{{ __('report.choose_project_status') }}" style="width: 100%;" required>
                                                <option></option>
                                                <option value="finished"
                                                        data-toggle="tooltip"
                                                        data-placement="top"
                                                        title="Выборка проектов по дате закрытия">{{ __('report.choose_project_status_finished') }}</option>
                                                <option value="finished_paid_date"
                                                        data-toggle="tooltip"
                                                        data-placement="top"
                                                        title="Выборка проектов по дате закрытия">{{ __('report.choose_project_status_finished_paid_date') }}</option>
                                                <option value="done_unpaid"
                                                        data-toggle="tooltip"
                                                        data-placement="top"
                                                        title="Выборка проектов по дате закрытия">{{ __('report.choose_project_status_done_unpaid') }}</option>
                                                <option value="active"
                                                        data-toggle="tooltip"
                                                        data-placement="top"
                                                        title="Выборка проектов по дате создания">{{ __('report.choose_project_status_active') }}</option>
                                                <option value="all"
                                                        data-toggle="tooltip"
                                                        data-placement="top"
                                                        title="Выборка проектов по дате создания">{{ __('report.choose_project_status_all') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="client">{{ __('project.created') }}</label>
                                            <select class="form-control select2" name="user_id"
                                                    data-placeholder="{{ __('project.choose_user') }}" style="width: 100%;">
                                                <option value="Все">{{ __('general.all') }}</option>
                                                @foreach($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="client">{{ __('project.manager') }}</label>
                                            <select class="form-control select2" name="manager_id"
                                                    data-placeholder="{{ __('project.choose_user') }}" style="width: 100%;">
                                                <option value="Все">{{ __('general.all') }}</option>
                                                @foreach($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 d-none" id="report_project_date_range">
                                        <label for="country">{{ __('general.date_range') }}</label>
                                        <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                            <i class="fa fa-calendar"></i>&nbsp;
                                            <span>Все</span> <i class="fa fa-caret-down"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
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
@endsection
