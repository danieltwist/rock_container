<i class="fas fa-coins bg-red"></i>
<div class="timeline-item">
    <h3 class="timeline-header">
        {{ __('container.project_expenses') }} {{ is_null($container_project->expenses) ? '' : number_format($info['outcome'], 0, '.', ' ').'р.' }}
    </h3>
    <div class="timeline-body">
        @if(!is_null($container_project->expenses))
            <div class="row" class="color-palette-box">
                @foreach($container_project->expenses as $key => $expense)
                    <div class="col-sm-4" id="{{ $expense['type'] }}">
                        <div class="card card-outline card-warning">
                            <div class="card-header">
                                <h3 class="card-title">{{ $expense[$expense['type'].'_name'] }}</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                {{ number_format($expense[$expense['type'].'_price_in_currency'], 0, '.', ' ') }} {{ $expense[$expense['type'].'_currency'] }}
                                @if($expense[$expense['type'].'_currency'] != 'RUB')
                                    ({{ number_format($expense[$expense['type'].'_total_price_in_rub'], 0, '.', ' ') }}р.)
                                @endif
                            </div>
                            <div class="card-footer">
                                <form action="{{ route('container_project.destroy', $container_project->id) }}" method="post">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="action" value="remove_expense">
                                    <input type="hidden" name="array_key" value="{{ $key }}">
                                    <button type="submit" class="btn btn-sm btn-danger remove_expense_block_cp mt-2"
                                            data-block-delete="{{ $expense['type'] }}"
                                            data-action='{"update_div":{"div_id":"expenses"},"update_container_project_top_panel":{"need_update":"true"}}'>
                                        <i class="fas fa-trash"></i> {{ __('general.remove') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            {{ __('container.project_no_expenses') }}
        @endif
            <form action="{{ route('container_project.update', $container_project->id) }}" method="post">
                @csrf
                @method('PUT')
                <input type="hidden" name="action" value="update_expenses">
                <div class="row mt-2 expense-blocks">
                    <input type="hidden" id="usd_rate" value="{{ $currency_rates->USD }}">
                    <input type="hidden" id="cny_rate" value="{{ $currency_rates->CNY }}">
                    <input type="hidden" id="usd_divided" value="{{ $currency_rates->usd_divided }}">
                    <input type="hidden" id="cny_divided" value="{{ $currency_rates->cny_divided }}">
                    <input type="hidden" id="usd_ratio" value="{{ $currency_rates->usd_ratio }}">
                    <input type="hidden" id="cny_ratio" value="{{ $currency_rates->cny_ratio }}">
                </div>
                <a class="btn btn-success btn-sm" id="add_expense_cp">
                    <i class="fas fa-plus"></i> {{ __('general.add') }}
                </a>
                <button type="submit" id="cp_expenses_save" class="pl-2 btn btn-primary btn-sm d-none"
                        data-action='{"update_div":{"div_id":"expenses"},"update_container_project_top_panel":{"need_update":"true"}}'>
                    {{ __('general.save') }}
                </button>
        </form>
    </div>
</div>
