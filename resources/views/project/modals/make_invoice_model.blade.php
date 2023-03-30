<div class="modal fade" id="make_invoice">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('project.add_invoice_to_project') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('invoice.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    @if(isset($project))
                        <input type="hidden" name="project_id" value="{{ $project->id }}">
                    @endif
                    @if(isset($application))
                        <input type="hidden" name="application_id" value="{{ $application->id }}">
                    @endif
                    <input type="hidden" name="action" value="create_new_finance">
                    <div class="form-group">
                        <label for="type">{{ __('general.type') }}</label>
                        <select class="form-control" name="type" required id="invoice_type">
                            <option value="Расход">{{ __('general.outcome') }}</option>
                            <option value="Доход">{{ __('general.income') }}</option>
                        </select>
                    </div>
                    <div id="expense_types_categories">
                        <div class="form-group">
                            <label for="expense_category">Вид расходов</label>
                            <select class="form-control select2" name="expense_category" id="expense_category"
                                    data-placeholder="Выберите вид расходов" style="width: 100%;">
                                <option></option>
                                @foreach($expense_types as $expense_type)
                                    @if($expense_type->type == 'category')
                                        <option value="{{ $expense_type->name }}">{{ $expense_type->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="expense_type_div"></div>
                    </div>
                    <div class="form-group">
                        <label for="direction">{{ __('general.direction') }}</label>
                        <select class="form-control" name="direction" id="finance_direction" required>
                            <option value="Поставщику">{{ __('general.supplier') }}</option>
                            <option value="Клиенту">{{ __('general.client') }}</option>
                        </select>
                    </div>
                    <div class="form-group d-none" id="client_group">
                        <label for="client_id">{{ __('general.client') }}</label>
                        <select class="form-control select2" name="client_id" id="client_select"
                                data-placeholder="{{ __('project.select_client') }}" style="width: 100%;">
                            <option></option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{$client->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" id="supplier_group">
                        <label for="supplier_id">{{ __('general.supplier') }}</label>
                        <select class="form-control select2" required name="supplier_id" id="supplier_select"
                                data-placeholder="{{ __('project.select_supplier') }}" style="width: 100%;">
                            <option></option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{$supplier->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    @if(!isset($project))
                        <div class="form-group">
                            <label for="project_id">Проект</label>
                            <select class="form-control select2" name="project_id"
                                    data-placeholder="Выберите проект" style="width: 100%;" required>
                                <option></option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="form-group">
                        <label>{{ __('general.currency') }}</label>
                        <select class="form-control" name="currency" id="invoice_currency"
                                data-placeholder="Выбери валюту" style="width: 100%;" required>
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
                    <div class="form-group d-none" id="invoice_rate_div">
                        <label>{{ __('general.cb_rate_minus') }} <span id="ratio"></span></label>
                        <input class="form-control" type="text" name="rate_out_date" id="invoice_rate"
                               placeholder="{{ __('general.cb_rate_corrected') }}" value="1">
                    </div>
                    <div class="form-group d-none" id="invoice_amount_in_currency_div">
                        <label>{{ __('general.price_in_currency') }}</label>
                        <input class="form-control rate_input" type="text" id="invoice_amount_in_currency"
                               name="amount_in_currency" placeholder="{{ __('general.price_in_currency') }}" value="0">
                    </div>
                    <div class="form-group">
                        <label>{{ __('general.price_in_rubles') }}</label>
                        <input class="form-control rate_input" id="invoice_total_price_in_rub" type="text" name="amount"
                               placeholder="{{ __('general.price_in_rubles') }}" required>
                    </div>
                    <div class="form-group">
                        <label>{{ __('project.payment_deadline') }}</label>
                        <input type="text" class="form-control date_input invoice_deadline" name="deadline"
                               placeholder="{{ __('project.payment_deadline') }}">
                    </div>
                    <div class="form-group">
                        <label>{{ __('general.additional_info') }}</label>
                        <textarea class="form-control" rows="3" name="additional_info" id="add_invoice_additional_info"
                                  placeholder="{{ __('general.additional_info') }}"></textarea>
                    </div>
                    <div class="form-group clearfix" id="supplier_group_agree_without_invoice">
                        <div class="icheck-primary d-inline">
                            <input type="checkbox" id="agree_without_invoice" name="agree_without_invoice">
                            <label for="agree_without_invoice">{{ __('project.send_to_agree_without_invoice') }}</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary float-right"
                            data-action='{"hide_modal":{"id": "make_invoice"},"update_table":{"table_id": "invoices_ajax_table_content_project"},"update_second_table":{"table_id": "invoices_ajax_table_content_application"},"reset_form":{"need_reset": "true"}}'>
                        {{ __('general.create') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
