@if(!is_null($bank_account_balances))
    <span class="dropdown-item dropdown-header">{{ $bank_account_balances->created_at->format('d.m.Y H:i') }}</span>
    <div class="dropdown-divider"></div>
    @foreach($bank_account_balances->companies as $key => $info)
        <div class="ml-2 mt-2 mb-2">
            {{ $info['name'] }}: {{ $info['amount'] }}
        </div>
        @if ($key != array_key_last($bank_account_balances->companies))
            <div class="dropdown-divider"></div>
        @endif
    @endforeach
@else
    <div class="dropdown-item text-sm-center">Нет информации по остаткам на счетах</div>
@endif
