@if($invoice->project)
    @if (!is_null($invoice->payment_order))
        @include('invoice.ajax.payment_order')
    @endif
    @if (is_null($invoice->payment_order_file))
        <b>{{ __('invoice.upload_payment_order') }}:</b>
        <form action="{{ route('invoice.update', $invoice->id) }}" class="mt-2" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="action" value="upload_payment_order_file">
            <div class="form-group">
                <label for="amount">{{ __('general.amount') }}</label>
                <input type="text" class="form-control" name="amount" placeholder="{{ __('general.amount') }}" required>
            </div>
            <div class="form-group">
                <input type="file" class="form-control-file" name="payment_order">
            </div>
            <button type="submit" class="btn btn-primary btn-sm"
                    data-action='{"update_div":{"div_id":"payment_orders"},"reset_form":{"need_reset": "true"}}'>
                {{ __('general.upload') }}
            </button>
        </form>
    @else
        @foreach($invoice->payment_order_file as $key=>$payment_order)
            <div class="mt-2">
                {{ __('invoice.upload_date') }} - {{ $payment_order['date'] }}<br>
                {{ __('invoice.uploaded_by') }} - {{ $payment_order['user'] }}<br>
                @if(array_key_exists('amount', $payment_order))
                    {{ __('general.amount') }} - {{ $payment_order['amount'] }}<br>
                @endif
                <a class="btn-primary btn btn-sm mt-2" href="{{ Storage::url($payment_order['filename']) }}" download>
                    {{ __('invoice.download_payment_order') }}
                </a>
                @can('remove invoices')
                    <a class="btn-danger btn btn-sm ajax_remove cursor-pointer mt-2"
                       data-invoice-id="{{ $invoice->id }}"
                       data-array_key="{{ $key }}"
                       data-action="remove_payment_order_file">
                        {{ __('invoice.remove_payment_order') }}
                    </a>
                @endcan
            </div>
        @endforeach
        <div class="mt-4">
            <a data-toggle="collapse" href="#uploadPaymentOrder_{{$invoice->id}}" role="button" aria-expanded="false" aria-controls="uploadPaymentOrder_{{$invoice->id}}">
                {{ __('invoice.upload_another_payment_order') }}
            </a>
            <div class="collapse" id="uploadPaymentOrder_{{$invoice->id}}">
                <div class="card card-body">
                    <form action="{{ route('invoice.update', $invoice->id) }}" class="mt-2" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="action" value="upload_payment_order_file">
                        <div class="form-group">
                            <label for="amount">{{ __('general.amount') }}</label>
                            <input type="text" class="form-control" name="amount" placeholder="{{ __('general.amount') }}" required>
                        </div>
                        <div class="form-group">
                            <input type="file" class="form-control-file" name="payment_order">
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm"
                                data-action='{"update_div":{"div_id":"payment_orders"},"reset_form":{"need_reset": "true"}}'>
                            {{ __('general.upload') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif
@else
    {{ __('general.project_delete') }}
@endif
