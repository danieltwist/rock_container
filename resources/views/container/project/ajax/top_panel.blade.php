<div class="col-md-3 col-6">
    <div class="small-box bg-gradient-primary">
        <div class="inner">
            <h4>{{ $info['on_the_way'] }}</h4>
            <p>{{ __('container.project_days_on_the_way') }}</p>
        </div>
    </div>
</div>
<div class="col-md-3 col-6">
    <div class="small-box bg-gradient-navy">
        <div class="inner">
            <h4>{{ number_format($info['income_expected'], 0, '.', ' ') }}р.</h4>
            <p>{{ __('container.project_income_expected') }}</p>
        </div>
    </div>
</div>
<div class="col-md-3 col-6">
    <div class="small-box bg-gradient-indigo">
        <div class="inner">
            <h4>{{ number_format($info['paid'], 0, '.', ' ') }}р.</h4>
            <p>{{ __('container.project_paid_by_client') }}</p>
        </div>
    </div>
</div>
<div class="col-md-3 col-6">
    <div class="small-box bg-gradient-success">
        <div class="inner">
            <h4>{{ number_format($info['profit'], 0, '.', ' ') }}р.</h4>
            <p>{{ __('container.project_profit') }}</p>
        </div>
    </div>
</div>
