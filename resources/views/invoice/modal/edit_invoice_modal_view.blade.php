<form action="{{ route('invoice.update', $invoice->id) }}" method="POST">
    @csrf
    @method('PUT')
    <input type="hidden" name="action" value="update_invoice">
        <div class="form-group">
            <label for="direction">{{ __('general.direction') }}</label>
            <select class="form-control finance_direction" name="direction" required>
                <option value="Поставщику" {{ $company_type == 'supplier' ? "selected" : "" }}>
                    {{ __('general.supplier') }}
                </option>
                <option value="Клиенту" {{ $company_type == 'client' ? "selected" : "" }}>
                    {{ __('general.client') }}
                </option>
            </select>
        </div>
        <div class="form-group {{ $company_type == 'supplier' ? 'd-none' : '' }} client_group">
            <label for="client_id">{{ __('general.client') }}</label>
            <select class="form-control select2 client_select" name="client_id"
                    data-placeholder="{{ __('project.select_client') }}" style="width: 100%;">
                <option></option>
                @foreach($clients as $client)
                    <option value="{{ $client->id }}" {{ $client_id == $client->id ? "selected" : "" }}>
                        {{$client->name}}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group {{ $company_type == 'client' ? 'd-none' : '' }} supplier_group">
            <label for="supplier_id">{{ __('general.supplier') }}</label>
            <select class="form-control select2 supplier_select" name="supplier_id"
                    data-placeholder="{{ __('project.select_supplier') }}" style="width: 100%;">
                <option></option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}"  {{ $supplier_id == $supplier->id ? "selected" : "" }}>
                        {{$supplier->name}}
                    </option>
                @endforeach
            </select>
        </div>
        <label for="project">Проект</label>
        <select class="form-control select2" name="project_id"
                data-placeholder="Выберите проект" style="width: 100%;">
            <option></option>
            @foreach($projects as $project)
                <option value="{{ $project->id }}"
                        @if($invoice->project_id == $project->id)
                        selected
                    @endif
                >{{ $project->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="application">Заявка</label>
        <select class="form-control select2" name="application_id"
                data-placeholder="Выберите заявку" style="width: 100%;">
            <option></option>
            @foreach($applications as $application)
                <option value="{{ $application->id }}"
                        @if($invoice->application_id == $application->id)
                        selected
                    @endif
                >№{{ $application->id }} - {{ $application->name }} от {{ $application->created_at }}</option>
            @endforeach
        </select>
    </div>
    @if($invoice->direction == 'Расход')
        <div class="form-group">
            <label for="expense_category">Вид расходов</label>
            <select class="form-control select2" name="expense_category" id="edit_expense_category"
                    data-placeholder="Выберите вид расходов" style="width: 100%;">
                <option></option>
                @foreach($expense_types as $expense_type)
                    @if($expense_type->type == 'category')
                        <option value="{{ $expense_type->name }}"
                        @if($invoice->expense_category == $expense_type->name)
                            selected
                        @endif
                        >{{ $expense_type->name }}</option>
                    @endif
                @endforeach
            </select>
        </div>
        <div class="expense_type_div">
            <div class="form-group">
                <label for="expense_type">Тип расходов</label>
                <select class="form-control select2" name="expense_type"
                        data-placeholder="Выберите тип расходов" style="width: 100%;">
                    <option></option>
                    @foreach($expense_types as $expense_type)
                        @if($expense_type->type == 'type')
                            <option value="{{ $expense_type->name }}"
                                @if($invoice->expense_type == $expense_type->name)
                                    selected
                                @endif
                            >{{ $expense_type->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div>
    @endif
    <div class="form-group">
        <label>{{ __('general.currency') }}</label>
        <select class="form-control select2" name="currency"
                id="edit_invoice_currency"
                data-placeholder="{{ __('general.choose_from_list') }}"
                style="width: 100%;"
                required>
            <option></option>
            <option value="RUB" {{ $invoice->currency == 'RUB' ? 'selected' : '' }}>{{ __('general.ruble') }}</option>
            <option value="USD" {{ $invoice->currency == 'USD' ? 'selected' : '' }}>{{ __('general.usd') }}</option>
            <option value="CNY" {{ $invoice->currency == 'CNY' ? 'selected' : '' }}>{{ __('general.cny') }}</option>

        </select>
    </div>
    <div class="row currency-dnone {{ $class }}">
        <div class="col-md-6">
            <div class="form-group">
                <label>{{ __('invoice.invoice_amount_in_currency') }}</label>
                <input type="text" class="form-control rate_input need_calculate"
                       id="edit_invoice_amount_out_date" name="amount_in_currency" placeholder="{{ __('invoice.invoice_amount_in_currency') }}"
                       value="{{ $invoice->amount_in_currency }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>{{ __('invoice.corrected_rate_day_out') }}</label>
                <input type="text" class="form-control rate_input need_calculate"
                       id="rate_out_date" name="rate_out_date" placeholder="{{ __('invoice.corrected_rate_day_out') }}"
                       value="{{ $invoice->rate_out_date }}">
            </div>
        </div>
    </div>
    <div class="form-group">
        <label>{{ __('invoice.invoice_amount_in_rub') }}</label>
        <input type="text" class="form-control rate_input" name="amount"
               id="edit_invoice_amount_out_date_in_rubles" placeholder="{{ __('invoice.invoice_amount_in_rub') }}"
               value="{{ $invoice->amount }}">
    </div>
    @if($invoice->direction == 'Расход')
        <div class="form-group currency-dnone {{ $class }}">
            <label>{{ __('invoice.amount_in_currency_actual') }}</label>
            <input type="text" class="form-control rate_input"
                   id="edit_invoice_amount_actual"
                   name="amount_in_currency_actual"
                   placeholder="{{ __('invoice.amount_in_currency_actual') }}"
                   value="{{ $invoice->amount_in_currency_actual }}">
        </div>
        <div class="form-group">
            <label>{{ __('invoice.amount_actual_in_rubles') }}</label>
            <input type="text" class="form-control rate_input"
                   id="edit_invoice_amount_actual_in_rubles"
                   name="amount_actual"
                   placeholder="{{ __('invoice.amount_actual_in_rubles') }}"
                   value="{{ $invoice->amount_actual }}">
        </div>
    @endif
    <div class="row currency-dnone {{ $class }}">
        <div class="col-md-6">
            <div class="form-group currency-dnone {{ $class }}">
                <label>{{ __('invoice.amount_in_currency_income_date') }}</label>
                <input type="text" class="form-control rate_input"
                       id="edit_invoice_amount_income_date"
                       name="amount_in_currency_income_date"
                       placeholder="{{ __('invoice.amount_in_currency_income_date') }}"
                       value="{{ $invoice->amount_in_currency_income_date }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>{{ __('invoice.rate_income_date') }}</label>
                <input type="text" class="form-control rate_input need_calculate"
                       id="rate_income_date"
                       name="rate_income_date"
                       placeholder="{{ __('invoice.rate_income_date') }}"
                       value="{{ $invoice->rate_income_date }}">
            </div>
        </div>
    </div>
        <div class="form-group">
            <label>{{ __('invoice.amount_income_date') }}</label>
            <input type="text" class="form-control rate_input"
                   id="edit_invoice_amount_income_date_in_rubles"
                   name="amount_income_date"
                   placeholder="{{ __('invoice.amount_income_date') }}"
                   value="{{ $invoice->amount_income_date }}">
        </div>

    @if ($invoice->currency != 'RUB' && $invoice->amount_sale_date != '')
        <div class="form-group">
            <label>{{ __('invoice.amount_sale_date') }}</label>
            <input type="text" class="form-control rate_input"
                   name="amount_sale_date"
                   placeholder="{{ __('invoice.amount_sale_date') }}"
                   value="{{ $invoice->amount_sale_date }}">
        </div>
        <div class="form-group">
            <label>{{ __('invoice.rate_sale_date') }}</label>
            <input type="text" class="form-control rate_input"
                   name="rate_sale_date"
                   placeholder="{{ __('invoice.rate_sale_date') }}"
                   value="{{ $invoice->rate_sale_date }}">
        </div>
    @endif

    @if(in_array($invoice->status, ['Оплачен', 'Частично оплачен','Ожидается создание инвойса','Взаимозачет']))
    <div class="form-group">
        <label>Статус</label>
        <select class="form-control select2" name="status"
              data-placeholder="{{ __('general.choose_from_list') }}"
              style="width: 100%;"
              required>
            <option></option>
            <option value="Ожидается создание инвойса" {{ $invoice->status == 'Ожидается создание инвойса' ? 'selected' : '' }}>{{ __('invoice.status_waiting_for_create_invoice') }}</option>
            <option value="Частично оплачен" {{ $invoice->status == 'Частично оплачен' ? 'selected' : '' }}>{{ __('invoice.status_part_paid') }}</option>
            <option value="Оплачен" {{ $invoice->status == 'Оплачен' ? 'selected' : '' }}>{{ __('invoice.status_paid') }}</option>
            <option value="Ожидается оплата">{{ __('invoice.status_waiting_for_payment') }}</option>
            <option value="Счет на согласовании">{{ __('invoice.status_agree') }}</option>
            <option value="Взаимозачет" {{ $invoice->status == 'Взаимозачет' ? 'selected' : '' }}>{{ __('invoice.sub_status_compensation') }}</option>
        </select>
    </div>
    @else
        <input type="hidden" name="status" value="{{ $invoice->status }}">
    @endif
    <div class="form-group">
        <label for="deadline">{{ __('invoice.payment_deadline') }}</label>
        <input type="text" class="form-control date_input invoice_deadline"
               name="deadline"
               placeholder="{{ __('invoice.payment_deadline') }}"
               value="{{ !is_null($invoice->deadline) ? $invoice->deadline->format('d.m.Y') : '' }}">
    </div>
    <div class="form-group">
        <label>{{ __('general.additional_info') }}</label>
        <textarea class="form-control" rows="3" name="additional_info"
                  placeholder="{{ __('general.additional_info') }}">{{ $invoice->additional_info }}</textarea>
    </div>
    <div class="form-group">
        <button type="submit" class="btn btn-primary"
                id="update_invoice_form"
                data-action='{"hide_modal":{"id": "edit_invoice_modal"}}'>
            {{ __('general.update') }}
        </button>
    </div>
</form>
