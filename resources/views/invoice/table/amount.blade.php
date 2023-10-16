@if ($invoice->direction == 'Доход')
    @if ($invoice->currency != 'RUB')
        {{ number_format($invoice->amount_in_currency, 0, '.', ' ') }} {{ $invoice->currency }} ({{ $invoice->rate_out_date }}) <br>
    @endif
    {{ number_format($invoice->amount, 2, '.', ' ') }}р.
    @if ($invoice->currency != 'RUB')
        @if ($invoice->amount_sale_date != '' || $invoice->amount_income_date != '')
            @if ($invoice->amount != $invoice->amount_sale_date || $invoice->amount != $invoice->amount_income_date)
                <br>
                <small>
                    <b>{{ __('invoice.exchange_difference') }}:
                        @if($invoice->amount_sale_date != '')
                            {{ number_format($invoice->amount_sale_date - $invoice->amount, 2, '.', ' ') }}р.<br>
                            ({{ number_format($invoice->amount_sale_date, 2, '.', ' ') }}р. {{ __('invoice.on_rate') }} {{ $invoice->rate_sale_date }})
                        @else
                            {{ number_format($invoice->amount_income_date - $invoice->amount, 2, '.', ' ') }}р.</b><br>
                    <a class="sell_currency_modal cursor-pointer"
                       data-toggle="modal"
                       data-target="#sell_currency"
                       data-invoice-id="{{ $invoice->id }}"
                       data-currency-amount="{{ $invoice->amount_in_currency_income_date }}">({{ __('invoice.currency_not_sold') }})
                    </a>
                    @endif
                </small>
            @endif
        @endif
    @endif
@else
    @if ($invoice->currency != 'RUB')
        {{ number_format($invoice->amount_in_currency, 0, '.', ' ') }} {{ $invoice->currency }} ({{ $invoice->rate_out_date }}) <br>
    @endif
    {{ number_format($invoice->amount, 2, '.', ' ') }}р.
    @if ($invoice->amount != $invoice->amount_actual && $invoice->amount_actual != '')
        <br>{{ __('invoice.fact_amount') }}:<br>
        @if ($invoice->currency != 'RUB')
            {{ number_format($invoice->amount_in_currency_actual, 0, '.', ' ') }} {{ $invoice->currency }} ({{ $invoice->rate_out_date }}) <br>
        @endif
        {{ number_format($invoice->amount_actual, 2, '.', ' ') }}р.
    @endif
    @if ($invoice->currency != 'RUB')
        @if ($invoice->amount_income_date != '')
            @if ($invoice->amount_actual != $invoice->amount_income_date)
                <br>
                <small>
                    <b>{{ __('invoice.exchange_difference') }}:
                        {{ number_format($invoice->amount_income_date - $invoice->amount_actual, 2, '.', ' ') }}р.
                    </b>
                </small>
            @endif
        @endif
    @endif
@endif
@if($invoice->status == 'Частично оплачен')
    <br>
    <small>
        Остаток:
        @if($invoice->currency == 'RUB')
            {{ number_format($invoice->amount - $invoice->amount_income_date, 2, '.', ' ') }}р.
        @else
            {{ number_format($invoice->amount_in_currency - $invoice->amount_in_currency_income_date, 2, '.', ' ') }}{{ $invoice->currency }}
        @endif
    </small>
@endif
