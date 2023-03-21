@if ($invoice['supplier_id']=='')
    @if(!is_null($invoice->client))
        <a href="{{ route('client.show', $invoice->client_id) }}">{{ $invoice->client['name'] }}</a>
    @else
        {{ __('invoice.counterparty_removed') }}
    @endif
@endif
@if ($invoice['client_id']=='')
    @if(!is_null($invoice->supplier))
        <a href="{{ route('supplier.show', $invoice->supplier_id) }}">{{ $invoice->supplier['name'] }}</a>
    @else
        {{ __('invoice.counterparty_removed') }}
    @endif
    <br>
    <small>
        {{ !is_null($invoice->block) ? $invoice->block['name'] : '' }}
    </small>
@endif
<small>
    <br>
    {{ !is_null($invoice->deadline) ? __('invoice.pay_before').' '.$invoice->deadline : '' }}
</small>
