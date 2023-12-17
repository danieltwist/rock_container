@if ($invoice->direction == 'Доход')
    @if ($invoice->currency != 'RUB')
        @if ($invoice->amount_in_currency_income_date != '')
            {{ number_format($invoice->amount_in_currency_income_date, 0, '.', ' ') }} {{ $invoice->currency }} ({{ $average_exchange_rate }})
            <br>
            {{ number_format($invoice->amount_income_date, 2, '.', ' ') }}р.
            @if ($invoice->amount_in_currency > $invoice->amount_in_currency_income_date)
                <br>
                <small>
                    <b>
                        {{ __('invoice.difference') }}: {{ number_format($invoice->amount_in_currency - $invoice->amount_in_currency_income_date, 2, '.', ' ') }}
                        {{ $invoice->currency }}
                    </b>
                </small>
            @endif
        @endif
    @else
        @if ($invoice->amount_income_date != '')
            {{ number_format($invoice->amount_income_date, 2, '.', ' ') }}р.
        @endif
    @endif
@else
    @if ($invoice->currency != 'RUB')
        @if ($invoice->amount_in_currency_income_date != '')
            {{ number_format($invoice->amount_in_currency_income_date, 0, '.', ' ') }} {{ $invoice->currency }} ({{ $average_exchange_rate }})
            <br>
            {{ number_format($invoice->amount_income_date, 2, '.', ' ') }}р.
        @endif
    @else
        @if ($invoice->amount_income_date != '')
            {{ number_format($invoice->amount_income_date, 2, '.', ' ') }}р.
        @endif
    @endif
@endif
@if (!is_null($invoice->payments_history))
    <br>
    <a class="cursor-pointer text-sm text-dark"
       data-toggle="collapse"
       data-target="#collapse_invoice_paid_info_{{ $invoice->id }}"
       aria-expanded="false"
       aria-controls="collapseExample">
        <i class="fa fa-angle-down"></i>
        {{ __('general.info') }}
    </a>
    <div class="collapse mt-2" id="collapse_invoice_paid_info_{{ $invoice->id }}">
        <small>
            @foreach($invoice->payments_history as $payment)
                {{ $payment['date'] }} - {{ $payment['user'] }} <br>
                @if($payment['currency'] == 'RUB')
                    {{ __('invoice.payment_amount') }}: {{ $payment['amount_rub'] }}р.
                @else
                    {{ __('invoice.payment_amount') }}: {{ $payment['amount_currency'] }}{{ $payment['currency'] }} ({{ $payment['amount_rub'] }}р.)
                @endif
                <br>
            @endforeach
        </small>
    </div>
@endif

