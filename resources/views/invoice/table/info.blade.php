@if(!is_null($invoice->losses_potential) && (!is_null($invoice->losses_amount)) && (is_null($invoice->losses_confirmed)))
    {{ __('invoice.losses_potential') }} {{ $invoice->losses_amount }}р.
@elseif(!is_null($invoice->losses_confirmed))
    {{ __('invoice.losses') }} {{ $invoice->losses_amount }}р.
@else
    @switch($invoice->direction)
        @case('Доход')
        {{ __('general.income') }}
        @break
        @case('Расход')
        {{ __('general.outcome') }}
        @break
    @endswitch
@endif
<small>{{ __('general.from') }} {{ $invoice['created_at']->format('d.m.Y H:i:s') }}</small>
@if($invoice->edited != '')
    <a data-toggle="modal" data-target="#view_component_history"
            class="text-dark cursor-pointer"
            data-component="invoice"
            data-id="{{ $invoice->id }}">
        <i class="fas fa-history"></i>
    </a>
@endif
@if($invoice->expense_type != '' || $invoice->expense_category != '')
    <br><small>{{ $invoice->expense_category }} {{ $invoice->expense_type }}</small>
@endif
@if($invoice->income_type != '' || $invoice->income_category != '')
    <br><small>{{ $invoice->income_category }} {{ $invoice->income_type }}</small>
@endif
@if (!is_null($invoice->project))
    <br><a href="{{ route('project.show', $invoice->project->id) }}">{{ $invoice->project->name }}</a>
@endif
@if (!is_null($invoice->application))
    / <a href="{{ route('application.show', $invoice->application->id) }}">Заявка {{ $invoice->application->name }}</a>
    <br>
@else
    <br>
@endif
<small>
    @if ($invoice->additional_info !='')
        @if(mb_strlen($invoice->additional_info)>100)
            <div id="collapse_task_text_compact_{{ $invoice->id }}">
                {{ \Illuminate\Support\Str::limit($invoice->additional_info, 100, $end='...') }}
                <a class="cursor-pointer collapse-trigger" data-div_id="collapse_task_text_full_{{ $invoice->id }}">
                    <i class="fa fa-angle-down"></i> {{ __('general.expand') }}
                </a>
            </div>
            <div id="collapse_task_text_full_{{ $invoice->id }}" class="d-none">
                {{ $invoice->additional_info }}
                <a class="cursor-pointer collapse-trigger" data-div_id="collapse_task_text_compact_{{ $invoice->id }}">
                    <i class="fa fa-angle-up"></i> {{ __('general.collapse') }}
                </a>
            </div>
        @else
            {{ $invoice->additional_info }}
            <br>
        @endif
    @endif
    <i>
        @if($invoice->upd != '' || $invoice->upd_file != '')
            {{ __('general.upd') }}
        @endif
        @if($invoice->file != '' || $invoice->invoice_file != '')
            {{ __('general.invoice') }}
        @endif
        @if($invoice->payment_order != '' || $invoice->payment_order_file != '')
            {{ __('general.pp') }}
        @endif
    </i>
</small>
