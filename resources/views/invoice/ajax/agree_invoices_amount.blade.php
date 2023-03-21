<div class="row">
    <div class="col-md-3 col-6">
        <div class="small-box bg-gradient-primary">
            <div class="inner">
                <h4>{{ $invoices_count }}</h4>
                <p>{{ __('invoice.agree_invoices_count') }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="small-box bg-gradient-navy">
            <div class="inner">
                <h4>{{ number_format($amount_rub, 0, '.', ' ') }}</h4>
                <p>{{ __('invoice.amount_in_rubles') }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="small-box bg-gradient-indigo">
            <div class="inner">
                <h4>{{ number_format($amount_usd, 0, '.', ' ') }}</h4>
                <p>{{ __('invoice.amount_in_usd') }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="small-box bg-gradient-success">
            <div class="inner">
                <h4>{{ number_format($amount_cny, 0, '.', ' ') }}</h4>
                <p>{{ __('invoice.amount_in_cny') }}</p>
            </div>
        </div>
    </div>
</div>
