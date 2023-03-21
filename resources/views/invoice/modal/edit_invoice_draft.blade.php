<div class="modal fade" id="edit_draft_invoice_modal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('invoice.edit_draft_invoice') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('make_draft_invoice') }}" method="POST">
                @csrf
                <div class="modal-body modal-body-invoice">
                    @livewire('edit-draft-invoice')
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" data-action='{"hide_modal":{"id": "edit_draft_invoice_modal"}}' id="edit_invoice_draft">
                        {{ __('general.update') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
