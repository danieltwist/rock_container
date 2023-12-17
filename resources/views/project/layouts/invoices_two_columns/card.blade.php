@if((!is_null($invoice->losses_potential) && (!is_null($invoice->losses_amount)) && (is_null($invoice->losses_confirmed)) || !is_null($invoice->losses_confirmed)))
    @php
        $invoice->class = 'danger';
    @endphp
@endif
<div class="card card-{{ $invoice->class }} {{ !in_array($invoice->class, ['danger']) ? 'card-outline' : '' }} collapsed-card">
    <div class="card-header">
        <h4 class="card-title">
            @if(!is_null($invoice->losses_potential) && (!is_null($invoice->losses_amount)) && (is_null($invoice->losses_confirmed)))
                {{ __('invoice.losses_potential') }} {{ $invoice->losses_amount }}р.
            @elseif(!is_null($invoice->losses_confirmed))
                {{ __('invoice.losses') }} {{ $invoice->losses_amount }}р.
            @else
                {{$invoice['direction']}} №{{ $invoice->id }}
            @endif
            {{ __('general.from') }} {{ $invoice['created_at']->format('d.m.Y') }} {{ __('general.to') }}
            @if ($invoice->currency != 'RUB')
                {{ number_format($invoice->amount_in_currency, 0, '.', ' ') }} {{ $invoice->currency }} ({{ $invoice->rate_out_date }})
            @endif
            {{ number_format($invoice->amount, 2, '.', ' ') }}р.
            @if($invoice->edited != '')
                <a class="cursor-pointer" data-toggle="modal" data-target="#view_invoice_changes" data-invoice-id="{{ $invoice->id }}">
                    <i class="fas fa-clock"></i>
                </a>
            @endif
            <br><small>
                @if ($invoice['supplier_id']=='')
                    @if(!is_null($invoice->client))
                        <a class="text-decoration-none" href="{{ route('client.show', $invoice->client_id) }}">{{ $invoice->client['name'] }}</a>
                    @else
                        {{ __('general.client_was_deleted') }}
                    @endif
                @endif
                @if ($invoice['client_id']=='')
                    @if(!is_null($invoice->supplier))
                        <a class="text-decoration-none" href="{{ route('supplier.show', $invoice->supplier_id) }}">{{ $invoice->supplier['name'] }}</a>
                    @else
                        {{ __('general.supplier_was_deleted') }}
                    @endif
                @endif
                <br>
                @if ($invoice->additional_info !='')
                    @if(mb_strlen($invoice->additional_info)>60)
                        <div id="collapse_invoice_text_compact_{{ $invoice->id }}">
                            {{ \Illuminate\Support\Str::limit($invoice->additional_info, 60, $end='...') }}
                            <a class="cursor-pointer collapse-trigger" data-div_id="collapse_invoice_text_full_{{ $invoice->id }}"><i class="fa fa-angle-down"></i> {{ __('general.expand') }}</a>
                        </div>
                        <div id="collapse_invoice_text_full_{{ $invoice->id }}" class="d-none">
                            {{ $invoice->additional_info }}
                            <a class="cursor-pointer collapse-trigger" data-div_id="collapse_invoice_text_compact_{{ $invoice->id }}"><i class="fa fa-angle-up"></i> {{ __('general.collapse') }}</a>
                        </div>
                    @else
                        {{ $invoice->additional_info }}<br>
                    @endif
                @endif
            </small>
        </h4>
        <div class="card-tools">
            <button type="button" class="btn btn-tool view_invoice_modal"
                    data-toggle="modal"
                    data-target="#view_invoice"
                    data-invoice-id="{{ $invoice['id'] }}"
                    data-type="static">
                <i class="fas fa-eye"></i>
            </button>
            <button type="button" class="btn btn-tool" data-card-widget="collapse"
                    title="Collapse">
                <i class="fas fa-plus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        @if(!is_null($invoice->deadline))
            {{ __('invoice.pay_before') }} {{ $invoice->deadline }}<br>
        @endif
        {{ $invoice->status }}<br>
        @if(!is_null($invoice->upd))
            {{ __('general.upd') }}
        @endif
        @if(!is_null($invoice->file))
            {{ __('general.invoice') }}
        @endif
        @if(!is_null($invoice->payment_order))
            {{ __('general.pp') }}
        @endif
        <div class="mt-2">
            {{ __('general.amount') }}: @if ($invoice->direction == 'Доход')
                @if ($invoice->currency != 'RUB')
                    {{ number_format($invoice->amount_in_currency, 0, '.', ' ') }} {{ $invoice->currency }} ({{ $invoice->rate_out_date }}) <br>
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
                                        {{ number_format($invoice->amount_income_date - $invoice->amount, 2, '.', ' ') }}р.<br>
                                        <a class="sell_currency_modal cursor-pointer"
                                           data-toggle="modal"
                                           data-target="#sell_currency"
                                           data-invoice-id="{{ $invoice->id }}"
                                           data-currency-amount="{{ $invoice->amount_in_currency_income_date }}">({{ __('invoice.currency_not_sold') }})
                                        </a>
                                    @endif
                                </b>
                            </small>
                        @endif
                    @endif
                @endif
            @else
                @if ($invoice->currency != 'RUB')
                    {{ number_format($invoice->amount_in_currency, 0, '.', ' ') }} {{ $invoice->currency }} ({{ $invoice->rate_out_date }}) <br>
                @endif
                {{ number_format($invoice->amount, 2, '.', ' ') }}р.
                @if ($invoice->amount != $invoice->amount_actual && $invoice->amount_actual != '')
                    <br>{{ __('invoice.fact_amount') }}:<br>
                    @if ($invoice->currency != 'RUB')
                        {{ number_format($invoice->amount_in_currency_actual, 0, '.', ' ') }} {{ $invoice->currency }} ({{ $invoice->rate_out_date }}) <br>
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
        </div>
        <div class="mt-2">
            {{ __('general.paid') }}: @if ($invoice->direction == 'Доход')
                @if ($invoice->currency != 'RUB')
                    @if ($invoice->amount_in_currency_income_date != '')
                        {{ number_format($invoice->amount_in_currency_income_date, 0, '.', ' ') }} {{ $invoice->currency }} ({{ $invoice->rate_income_date }})
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
                        {{ number_format($invoice->amount_in_currency_income_date, 0, '.', ' ') }} {{ $invoice->currency }} ({{ $invoice->rate_income_date }})
                        <br>
                        {{ number_format($invoice->amount_income_date, 2, '.', ' ') }}р.
                    @endif
                @else
                    @if ($invoice->amount_income_date != '')
                        {{ number_format($invoice->amount_income_date, 2, '.', ' ') }}р.
                    @endif
                @endif
            @endif

        </div>
        @include('project.layouts.invoices_two_columns.buttons')
    </div>
</div>
