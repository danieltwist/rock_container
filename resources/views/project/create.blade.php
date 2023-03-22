@extends('layouts.project')
@section('title', __('project.new_project'))
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">{{ __('project.new_project') }}</h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @include('layouts.info_block')
            @if (!isset($_COOKIE['yaToken']))
                <div class="alert alert-warning alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-ban"></i> {{ __('project.attention') }}</h5>
                    {{ __('project.not_auth_in_yandex') }}
                </div>
            @endif
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-default">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('project.create_new_project') }}</h3>
                        </div>
                        @can('create projects')
                        <form action="{{ route('project.store') }}" method="POST" id="create_project">
                            @csrf
                            <div class="card-body">
                                @if ($errors->any())
                                    <div class="callout callout-danger">
                                        <h5>{{ __('general.error') }}</h5>
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </div>
                                @endif
                                <input type="hidden" name="user_id" value="{{ $current_user_id }}">
                                <div class="form-group">
                                    <label for="name">{{ __('project.project_name') }}</label>
                                    <input type="text" class="form-control project-name" name="name" placeholder="{{ __('project.project_name') }}"
                                           value="{{ substr($today, 2) }}" id="project_name" required>
                                </div>
                                @if (in_array($role, ['super-admin','director']))
                                    <div class="form-group clearfix">
                                        <div class="icheck-primary d-inline">
                                            <input type="checkbox" id="management_expenses" name="management_expenses">
                                            <label for="management_expenses">УР проект</label>
                                        </div>
                                    </div>
                                @endif
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ __('general.client') }}</label>
                                            <select class="form-control select2" name="client_id" id="main_client"
                                                    data-placeholder="{{ __('project.choose_client') }}" style="width: 100%;" required>
                                                <option></option>
                                                @foreach($clients as $client)
                                                    <option value="{{ $client->id }}">{{$client->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ __('project.additional_clients') }}</label>
                                            <select class="form-control select2" name="additional_clients[]" id="additional_client"
                                                    data-placeholder="{{ __('project.choose_additional_clients') }}" style="width: 100%;" multiple>
                                                <option></option>
                                                @foreach($clients as $client)
                                                    <option value="{{ $client->id }}">{{$client->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="from">{{ __('project.send_from') }}</label>
                                    <input type="text" class="form-control to_uppercase" name="from"
                                           placeholder="{{ __('project.send_from') }}">
                                </div>
                                <div class="form-group">
                                    <label for="from">{{ __('project.pogranperehod') }}</label>
                                    <input type="text" class="form-control to_uppercase" name="pogranperehod" placeholder="{{ __('project.pogranperehod') }}">
                                </div>
                                <div class="form-group">
                                    <label for="to">{{ __('project.send_to') }}</label>
                                    <input type="text" class="form-control to_uppercase" name="to"
                                           placeholder="{{ __('project.send_to') }}">
                                </div>

                                <div class="form-group">
                                    <label for="freight_info">{{ __('project.freight_info') }}</label>
                                    <input type="text" class="form-control to_uppercase" name="freight_info"
                                           placeholder="{{ __('project.freight_info') }}">
                                </div>

                                <div class="form-group">
                                    <label for="freight_amount">{{ __('project.freight_amount') }}</label>
                                    <input type="text" class="form-control digits_only" name="freight_amount"
                                           placeholder="{{ __('project.freight_amount') }}">
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <label>{{ __('project.client_manager') }}</label>
                                        <select class="form-control select2" name="manager_id" id="manager_select" onchange="MakeFoldersList();"
                                                data-placeholder="{{ __('project.client_manager') }}" style="width: 100%;">
                                            <option></option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" data-folder="{{ $user->folder_on_yandex_disk }}">{{$user->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label>{{ __('project.logist') }}</label>
                                        <select class="form-control select2" name="logist_id" id="logist_select" onchange="MakeFoldersList();"
                                                data-placeholder="{{ __('project.logist') }}"
                                                style="width: 100%;">
                                            <option></option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" data-folder="{{ $user->folder_on_yandex_disk }}">{{$user->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label>{{ __('project.choose_folder_on_yandex') }}</label>
                                        <select class="form-control select2" name="folder_yandex_disk" id="folder_yandex_disk"
                                                data-placeholder="{{ __('project.choose_folder_create_project') }}"
                                                style="width: 100%;">
                                            <option></option>
                                        </select>
                                    </div>
                                </div>
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="prepayment">{{ __('project.prepayment') }}</label>
                                                <input type="text" class="form-control" name="prepayment"
                                                       placeholder="{{ __('project.prepayment_with_currency') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="planned_payment_date">{{ __('project.planned_payment_date') }}</label>
                                                <input type="text" class="form-control date_input invoice_deadline" name="planned_payment_date"
                                                       placeholder="{{ __('project.planned_payment_date') }}">
                                            </div>
                                        </div>
                                    </div>
                                <div class="form-group mt-2">
                                    <label>{{ __('general.additional_info') }}</label>
                                    <textarea class="form-control to_uppercase" rows="3" name="additional_info"
                                              placeholder="{{ __('general.additional_info') }}"></textarea>
                                </div>
                                <h5 class="mt-4">{{ __('project.cost_part') }}</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('general.supplier') }}</label>
                                                <select class="form-control select2" id="expense_client"
                                                        data-placeholder="{{ __('general.supplier') }}" style="width: 100%;">
                                                    <option value="all">{{ __('general.all') }}</option>
                                                    @foreach($suppliers as $supplier)
                                                        <option value="{{ $supplier->id }}">{{$supplier->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ __('project.choose_expense_type') }}</label>
                                                <input type="text" class="form-control twitter-typeahead"
                                                       id="expense_type"
                                                       placeholder="{{ __('project.choose_expense_type') }}"
                                                       autocomplete="off"
                                                       spellcheck="false">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="expense_description">{{ __('project.description') }}</label>
                                                <input type="text" class="form-control" id="expense_description"
                                                       placeholder="{{ __('project.description') }}">
                                            </div>
                                            <div class="form-group">
                                                <a class="btn btn-success" id="add_expense">{{ __('project.add_to_expenses') }}</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-4 expense-blocks">
                                        <input type="hidden" id="usd_rate" value="{{ $rates->USD }}">
                                        <input type="hidden" id="cny_rate" value="{{ $rates->CNY }}">
                                        <input type="hidden" id="usd_divided" value="{{ $rates->usd_divided }}">
                                        <input type="hidden" id="cny_divided" value="{{ $rates->cny_divided }}">
                                        <input type="hidden" id="usd_ratio" value="{{ $rates->usd_ratio }}">
                                        <input type="hidden" id="cny_ratio" value="{{ $rates->cny_ratio }}">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h3 class="card-title">{{ __('project.whole_project_cost') }}</h3>
                                                    <div class="card-tools">
                                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row mt-3">
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label>{{ __('general.currency') }}</label>
                                                                <select class="form-control choose_currency" name="currency" data-type="project"
                                                                        data-placeholder="{{ __('general.currency') }}" style="width: 100%;" required>
                                                                    <option value="RUB"
                                                                            data-currency-rate="1">
                                                                        {{ __('general.ruble') }}
                                                                    </option>
                                                                    <option value="USD"
                                                                            data-currency-rate="{{ $rates->USD }}"
                                                                            data-divided="{{ $rates->usd_divided }}"
                                                                            data-ratio="{{ $rates->usd_ratio }}">
                                                                        {{ __('general.usd') }}, {{ __('general.cb_rate') }} {{ $rates->USD }}
                                                                    </option>
                                                                    <option value="CNY"
                                                                            data-currency-rate="{{ $rates->CNY }}"
                                                                            data-divided="{{ $rates->cny_divided }}"
                                                                            data-ratio="{{ $rates->cny_ratio }}">
                                                                        {{ __('general.cny') }}, {{ __('general.cb_rate') }} {{ $rates->CNY }}
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label>{{ __('general.cb_rate_corrected') }} <span id="ratio"></span></label>
                                                                <input class="form-control" type="text" name="cb_rate" id="project_rate"
                                                                       placeholder="{{ __('general.cb_rate_corrected') }}"
                                                                       value="1">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label>{{ __('project.1pc_price') }}</label>
                                                                <input class="form-control digits_only price_1pc" type="text"
                                                                       name="price_1pc" id="project_price_1pc"
                                                                       placeholder="{{ __('project.1pc_price') }}" value="0">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label>{{ __('general.quantity') }}</label>
                                                                <input class="form-control digits_only amount" id="project_amount" data-type="project"
                                                                       type="text" name="amount" placeholder="{{ __('general.quantity') }}" value="1">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="price">{{ __('project.project_price_in_currency') }}</label>
                                                                <input type="text" class="form-control digits_only"
                                                                       id="project_price_in_currency" name="price_in_currency"
                                                                       placeholder="{{ __('project.project_price_in_currency') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="price">{{ __('project.project_total_price_in_rub') }}</label>
                                                                <input type="text" class="form-control digits_only"
                                                                       id="project_total_price_in_rub" name="price_in_rub"
                                                                       placeholder="{{ __('project.project_total_price_in_rub') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>{{ __('project.planned_income') }}</label>
                                                                <input type="text" class="form-control digits_only"
                                                                       id="project_planned_revenue" placeholder="{{ __('project.planned_income') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>{{ __('project.planned_outcome') }}</label>
                                                                <input type="text" class="form-control digits_only"
                                                                       id="project_planned_costs" name="planned_costs"
                                                                       placeholder="{{ __('project.planned_outcome') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>{{ __('project.planned_profit') }}</label>
                                                                <input type="text" class="form-control rate_input"
                                                                       id="project_planned_profit" name="planned_profit"
                                                                       placeholder="{{ __('project.planned_profit') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary submit-button">
                                    {{ __('project.create_project') }}
                                </button>
                            </div>
                        </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
