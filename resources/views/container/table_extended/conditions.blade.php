@if ($container->start_date_for_us != '' || $container->start_date_for_client != '')
    <small>{{ __('container.end_grace_period') }}: {{ $container->usage_dates['end_grace_date']}} / {{ $container->usage_dates['end_grace_date_for_us']}}
    @if($container->usage_dates['overdue_days'] != 0 || $container->usage_dates['overdue_days_for_us'] != 0)
        <br>
        <b>{{ __('container.overdue_days') }}: {{ $container->usage_dates['overdue_days'] }} ({{ $container->usage_dates['snp_amount_for_client'] }})
            / {{ $container->usage_dates['overdue_days_for_us'] }} ({{ $container->usage_dates['snp_amount_for_us'] }})
        </b>
    @endif
    </small>
@endif
<br>
<button class="btn btn-primary btn-sm mt-2" type="button"
        data-toggle="collapse"
        data-target="#collapseSNPconditions{{ $container->id }}"
        aria-expanded="false"
        aria-controls="collapseExample">
    <i class="fa fa-angle-down"></i>
    {{ __('container.snp_conditions') }}
</button>
<div class="collapse mt-2" id="collapseSNPconditions{{ $container->id }}">
    <div class="card card-body">
        <b>{{ __('container.grace_period') }} {{ __('container.days') }}:</b> {{ !is_null($container->grace_period_for_client) ? $container->grace_period_for_client : '-' }}
        / {{ !is_null($container->grace_period_for_us) ? $container->grace_period_for_us : '-' }}
        <br>
        <b>{{ __('container.snp_amount_client') }}:</b>
        @if($container->usage_dates['range_client'] != '' || $container->snp_amount_for_client != '' )
            {{ $container->usage_dates['range_client'] != '' ? $container->usage_dates['range_client'].', далее' : '' }}
            {{ $container->snp_amount_for_client }}{{ $container->snp_currency }} {{ __('container.in_day') }}
        @else
            -
        @endif
        <br>
        <b>{{ __('container.snp_amount_us') }}:</b>
        @if($container->usage_dates['range_us'] != '' || $container->snp_amount_for_us != '')
            {{ $container->usage_dates['range_us'] != '' ? $container->usage_dates['range_us'].', далее' : '' }}
            {{ $container->snp_amount_for_us }}{{ $container->snp_currency }} {{ __('container.in_day') }}
        @else
            -
        @endif
    </div>
</div>

@if($container->additional_info != '')
    <br><small>{{ __('general.additional_info') }}: {{ $container->additional_info }}</small>
@endif
