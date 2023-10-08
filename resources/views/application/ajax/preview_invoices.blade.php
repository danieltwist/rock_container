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
                {{ number_format($invoice['amount_in_rubles'], 2, '.', ' ') }}р.
                <br>
                <div class="icheck-primary icheck-inline">
                    <input type="radio" id="currency_rub_{{ $key }}" name="currency_out[{{ $key }}]" {{ $invoice['currency'] == 'RUB' ? 'checked' : "" }} value="RUB"/>
                    <label for="currency_rub_{{ $key }}">RUB</label>
                </div>
                <div class="icheck-primary icheck-inline">
                    <input type="radio" id="currency_usd_{{ $key }}" name="currency_out[{{ $key }}]"  {{ $invoice['currency'] == 'USD' ? 'checked' : "" }} value="USD"/>
                    <label for="currency_usd_{{ $key }}">USD</label>
                </div>
                <div class="icheck-primary icheck-inline">
                    <input type="radio" id="currency_cny_{{ $key }}" name="currency_out[{{ $key }}]"  {{ $invoice['currency'] == 'CNY' ? 'checked' : "" }} value="CNY"/>
                    <label for="currency_cny_{{ $key }}">CNY</label>
                </div>
                <br><br>
            </strong>
        @endforeach
    @else
        Нет расходов / доходов для добавления
    @endif
@else
    <strong>Количество добавленных контейнеров не соответствует данным заявки, генерация доходов / расходов недоступна</strong>
@endif
