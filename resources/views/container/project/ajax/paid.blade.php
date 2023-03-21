<div class="card-header">
    <h3 class="card-title">{{ __('container.project_paid') }}</h3>
</div>
<form action="{{ route('container_project.update', $container_project->id) }}" method="POST">
    @csrf
    @method('PUT')
    <input type="hidden" name="action" value="update_paid">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <label>{{ __('container.project_paid_usd') }}</label>
                <div class="form-group">
                    <input class="form-control rate_input"
                           type="text"
                           name="paid_usd"
                           id="paid_usd"
                           onkeyup="fillPaidInRub();"
                           placeholder="{{ __('container.project_paid_usd') }}"
                           value="{{ $container_project->paid_usd }}">
                </div>
            </div>
            <div class="col-md-6">
                <label>{{ __('general.CB_rate') }}-{{ $currency_rates->usd_ratio }} {{ __('general.for_today') }}</label>
                <div class="form-group">
                    <input class="form-control rate_input"
                           type="text"
                           name="paid_bank"
                           placeholder="{{ __('general.CB_rate') }}-{{ $currency_rates->usd_ratio }} {{ __('general.for_today') }}"
                           id="paid_bank"
                           onkeyup="fillPaidInRub();"
                           value="{{ $currency_rates->usd_divided }}">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <label>{{ __('container.project_paid_rub') }}</label>
                <div class="form-group">
                    <input class="form-control rate_input"
                           type="text"
                           name="paid_rub"
                           placeholder="{{ __('container.project_paid_rub') }}"
                           id="paid_rub"
                           value="{{ $container_project->paid_rub }}">
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <button type="submit" class="btn btn-sm btn-outline-primary"
                data-action='{"update_div":{"div_id":"paid"},"update_container_project_top_panel":{"need_update":"true"}}'>
            {{ __('general.save') }}
        </button>
    </div>
</form>
