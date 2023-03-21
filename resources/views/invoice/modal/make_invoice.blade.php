<div class="modal fade" id="create_invoice_modal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('invoice.create_invoice') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('generate_invoice') }}" method="POST">
                @csrf
                <div class="modal-body modal-body-invoice">
                    @livewire('make-invoice')
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" data-action='{"hide_modal":{"id": "create_invoice_modal"}}' id="create_invoice">
                        {{ __('invoice.create_invoice') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
