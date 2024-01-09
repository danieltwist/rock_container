<div class="modal fade" id="sell_currency">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('invoice.sell_currency') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('sell_currency') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="sell_currency_invoice_id" name="invoice_id">
                    <input type="hidden" id="sell_currency_amount">
                    <div class="form-group">
                        <label>{{ __('general.rate') }}</label>
                        <input class="form-control rate_input"
                               id="sell_currency_rate_sale_date"
                               type="text"
                               name="rate_sale_date"
                               placeholder="{{ __('general.rate') }}">
                    </div>
                    <div class="form-group">
                        <label>{{ __('invoice.total_in_rub') }}</label>
                        <input class="form-control"
                               id="sell_currency_amount_sale_date"
                               type="text"
                               name="amount_sale_date"
                               placeholder="{{ __('invoice.total_in_rub') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">{{ __('invoice.sell_currency') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
