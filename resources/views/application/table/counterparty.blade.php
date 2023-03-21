@if($application->counterparty_type == 'Клиент')
    Клиент: <a href="{{ route('client.show', $application->client_id) }}" class="text-dark">{{ $application->client_name }}</a>
@endif
@if($application->counterparty_type == 'Поставщик')
    Поставщик: <a href="{{ route('supplier.show', $application->supplier_id) }}" class="text-dark">{{ $application->supplier_name }}</a>
@endif
@if(!is_null($application->contract_info))
    <br>
    <small>
        Договор: {{ $application->contract_info['name'] }} от {{ $application->contract_info['date'] }}
    </small>
@endif
@if(!is_null($application->additional_info))
    <br>
    <small>
        Дополнительная информация: {{ $application->additional_info }}
    </small>
@endif
