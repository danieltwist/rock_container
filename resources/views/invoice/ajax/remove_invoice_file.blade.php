@if(checkUploadedFileInvoice($invoice->id)['file'])
    {{ __('invoice.upload_date') }} - {{ \Carbon\Carbon::parse(unserialize($invoice->file)['date'])->format('d.m.Y H:i:s') }}<br>
    {{ __('invoice.uploaded_by') }} - {{ unserialize($invoice->file)['user'] }}<br>
    <a class="btn-primary btn btn-sm mt-2" href="{{ Storage::url(unserialize($invoice->file)['filename']) }}" download>
        {{ __('invoice.download_invoice') }}
    </a>
@else
    <a class="btn-primary btn btn-sm mt-2" href="{{ Storage::url($invoice->file) }}" download>
        {{ __('invoice.download_invoice') }}
    </a>
@endif
<div class="mt-4"></div>
