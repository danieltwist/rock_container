@if($invoice->project)
    @if (!$invoice->file == '')
        @include('invoice.ajax.remove_invoice_file')
    @endif
    @if (is_null($invoice->invoice_file))
        <b>{{ __('invoice.upload_invoice') }}:</b>
        <form action="{{ route('invoice.update', $invoice->id) }}" class="mt-2" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="action" value="upload_invoice_file">
            <div class="form-group">
                <label for="amount">{{ __('general.amount') }}</label>
                <input type="text" class="form-control" name="amount" placeholder="{{ __('general.amount') }}" required>
            </div>
            <div class="form-group">
                <input type="file" class="form-control-file" name="invoice_file">
            </div>
            <button type="submit" class="btn btn-primary btn-sm"
                    data-action='{"update_div":{"div_id":"invoice_file_{{ $invoice->id }}"},"reset_form":{"need_reset": "true"}}'>
                {{ __('general.upload') }}
            </button>
        </form>
    @else
        @foreach($invoice->invoice_file as $key=>$invoice_file)
            <div class="mt-2">
                {{ __('invoice.upload_date') }} - {{ $invoice_file['date'] }}<br>
                {{ __('invoice.uploaded_by') }} - {{ $invoice_file['user'] }}<br>
                @if(array_key_exists('amount', $invoice_file))
                    {{ __('general.amount') }} - {{ $invoice_file['amount'] }}<br>
                @endif
                <a class="btn-primary btn btn-sm mt-2" href="{{ Storage::url($invoice_file['filename']) }}" download>{{ __('invoice.download_invoice') }}</a>
                @can('remove invoices')
                    <a class="btn-danger btn btn-sm ajax_remove cursor-pointer mt-2"
                       data-invoice-id="{{ $invoice->id }}"
                       data-array_key="{{ $key }}"
                       data-action="remove_invoice_file">
                        {{ __('invoice.remove_invoice') }}
                    </a>
                @endcan
            </div>
        @endforeach
        <div class="mt-4">
            <a data-toggle="collapse" href="#uploadInvoiceFile_{{$invoice->id}}" role="button" aria-expanded="false" aria-controls="uploadInvoiceFile_{{$invoice->id}}">
                {{ __('invoice.upload_another_invoice') }}
            </a>
            <div class="collapse" id="uploadInvoiceFile_{{$invoice->id}}">
                <div class="card card-body">
                    <form action="{{ route('invoice.update', $invoice->id) }}" class="mt-2" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="action" value="upload_invoice_file">
                        <div class="form-group">
                            <label for="amount">{{ __('general.amount') }}</label>
                            <input type="text" class="form-control" name="amount" placeholder="{{ __('general.amount') }}" required>
                        </div>
                        <div class="form-group">
                            <input type="file" class="form-control-file" name="invoice_file">
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm"
                                data-action='{"update_div":{"div_id":"invoice_file_{{ $invoice->id }}"},"reset_form":{"need_reset": "true"}}'>
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
