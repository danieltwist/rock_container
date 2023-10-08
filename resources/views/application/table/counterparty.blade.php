@if($application->counterparty_type == 'Клиент')
    Клиент: <a href="{{ route('client.show', $application->client_id) }}" class="text-dark text-bold">{{ $application->client_name }}</a>
@endif
@if($application->counterparty_type == 'Поставщик')
    Поставщик: <a href="{{ route('supplier.show', $application->supplier_id) }}" class="text-dark text-bold">{{ $application->supplier_name }}</a>
@endif
@if(!is_null($application->contract_info))
    <br>
    <small>
        Договор: {{ $application->contract_info['name'] }} от {{ is_null($application->contract_info['date']) ?: \Carbon\Carbon::parse($application->contract_info['date'])->format('d.m.Y') }}
    </small>
@endif
@if(!is_null($application->additional_info))
    <br>
    <small>
        <div id="collapse_task_text_compact_{{ $application->id }}">
            {{ \Illuminate\Support\Str::limit($application->additional_info, 40, $end='...') }}
            <a class="cursor-pointer collapse-trigger text-dark" data-div_id="collapse_task_text_full_{{ $application->id }}">
                <i class="fa fa-angle-down"></i>
            </a>
        </div>
        <div id="collapse_task_text_full_{{ $application->id }}" class="d-none">
            {{ $application->additional_info }}
            <a class="cursor-pointer collapse-trigger text-dark" data-div_id="collapse_task_text_compact_{{ $application->id }}">
                <i class="fa fa-angle-up"></i>
            </a>
        </div>
    </small>
@endif
