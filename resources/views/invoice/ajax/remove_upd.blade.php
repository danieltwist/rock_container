@if(checkUploadedFileInvoice($invoice->id)['upd'])
    {{ __('invoice.upload_date') }} - {{ unserialize($invoice->upd)['date'] }}<br>
    {{ __('invoice.uploaded_by') }} - {{ unserialize($invoice->upd)['user'] }}<br>
    <a class="btn-primary btn btn-sm mt-2" href="{{ Storage::url(unserialize($invoice->upd)['filename']) }}" download>
        {{ __('invoice.download_upd') }}
    </a>
@else
    <a class="btn-primary btn btn-sm" href="{{ Storage::url($invoice->upd) }}" download>
        {{ __('invoice.download_upd') }}
    </a>
@endif
<div class="mt-4"></div>
