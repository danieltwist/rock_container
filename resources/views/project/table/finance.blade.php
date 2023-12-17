@if(!is_null($project->expense))
    <b>{{ __('project.now_profit') }}: {{ number_format($project->finance['profit'], 0, '.', ' ') }}р.</b><br>
    Доходы: {{ number_format($project->finance['total_price'], 0, '.', ' ') }}р.<br>
    Расходы: {{ number_format($project->finance['total_cost'], 0, '.', ' ') }}р.<br>
@else
    {{ __('project.block_not_filled') }}
@endif
