@if($invoice->project)
    @if (!is_null($invoice->upd))
        @include('invoice.ajax.remove_upd')
    @endif
    @if (is_null($invoice->upd_file))
        <b>{{ __('invoice.upload_upd') }}:</b>
        <form action="{{ route('invoice.update', $invoice->id) }}" class="mt-2" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="action" value="upload_upd_file">
            <div class="form-group">
                <label for="amount">{{ __('general.amount') }}</label>
                <input type="text" class="form-control" name="amount" placeholder="{{ __('general.amount') }}" required>
            </div>
            <div class="form-group">
                <input type="file" class="form-control-file" name="upd_file">
            </div>
            <button type="submit" class="btn btn-primary btn-sm"
                    data-action='{"update_div":{"div_id":"upd_file_{{$invoice->id}}"},"reset_form":{"need_reset": "true"}}'>
                {{ __('general.upload') }}
            </button>
        </form>
    @else
        @foreach($invoice->upd_file as $key=>$upd_file)
            <div class="mt-2">
                {{ __('invoice.upload_date') }} - {{ \Carbon\Carbon::parse($upd_file['date'])->format('d.m.Y H:i:s') }}<br>
                {{ __('invoice.uploaded_by') }} - {{ $upd_file['user'] }}<br>
                @if(array_key_exists('amount', $upd_file))
                    {{ __('general.amount') }} - {{ $upd_file['amount'] }}<br>
                @endif
                <a class="btn-primary btn btn-sm mt-2" href="{{ Storage::url($upd_file['filename']) }}" download>{{ __('invoice.download_upd') }}</a>
                @can('remove invoices')
                    <a class="btn-danger btn btn-sm ajax_remove cursor-pointer mt-2"
                       data-invoice-id="{{ $invoice->id }}"
                       data-array_key="{{ $key }}"
                       data-action="remove_upd_file">
                        {{ __('invoice.remove_upd') }}
                    </a>
                @endcan
            </div>
        @endforeach
        <div class="mt-4">
            <a data-toggle="collapse" href="#uploadUPDFile_{{$invoice->id}}" role="button" aria-expanded="false" aria-controls="uploadUPDFile_{{$invoice->id}}">
                {{ __('invoice.upload_another_upd') }}
            </a>
            <div class="collapse" id="uploadUPDFile_{{$invoice->id}}">
                <div class="card card-body">
                    <form action="{{ route('invoice.update', $invoice->id) }}" class="mt-2" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="action" value="upload_upd_file">
                        <div class="form-group">
                            <label for="amount">{{ __('general.amount') }}</label>
                            <input type="text" class="form-control" name="amount" placeholder="{{ __('general.amount') }}" required>
                        </div>
                        <div class="form-group">
                            <input type="file" class="form-control-file" name="upd_file">
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm"
                                data-action='{"update_div":{"div_id":"upd_file_{{$invoice->id}}"},"reset_form":{"need_reset": "true"}}'>
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
