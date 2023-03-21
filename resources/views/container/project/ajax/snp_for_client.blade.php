<i class="fas fa-clock bg-blue"></i>
<div class="timeline-item">
    <h3 class="timeline-header">
        {{ __('container.project_grace_period') }}
        {{ $container_project->grace_period == ''
            ? __('container.project_not_set_m')
            : $container_project->grace_period.' дней' }}, {{ __('container.project_snp_amount') }}
        {{ $container_project->snp_amount_usd == ''
            ? __('container.project_not_set_f')
            : $container_project->snp_amount_usd.'USD'.' ('.$container_project->snp_rub.'р, '. __('general.rate') .' '.$container_project->snp_bank.')' }}
    </h3>
    @if($container_project->grace_period == '' || $container_project->snp_amount_usd == '')
        <div class="timeline-body">
            <form action="{{ route('container_project.update', $container_project->id) }}" method="post">
                @csrf
                @method('PUT')
                <input type="hidden" name="action" value="update_snp">
                <div class="row">
                    <div class="col-md-12">
                        <label>{{ __('container.grace_period') }} {{ __('container.days') }}</label>
                        <div class="form-group">
                            <input class="form-control digits_only"
                                   type="text"
                                   name="grace_period"
                                   placeholder="{{ __('container.grace_period') }} {{ __('container.days') }}"
                                   value="{{ $container_project->grace_period }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label>{{ __('container.project_snp_amount_usd') }}</label>
                        <div class="form-group">
                            <input class="form-control rate_input"
                                   type="text"
                                   name="snp_amount_usd"
                                   id="snp_amount_usd"
                                   onkeyup="fillSNPInRub();"
                                   placeholder="{{ __('container.project_snp_amount_usd') }}"
                                   value="{{ $container_project->snp_amount_usd }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label>{{ __('general.CB_rate') }}-{{ $currency_rates->usd_ratio }} {{ __('general.for_today') }}</label>
                        <div class="form-group">
                            <input class="form-control rate_input"
                                   type="text"
                                   name="snp_bank"
                                   placeholder="{{ __('general.CB_rate') }}-{{ $currency_rates->usd_ratio }} {{ __('general.for_today') }}"
                                   id="snp_bank"
                                   onkeyup="fillSNPInRub();"
                                   value="{{ $currency_rates->usd_divided }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label>{{ __('container.project_snp_amount_rub') }}</label>
                        <div class="form-group">
                            <input class="form-control rate_input"
                                   type="text"
                                   name="snp_rub"
                                   placeholder="{{ __('container.project_snp_amount_rub') }}"
                                   id="snp_rub">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary btn-sm"
                                data-action='{"update_div":{"div_id":"snp_for_client"}}'>
                            {{ __('general.save') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    @else
        @if($info['snp_days'] != 0)
            <div class="timeline-body">
                {{ __('container.project_snp_days') }}: {{ $info['snp_days'].' = '.$info['snp_days'] * $container_project->snp_amount_usd.'USD'.' ('.$info['snp_days'] * $container_project->snp_rub.'р.)' }}
            </div>
        @endif
        <div class="timeline-footer">
            <form class="form-inline" action="{{ route('container_project.destroy', $container_project->id) }}" method="post">
                @csrf
                @method('DELETE')
                <input type="hidden" name="action" value="delete_snp">
                <button type="submit" class="btn btn-outline-danger btn-sm"
                        data-action='{"update_div":{"div_id":"snp_for_client"}}'>
                    {{ __('general.remove') }}
                </button>
            </form>
        </div>
    @endif
</div>
