@if($invoice->project)
    <b>{{ __('invoice.upload_invoice') }}:</b>
    <input type="file" class="form-control-file ajax_upload_file" name="invoice"
           data-invoice-id="{{ $invoice->id }}"
           data-action="upload_invoice_file"
           data-type="ajax">
@else
    {{ __('general.project_delete') }}
@endif
