@if(!is_null($invoice->payments_history))
    @foreach($invoice->payments_history as $key => $payment)
        <b>Платеж от {{ $payment['date'] }}, {{ $payment['user'] }}</b>
        <div class="mt-2 mb-4">
            Сумма оплаты в рублях: <a href="#" class="xedit" data-pk="{{ $key }}" data-name="amount_rub"
                                      data-invoice_id="{{ $invoice->id }}"
                                      data-model="InvoicePaymentHistory">
                {{ $payment['amount_rub'] }} </a>
            <br>
            Сумма оплаты в валюте: <a href="#" class="xedit" data-pk="{{ $key }}" data-name="amount_currency"
                                      data-invoice_id="{{ $invoice->id }}"
                                      data-model="InvoicePaymentHistory">
                {{ $payment['amount_currency'] }} </a>
        </div>
    @endforeach
@endif
