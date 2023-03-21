<div class="modal fade" id="create_draft_invoice_modal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('invoice.create_invoice_draft') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('make_draft_invoice') }}" method="POST">
                @csrf
                <div class="modal-body modal-body-invoice">
                    @livewire('make-draft-invoice')
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" data-action='{"hide_modal":{"id": "create_draft_invoice_modal"}}' id="create_invoice_draft">{{ __('invoice.create_invoice_draft') }}</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
