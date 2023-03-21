<form action="{{ route('invoice.update', $invoice->id) }}" method="POST">
    @csrf
    @method('PUT')
    <input type="hidden" name="action" value="update_invoice">
    <div class="form-group">
        <label>{{ $company_type_name }}</label>
        <select class="form-control select2" name="{{ $company_type_id }}"
                data-placeholder="{{ __('general.choose_from_list') }}"
                style="width: 100%;"
                required>
            <option></option>
            @foreach($company_list as $company)
                <option
                    value="{{ $company->id }}" {{ $invoice->$company_type_id == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
            @endforeach
        </select>
    </div>
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

    @if(in_array($invoice->status, ['Оплачен', 'Частично оплачен','Ожидается создание инвойса']))
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
            <option value="Ожидается оплата" {{ $invoice->status == 'Ожидается оплата' ? 'selected' : '' }}>{{ __('invoice.status_waiting_for_payment') }}</option>
            <option value="Счет на согласовании" {{ $invoice->status == 'Счет на согласовании' ? 'selected' : '' }}>{{ __('invoice.status_agree') }}</option>
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
               value="{{ $invoice->deadline }}">
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
