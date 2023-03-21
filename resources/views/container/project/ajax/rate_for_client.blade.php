<i class="fas fa-dollar-sign bg-red"></i>
<div class="timeline-item">
    <h3 class="timeline-header">
        {{ __('container.project_rate_for_client') }} {{ $container_project->rate_for_client_usd == ''
            ? __('container.project_not_set_f')
            : $container_project->rate_for_client_usd.'USD'.' ('.$container_project->rate_for_client_rub.'р, '.__('general.rate').' '.$container_project->rate_for_client_bank.')' }}
    </h3>
    @if($container_project->rate_for_client_usd == '')
        <div class="timeline-body">
            <form action="{{ route('container_project.update', $container_project->id) }}" method="post">
                @csrf
                @method('PUT')
                <input type="hidden" name="action" value="update_rate_for_client">
                <div class="row">
                    <div class="col-md-4">
                        <label>{{ __('container.project_rate_for_client_usd') }}</label>
                        <div class="form-group">
                            <input class="form-control rate_input"
                                   type="text"
                                   name="rate_for_client_usd"
                                   id="rate_for_client_usd"
                                   onkeyup="fillRateInRub();"
                                   placeholder="{{ __('container.project_rate_for_client_usd') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label>{{ __('general.CB_rate') }}-{{ $currency_rates->usd_ratio }} {{ __('general.for_today') }}</label>
                        <div class="form-group">
                            <input class="form-control rate_input"
                                   type="text"
                                   name="rate_for_client_bank"
                                   placeholder="{{ __('general.CB_rate') }}-{{ $currency_rates->usd_ratio }} {{ __('general.for_today') }}"
                                   id="rate_for_client_bank"
                                   onkeyup="fillRateInRub();"
                                   value="{{ $currency_rates->usd_divided }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label>{{ __('container.project_rate_for_client_rub') }}</label>
                        <div class="form-group">
                            <input class="form-control rate_input"
                                   type="text"
                                   name="rate_for_client_rub"
                                   placeholder="{{ __('container.project_rate_for_client_rub') }}"
                                   id="rate_for_client_rub">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-sm"
                        data-action='{"update_div":{"div_id":"rate_for_client"}}'>
                    {{ __('general.save') }}
                </button>
            </form>
        </div>
    @else
        <div class="timeline-footer">
            <form class="form-inline" action="{{ route('container_project.destroy', $container_project->id) }}" method="post">
                @csrf
                @method('DELETE')
                <input type="hidden" name="action" value="delete_rate_for_client">
                <button type="submit" class="btn btn-outline-danger btn-sm"
                        data-action='{"update_div":{"div_id":"rate_for_client"}}'>
                    {{ __('general.remove') }}
                </button>
            </form>
        </div>
    @endif
</div>
