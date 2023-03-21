@if($invoice->project)
    <b>{{ __('invoice.upload_upd') }}:</b>
    <input type="file" class="form-control-file ajax_upload_file" name="upd"
           data-invoice-id="{{ $invoice->id }}"
           data-action="upload_upd">
@else
    {{ __('general.project_delete') }}
@endif
