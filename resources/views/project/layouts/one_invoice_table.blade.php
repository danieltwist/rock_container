<table class="table table-striped invoices_table">
    <thead>
    <tr>
        <th style="width: 1%">#</th>
        <th style="width: 29%">{{ __('general.type') }}</th>
        <th style="width: 30%">{{ __('general.counterparty') }}</th>
        <th style="width: 20%">{{ __('general.amount') }}</th>
        <th style="width: 20%">{{ __('general.paid') }}</th>
    </tr>
    </thead>
    <tbody>
        @php
            switch($invoice->status){
                case 'Удален': case 'Не оплачен':
                    $class = 'danger';
                    break;
                case 'Частично оплачен': case 'Оплачен':
                    $class = 'success';
                    break;
                case 'Ожидается счет от поставщика': case 'Ожидается создание инвойса': case 'Создан черновик инвойса': case 'Ожидается загрузка счета':
                    $class = 'warning';
                    break;
                case 'Согласована частичная оплата': case 'Счет согласован на оплату':
                    $class = 'info';
                    break;
                case 'Ожидается оплата':
                    $class = 'primary';
                    break;
                case 'Счет на согласовании':
                    $class = 'secondary';
                    break;
                default:
                    $class = 'secondary';
            }
        @endphp
        @if ($invoice->status == 'Оплачен')
            <tr class="table-success">
        @else
            <tr>
                @endif
                <td>{{$invoice['id']}}</td>
                <td>
                    @switch($invoice->direction)
                        @case('Доход')
                        {{ __('general.income') }}
                        @break
                        @case('Расход')
                        {{ __('general.outcome') }}
                        @break
                    @endswitch
                    @if (!is_null($invoice->project))
                        <br><a href="{{ route('project.show', $invoice->project->id) }}">{{ $invoice->project->name }}</a>
                    @endif
                    <br>
                    <small>
                        {{ $invoice['created_at'] }}
                        @if ($invoice['additional_info']!='')
                            <br><br>
                            {{ $invoice['additional_info'] }}
                        @endif
                    </small>
                </td>
                <td>
                    @if ($invoice['supplier_id']=='')
                        {{ !is_null($invoice->client) ? $invoice->client['name'] : __('general.client_was_deleted') }}
                    @endif
                    @if ($invoice['client_id']=='')
                        {{ !is_null($invoice->supplier) ? $invoice->supplier['name'] : __('general.supplier_was_deleted') }}
                        <br>
                        <small>
                            {{ !is_null($invoice->block) ? $invoice->block['name'] : '' }}
                        </small>
                    @endif
                    <small>
                        <br>
                        {{ !is_null($invoice->deadline) ? __('invoice.pay_before').' '.$invoice->deadline : '' }}
                    </small>
                </td>
                <td>
                    @if ($invoice->direction == 'Доход')
                        @if ($invoice->currency != 'RUB')
                            {{ number_format($invoice->amount_in_currency, 2, '.', ' ') }} {{ $invoice->currency }} ({{ $invoice->rate_out_date }}) <br>
                        @endif
                        {{ number_format($invoice->amount, 2, '.', ' ') }}р.
                        @if ($invoice->currency != 'RUB')
                            @if ($invoice->amount_sale_date != '' || $invoice->amount_income_date != '')
                                @if ($invoice->amount != $invoice->amount_sale_date || $invoice->amount != $invoice->amount_income_date)
                                    <br>
                                    <small><b>{{ __('invoice.exchange_difference') }}:
                                            @if($invoice->amount_sale_date != '')
                                                {{ number_format($invoice->amount_sale_date - $invoice->amount, 2, '.', ' ') }}р.<br>
                                                ({{ number_format($invoice->amount_sale_date, 2, '.', ' ') }}р. {{ __('invoice.on_rate') }} {{ $invoice->rate_sale_date }})
                                            @else
                                                {{ number_format($invoice->amount_income_date - $invoice->amount, 2, '.', ' ') }}р.</b><br>
                                        ({{ __('invoice.currency_not_sold') }})
                                        @endif
                                    </small>
                                @endif
                            @endif
                        @endif
                    @else
                        @if ($invoice->currency != 'RUB')
                            {{ number_format($invoice->amount_in_currency, 2, '.', ' ') }} {{ $invoice->currency }} ({{ $invoice->rate_out_date }}) <br>
                        @endif
                        {{ number_format($invoice->amount, 2, '.', ' ') }}р.
                        @if ($invoice->amount != $invoice->amount_actual && $invoice->amount_actual != '')
                            <br>{{ __('invoice.fact_amount') }}:<br>
                            @if ($invoice->currency != 'RUB')
                                {{ number_format($invoice->amount_in_currency_actual, 2, '.', ' ') }} {{ $invoice->currency }} ({{ $invoice->rate_out_date }}) <br>
                            @endif
                            {{ number_format($invoice->amount_actual, 2, '.', ' ') }}р.
                        @endif
                        @if ($invoice->currency != 'RUB')
                            @if ($invoice->amount_income_date != '')
                                @if ($invoice->amount_actual != $invoice->amount_income_date)
                                    <br>
                                    <small>
                                        <b>{{ __('invoice.exchange_difference') }}:
                                            {{ number_format($invoice->amount_income_date - $invoice->amount_actual, 2, '.', ' ') }}р.
                                        </b>
                                    </small>
                                @endif
                            @endif
                        @endif
                    @endif
                </td>
                <td>
                    @if ($invoice->direction == 'Доход')
                        @if ($invoice->currency != 'RUB')
                            @if ($invoice->amount_in_currency_income_date != '')
                                {{ number_format($invoice->amount_in_currency_income_date, 2, '.', ' ') }} {{ $invoice->currency }} ({{ $invoice->rate_income_date }})
                                <br>
                                {{ number_format($invoice->amount_income_date, 2, '.', ' ') }}р.
                                @if ($invoice->amount_in_currency > $invoice->amount_in_currency_income_date)
                                    <br>
                                    <small>
                                        <b>
                                            {{ __('invoice.difference') }}: {{ number_format($invoice->amount_in_currency - $invoice->amount_in_currency_income_date, 2, '.', ' ') }}
                                            {{ $invoice->currency }}
                                        </b>
                                    </small>
                                @endif
                            @endif
                        @else
                            @if ($invoice->amount_income_date != '')
                                {{ number_format($invoice->amount_income_date, 2, '.', ' ') }}р.
                            @endif
                        @endif
                    @else
                        @if ($invoice->currency != 'RUB')
                            @if ($invoice->amount_in_currency_income_date != '')
                                {{ number_format($invoice->amount_in_currency_income_date, 2, '.', ' ') }} {{ $invoice->currency }} ({{ $invoice->rate_income_date }})
                                <br>
                                {{ number_format($invoice->amount_income_date, 2, '.', ' ') }}р.
                            @endif
                        @else
                            @if ($invoice->amount_income_date != '')
                                {{ number_format($invoice->amount_income_date, 2, '.', ' ') }}р.
                            @endif
                        @endif
                    @endif
                </td>
            </tr>
    </tbody>
</table>
