@extends('layouts.project')
@section('title', __('project.edit_project'))
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('project.edit_project') }} {{ $project['name'] }}</h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @include('layouts.info_block')
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('project.edit_project') }}</h3>
                        </div>
                        <form action="{{ route('project.update', $project->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="action" value="update_project">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="name">{{ $project->name }}</label>
                                    <input type="text" class="form-control project-name" name="name" placeholder="{{ $project->name }}"
                                           value="{{ $project->name }}" required>
                                </div>
                                @if (in_array($role, ['super-admin','director']))
                                    <div class="form-group clearfix">
                                        <div class="icheck-primary d-inline">
                                            <input type="checkbox" id="management_expenses" name="management_expenses" {{ is_null($project->management_expenses) ?: 'checked' }}>
                                            <label for="management_expenses">УР проект</label>
                                        </div>
                                    </div>
                                @endif
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ __('general.client') }}</label>
                                            <select class="form-control select2" name="client_id"
                                                    id="main_client"
                                                    data-placeholder="{{ __('project.choose_client') }}"
                                                    style="width: 100%;"
                                                    required>
                                                <option></option>
                                                @foreach($clients as $client)
                                                    <option
                                                        value="{{ $client->id }}" {{ $project->client_id == $client->id ? 'selected' : '' }}>{{$client->name}}</option>
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
                                                @if(!is_null($additional_clients))
                                                    @foreach($additional_clients as $add_client)
                                                        <option
                                                            value="{{ $add_client->id }}" selected>{{ $add_client->name }}</option>
                                                    @endforeach
                                                @endif
                                                @foreach($clients as $client)
                                                    <option value="{{ $client->id }}">{{$client->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="from">{{ __('project.send_from') }}</label>
                                    <input type="text" class="form-control to_uppercase" name="from" placeholder="{{ __('project.send_from') }}"
                                           value="{{ $project->from }}">
                                </div>
                                <div class="form-group">
                                    <label for="from">{{ __('project.pogranperehod') }}</label>
                                    <input type="text" class="form-control to_uppercase" name="pogranperehod" placeholder="{{ __('project.pogranperehod') }}"
                                           value="{{ $project->pogranperehod }}">
                                </div>
                                <div class="form-group">
                                    <label for="to">{{ __('project.send_to') }}</label>
                                    <input type="text" class="form-control to_uppercase" name="to" placeholder="{{ __('project.send_to') }}"
                                           value="{{ $project->to }}">
                                </div>
                                <div class="form-group">
                                    <label for="freight_info">{{ __('project.freight_info') }}</label>
                                    <input type="text" class="form-control to_uppercase" name="freight_info"
                                           placeholder="{{ __('project.freight_info') }}" value="{{ $project->freight_info }}">
                                </div>
                                <div class="form-group">
                                    <label for="freight_amount">{{ __('project.freight_amount') }}</label>
                                    <input type="text" class="form-control digits_only" name="freight_amount"
                                           placeholder="{{ __('project.freight_amount') }}" value="{{ $project->freight_amount }}">
                                </div>
                                @if(canWorkWithProject($project->id))
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>{{ __('project.client_manager') }}</label>
                                            <select class="form-control select2" name="manager_id" data-placeholder="{{ __('project.client_manager') }}" style="width: 100%;">
                                                <option></option>
                                                @foreach($users as $manager)
                                                    <option value="{{ $manager->id }}" {{ $project->manager_id == $manager->id ? 'selected' : '' }}>{{$manager->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label>{{ __('project.logist') }}</label>
                                            <select class="form-control select2" name="logist_id" data-placeholder="{{ __('project.logist') }}" style="width: 100%;">
                                                <option></option>
                                                @foreach($users as $logist)
                                                    <option value="{{ $logist->id }}" {{ $project->logist_id == $logist->id ? 'selected' : '' }}>{{$logist->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="prepayment">{{ __('project.prepayment') }}</label>
                                            <input type="text" class="form-control" name="prepayment"
                                                   placeholder="{{ __('project.prepayment_with_currency') }}" value="{{ $project->prepayment }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="planned_payment_date">{{ __('project.planned_payment_date') }}</label>
                                            <input type="text" class="form-control date_input invoice_deadline" name="planned_payment_date"
                                                   placeholder="{{ __('project.planned_payment_date') }}" value="{{ $project->planned_payment_date }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>{{ __('general.additional_info') }}</label>
                                    <textarea class="form-control to_uppercase" rows="3" name="additional_info"
                                              placeholder="{{ __('general.additional_info') }}">{{ $project->additional_info }}</textarea>
                                </div>
                                @if (can_edit_this_project_price($project->id) || (can_edit_this_project($project->id) && ($project->status == 'Черновик')))
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
                                                <input type="text" class="form-control" id="expense_description" placeholder="{{ __('project.description') }}">
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

                                        @if (!is_null($project->expense->expenses_array))
                                            @php
                                                $max_item = max(array_keys(unserialize($project->expense->expenses_array)));
                                            @endphp
                                            <input type="hidden" id="max_expense_i" value="{{ $max_item }}">
                                            @foreach(unserialize($project->expense->expenses_array) as $expense)
                                                @php
                                                    $type = $expense['type'];
                                                    $exp = explode('_',$type);
                                                    $expense_i = $exp[1];
                                                @endphp
                                                <div class="col-md-4" id="{{ $type }}">
                                                    <input type="hidden" name="expenses_array[{{ $expense_i }}][type]" value="{{ $type }}">
                                                    <input type="hidden" name="expenses_array[{{ $expense_i }}][{{ $type }}_name]" value="{{ $expense[$type.'_name'] }}">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h3 class="card-title">{{ $expense[$type.'_name'] }}</h3>
                                                            <div class="card-tools">
                                                                <button type="button" class="btn btn-tool remove_expense_block" data-block-delete="{{ $type }}">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="form-group">
                                                                <label>{{ __('general.currency') }}</label>
                                                                <select class="form-control choose_currency"
                                                                        name="expenses_array[{{ $expense_i }}][{{ $type }}_currency]"
                                                                        data-type="{{ $type }}" data-placeholder="{{ __('general.currency') }}"
                                                                        style="width: 100%;" required>
                                                                    <option value="RUB" data-currency-rate="1"
                                                                        {{ $expense[$type.'_currency'] == 'RUB' ? 'selected' : '' }}>
                                                                        {{ __('general.ruble') }}
                                                                    </option>
                                                                    <option value="USD"
                                                                            data-currency-rate="{{ $rates->USD }}"
                                                                            data-divided="{{ $rates->usd_divided }}"
                                                                            data-ratio="{{ $rates->usd_ratio }}"
                                                                        {{ $expense[$type.'_currency'] == 'USD' ? 'selected' : '' }}>
                                                                        {{ __('general.usd') }}, {{ __('general.cb_rate') }} {{ $rates->USD }}
                                                                    </option>
                                                                    <option value="CNY"
                                                                            data-currency-rate="{{ $rates->CNY }}"
                                                                            data-divided="{{ $rates->cny_divided }}"
                                                                            data-ratio="{{ $rates->cny_ratio }}"
                                                                        {{ $expense[$type.'_currency'] == 'CNY' ? 'selected' : '' }}>
                                                                        {{ __('general.cny') }}, {{ __('general.cb_rate') }} {{ $rates->CNY }}
                                                                    </option>
                                                                </select>
                                                            </div>
                                                            <div class="form-group {{ $expense[$type.'_currency'] == 'RUB' ? 'd-none' : '' }}"
                                                                 id="{{ $type }}_rate_div">
                                                                <label>{{ __('general.cb_rate_corrected') }}</label>
                                                                <input class="form-control rate" type="text"
                                                                       name="expenses_array[{{ $expense_i }}][{{ $type }}_rate]"
                                                                       id="{{ $type }}_rate" placeholder="{{ __('general.cb_rate_corrected') }}"
                                                                       value="{{ $expense[$type.'_rate'] }}">
                                                            </div>
                                                            <div class="form-group">
                                                                <label>{{ __('project.1pc_price') }}</label>
                                                                <input class="form-control digits_only price_1pc" type="text"
                                                                       id="{{ $type }}_price_1pc"
                                                                       name="expenses_array[{{ $expense_i }}][{{ $type }}_price_1pc]"
                                                                       placeholder="{{ __('project.1pc_price') }}"
                                                                       value="{{ $expense[$type.'_price_1pc'] }}">
                                                            </div>
                                                            <div class="form-group">
                                                                <label>{{ __('general.quantity') }}</label>
                                                                <input class="form-control digits_only amount"
                                                                       id="{{ $type }}_amount"
                                                                       data-type="{{ $type }}"
                                                                       type="text"
                                                                       name="expenses_array[{{ $expense_i }}][{{ $type }}_amount]"
                                                                       placeholder="{{ __('general.quantity') }}"
                                                                       value="{{ $expense[$type.'_amount'] }}">
                                                            </div>
                                                            <div class="form-group">
                                                                <label>{{ __('project.project_total_price_in_rub') }}</label>
                                                                <input class="form-control digits_only expenses"
                                                                       id="{{ $type }}_total_price_in_rub"
                                                                       type="text"
                                                                       name="expenses_array[{{ $expense_i }}][{{ $type }}_total_price_in_rub]"
                                                                       placeholder="{{ __('project.project_total_price_in_rub') }}"
                                                                       value="{{ $expense[$type.'_total_price_in_rub'] }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
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
                                                                    <option value="RUB" data-currency-rate="1"
                                                                        {{ $project->expense->currency == 'RUB' ? 'selected' : '' }}>
                                                                        {{ __('general.ruble') }}
                                                                    </option>
                                                                    <option value="USD"
                                                                            data-currency-rate="{{ $rates->USD }}"
                                                                            data-divided="{{ $rates->usd_divided }}"
                                                                            data-ratio="{{ $rates->usd_ratio }}"
                                                                        {{ $project->expense->currency == 'USD' ? 'selected' : '' }}>
                                                                        {{ __('general.usd') }}, {{ __('general.cb_rate') }} {{ $rates->USD }}
                                                                    </option>
                                                                    <option value="CNY"
                                                                            data-currency-rate="{{ $rates->CNY}}"
                                                                            data-divided="{{ $rates->cny_divided }}"
                                                                            data-ratio="{{ $rates->cny_ratio }}"
                                                                        {{ $project->expense->currency == 'CNY' ? 'selected' : '' }}>
                                                                        {{ __('general.cny') }}, {{ __('general.cb_rate') }} {{ $rates->CNY }}
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label>{{ __('general.cb_rate_corrected') }}</label>
                                                                <input class="form-control" type="text" name="cb_rate" id="project_rate"
                                                                       placeholder="{{ __('general.cb_rate_corrected') }}"
                                                                       value="{{ $project->expense->cb_rate }}">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label>{{ __('project.1pc_price') }}</label>
                                                                <input class="form-control digits_only price_1pc" type="text"
                                                                       name="price_1pc" id="project_price_1pc"
                                                                       placeholder="{{ __('project.1pc_price') }}"
                                                                       value="{{ $project->expense->price_1pc }}">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label>{{ __('general.quantity') }}</label>
                                                                <input class="form-control digits_only amount" id="project_amount" data-type="project"
                                                                       type="text" name="amount" placeholder="{{ __('general.quantity') }}"
                                                                       value="{{ $project->expense->amount }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="price">{{ __('project.project_price_in_currency') }}</label>
                                                                <input type="text" class="form-control digits_only"
                                                                       id="project_price_in_currency" name="price_in_currency"
                                                                       placeholder="{{ __('project.project_price_in_currency') }}"
                                                                       value="{{ $project->expense->price_in_currency }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="price">{{ __('project.project_total_price_in_rub') }}</label>
                                                                <input type="text" class="form-control digits_only"
                                                                       id="project_total_price_in_rub" name="price_in_rub"
                                                                       placeholder="{{ __('project.project_total_price_in_rub') }}"
                                                                       value="{{ $project->expense->price_in_rub }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>{{ __('project.planned_income') }}</label>
                                                                <input type="text" class="form-control digits_only"
                                                                       id="project_planned_revenue" placeholder="{{ __('project.planned_income') }}"
                                                                       value="{{ $project->expense->price_in_rub }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>{{ __('project.planned_outcome') }}</label>
                                                                <input type="text" class="form-control digits_only"
                                                                       id="project_planned_costs" name="planned_costs"
                                                                       placeholder="{{ __('project.planned_outcome') }}"
                                                                       value="{{ $project->expense->planned_costs }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>{{ __('project.planned_profit') }}</label>
                                                                <input type="text" class="form-control rate_input"
                                                                       id="project_planned_profit" name="planned_profit"
                                                                       placeholder="{{ __('project.planned_profit') }}"
                                                                       value="{{ $project->expense->planned_profit }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="form-group mt-4">
                                        <a class="btn bg-orange"
                                           data-toggle="modal"
                                           data-target="#create_task_modal"
                                           data-model="project"
                                           data-text="Измените цену на проект"
                                           data-user="Группа Директоры"
                                           data-model-id="{{ $project->id }}">
                                            <i class="fas fa-briefcase"></i>
                                            {{ __('project.ask_change_price') }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('general.update') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
