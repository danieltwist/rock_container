@if(checkUploadedFileInvoice($invoice->id)['payment_order'])
    @foreach(unserialize($invoice->payment_order) as $key=>$payment_order)
        <div class="mt-2">{{ __('invoice.upload_date') }} - {{ \Carbon\Carbon::parse($payment_order['date'])->format('d.m.Y H:i:s') }}<br>
            {{ __('invoice.uploaded_by') }} - {{ $payment_order['user'] }}<br>
            @if(array_key_exists('amount', $payment_order))
                {{ __('general.amount') }} - {{ $payment_order['amount'] }}<br>
            @endif
            <a class="btn-primary btn btn-sm mt-2" href="{{ Storage::url($payment_order['filename']) }}" download>
                {{ __('invoice.download_payment_order') }}
            </a>
        </div>
    @endforeach
@else
    <b><a class="btn-primary btn btn-sm" href="{{ Storage::url($invoice->payment_order) }}" download>
            {{ __('invoice.download_payment_order') }}
        </a>
    </b>
@endif
<div class="mt-4"></div>
