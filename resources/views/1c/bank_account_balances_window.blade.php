@if(!is_null($bank_account_balances) || !is_null($safe_balance))
    @if(!is_null($bank_account_balances))
        <span class="dropdown-item dropdown-header">Банковские счета на {{ $bank_account_balances->created_at->format('d.m.Y H:i') }}</span>
        <div class="dropdown-divider"></div>
        @foreach($bank_account_balances->companies as $key => $info)
            <div class="ml-2 mt-2 mb-2">
                {{--            {{ $info['name'] }}: --}}
                {{ number_format($info['amount'], 2, '.', ' ') }}р.
            </div>
            @if ($key != array_key_last($bank_account_balances->companies))
                <div class="dropdown-divider"></div>
            @endif
        @endforeach
    @endif
    @if(!is_null($safe_balance))
        <span class="dropdown-item dropdown-header">Сейф</span>
        <div class="dropdown-divider"></div>
        <div class="ml-2 mt-2 mb-2">
            {{ number_format($safe_balance, 2, '.', ' ') }}р.
        </div>
    @endif
@else
    <div class="dropdown-item text-sm-center">{{ __('invoice.no_info_about_account_balances') }}</div>
@endif
