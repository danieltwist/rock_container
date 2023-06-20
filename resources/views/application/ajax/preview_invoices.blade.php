@if($allowed)
    @if(!is_null($invoices))
        <input type="hidden" name="all_invoices" value="{{ serialize($invoices) }}">
        @foreach($invoices as $key => $invoice)
            <div class="icheck-primary d-inline">
                <input type="checkbox"
                       name="preview_invoices_application_{{ $key }}"
                       id="preview_invoices_application_{{ $key }}"
                       checked>
                <label for="preview_invoices_application_{{ $key }}">{{ $invoice['type'] }} - {{ $invoice['info'] }}</label>
            </div>
            <br>{{ $invoice['counterparty_name'] }}
            <br>
            <strong>
                Сумма:
                @if($invoice['currency'] != 'RUB')
                    {{ number_format($invoice['amount_in_currency'], 0, '.', ' ') }} {{ $invoice['currency'] }} /
                @endif
                {{ number_format($invoice['amount_in_rubles'], 0, '.', ' ') }}р.
                <br><br>
            </strong>
        @endforeach
    @else
        Нет расходов / доходов для добавления
    @endif
@else
    <strong>Количество добавленных контейнеров не соответствует данным заявки, генерация доходов / расходов недоступна</strong>
@endif
