<div>
    @if (!is_null($invoice))
        @if(!is_null($project))
            <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
            @if ($contracts->isNotEmpty())
                <div class="form-group">
                    <label>{{ __('invoice.choose_contract') }}</label>
                    <select class="form-control" name="contract" required>
                        @if (is_null($contract_id))<option value="">{{ __('invoice.contract_not_chosen') }}</option> @endif
                        @foreach($contracts as $contract)
                            <option value="{{ $contract->id }}" {{ $contract_id ==  $contract->id ? 'selected' : '' }}>
                                {{ $contract->name }} {{ __('general.from') }} {{ $contract->date_start }}, {{ __('invoice.valid_before') }} {{ $contract->date_period }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @else
                {{ __('invoice.no_contracts_for_this_client') }}<br><br>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="cont_num">{{ __('invoice.contract_number') }}</label>
                            <input type="text" class="form-control" name="cont_num" placeholder="{{ __('invoice.contract_number') }}" value="{{ $cont_num }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="cont_num">{{ __('invoice.contract_date') }}</label>
                            <input type="text" class="form-control date_input" name="cont_date" placeholder="{{ __('invoice.contract_date') }}" value="{{ $cont_date }}" required>
                        </div>
                    </div>
                </div>
            @endif
            <div class="form-group">
                <label>{{ __('invoice.company_name') }}</label>
                <input type="text" class="form-control" name="client_company_name" placeholder="{{ __('invoice.company_name') }}" value="{{ $client_company_name }}" required>
            </div>
            <div class="form-group">
                <label>{{ __('general.requisites') }}</label>
                <textarea class="form-control" rows="6" name="client_company_requisites" placeholder="{{ __('general.requisites') }}">{{ $client_company_requisites }}</textarea>
            </div>
            <div class="form-group">
                <label>{{ __('invoice.invoice_services') }}</label>
                <textarea class="form-control" rows="4" name="invoice_services" placeholder="{{ __('invoice.invoice_services') }}">{{ $invoice_services }}</textarea>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="currency">{{ __('general.currency') }}</label>
                        <input type="text" class="form-control" name="currency" placeholder="{{ __('general.currency') }}" value="{{ $currency }}" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="price_1pc">{{ __('invoice.price_1pc') }}</label>
                        <input type="text" class="form-control rate_input" name="price_1pc" placeholder="{{ __('invoice.price_1pc') }}" value="{{ $price_1pc }}" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="freight_amount">{{ __('invoice.freight_amount') }}</label>
                        <input type="text" class="form-control rate_input" name="freight_amount" placeholder="{{ __('invoice.freight_amount') }}" value="{{ $freight_amount }}" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="price_in_currency">{{ __('invoice.total') }}</label>
                        <input type="text" class="form-control" name="price_in_currency" placeholder="{{ __('invoice.total') }}" value="{{ $price_in_currency }}" required>
                    </div>
                </div>
            </div>
        @else
            {{ __('general.project_was_delete') }}
        @endif
    @endif
</div>
