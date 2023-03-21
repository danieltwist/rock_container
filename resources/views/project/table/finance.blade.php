@if(!is_null($project->expense))
    {{ __('project.planned_income') }}: {{ number_format($project->expense->price_in_rub, 0, '.', ' ') }}р.<br>
    @if ($project->expense->currency != 'RUB')
        {{ __('project.in_currency') }}: {{ number_format($project->expense->price_in_currency, 0, '.', ' ') }} {{ $project->expense->currency }}<br>
        {{ __('general.rate') }}: {{ $project->expense->cb_rate .' / '. $project->finance['today_rate'] }}
    @else
        {{ __('project.project_currency') }}: {{ __('general.ruble') }}
    @endif
    <br><br>{{ __('project.planned_profit') }}: {{ number_format($project->expense->planned_profit, 0, '.', ' ') }}р.<br>
    <b>{{ __('project.now_profit') }}: {{ number_format($project->finance['profit'], 0, '.', ' ') }}р.</b>
    <br>
    <button class="btn btn-primary btn-sm mt-2" type="button"
            data-toggle="collapse"
            data-target="#collapseCosts{{$project->id}}"
            aria-expanded="false"
            aria-controls="collapseExample">
        <i class="fa fa-angle-down"></i>
        {{ __('project.planned_outcome') }} {{ number_format($project->expense->planned_costs, 0, '.', ' ') }}р.
    </button>

    <div class="collapse mt-2" id="collapseCosts{{$project->id}}">
        <div class="card card-body">
            <div class="text-muted">
                @if (!is_null($project->expense->expenses_array))
                    @foreach(unserialize($project->expense->expenses_array) as $expense)
                        @php
                            $type = $expense['type'];
                        @endphp
                        <p class="text-sm">{{ $expense[$type.'_name'] }}
                            <b class="d-block">
                                {{ $expense[$type.'_price_1pc'].$expense[$type.'_currency'] }}
                                {{ $expense[$type.'_currency']!='RUB' ? '('. $expense[$type.'_rate'] .'р.)' : ''}} x
                                {{ $expense[$type.'_amount'] }} =
                                {{ $expense[$type.'_total_price_in_rub'] }}р.
                            </b>
                        </p>
                    @endforeach
                @else
                    {{ __('project.cost_part_not_filled') }}
                @endif
            </div>
        </div>
    </div>
@else
    {{ __('project.block_not_filled') }}
@endif
