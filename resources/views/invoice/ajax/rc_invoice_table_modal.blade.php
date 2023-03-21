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
<table class="table mt-4 datatable" id="invoices_table_modal">
    <thead>
    <tr>
        <th style="width: 1%">#</th>
        <th style="width: 10%">{{ __('general.type') }}</th>
        <th style="width: 20%">{{ __('general.amount') }}</th>
        <th style="width: 20%">{{ __('general.paid') }}</th>
        <th>{{ __('general.status') }}</th>
        <th style="width: 20%">{{ __('invoice.invoice_table') }}</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>{{ $invoice['id'] }}</td>
        <td>
            @switch($invoice->direction)
                @case('Доход')
                {{ __('general.income') }}
                @break
                @case('Расход')
                {{ __('general.outcome') }}
                @break
            @endswitch
            @if($invoice->edited != '')
                <a class="cursor-pointer" data-toggle="modal" data-target="#view_invoice_changes" data-invoice-id="{{ $invoice->id }}">
                    <i class="fas fa-clock"></i>
                </a>
            @endif
        </td>
        <td>
            @if ($invoice->direction == 'Доход')
                @if ($invoice->currency != 'RUB')
                    {{ number_format($invoice->amount_in_currency, 0, '.', ' ') }} {{ $invoice->currency }} ({{ $invoice->rate_out_date }}) <br>
                @endif
                {{ number_format($invoice->amount, 2, '.', ' ') }}р.
                @if ($invoice->currency != 'RUB')
                    @if ($invoice->amount_sale_date != '' || $invoice->amount_income_date != '')
                        @if ($invoice->amount != $invoice->amount_sale_date || $invoice->amount != $invoice->amount_income_date)
                            <br>
                            <small>
                                <b>{{ __('invoice.exchange_difference') }}:
                                    @if($invoice->amount_sale_date != '')
                                        {{ number_format($invoice->amount_sale_date - $invoice->amount, 2, '.', ' ') }}р.<br>
                                        ({{ number_format($invoice->amount_sale_date, 2, '.', ' ') }}р. {{ __('invoice.on_rate') }} {{ $invoice->rate_sale_date }})
                                    @else
                                        {{ number_format($invoice->amount_income_date - $invoice->amount, 2, '.', ' ') }}р.</b><br>
                                <a class="sell_currency_modal cursor-pointer"
                                   data-toggle="modal"
                                   data-target="#sell_currency"
                                   data-invoice-id="{{ $invoice->id }}"
                                   data-currency-amount="{{ $invoice->amount_in_currency_income_date }}">({{ __('invoice.currency_not_sold') }})
                                </a>
                                @endif
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
        </td>
        <td>
            @if ($invoice->direction == 'Доход')
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
        </td>
        <td class="project-state">
            <span class="badge badge-{{ $class }}">
                @include('invoice.status_switch', ['status' => $invoice->status])
                @if(in_array($invoice->status, ['Счет на согласовании','Согласована частичная оплата','Счет согласован на оплату']))
                    @if($invoice->sub_status != '')
                        / @include('invoice.status_switch', ['status' => $invoice->sub_status])
                    @endif
                @endif
            </span>
            <br>
            @if (in_array($invoice->status, ['Ожидается счет от поставщика', 'Ожидается загрузка счета']))
                @if($invoice->project)
                    <input type="file" class="form-control-file ajax_upload_invoice_file mt-4" name="invoice"
                           data-invoice-id="{{ $invoice->id }}">
                @else
                    <small>{{ __('general.project_delete') }}</small>
                @endif
            @endif
            @if(in_array($invoice->status, ['Счет на согласовании','Согласована частичная оплата','Счет согласован на оплату']))
                @if ($invoice->agree_1 != '')
                    {{ __('general.date') }}: {{ unserialize($invoice->agree_1)['date'] }}
                @endif
            @endif
            @if (in_array($invoice->status, ['Оплачен', 'Частично оплачен', 'Не оплачен']))
                <small>{{ $invoice['accountant_comment']!='' ? $invoice['accountant_comment'] : ''}}</small>
            @endif
        </td>
        <td>
            @if($invoice->direction == 'Расход')
                @if ($invoice->file != '' || $invoice->invoice_file != '')
                    {{ __('invoice.invoice_uploaded') }}
                @endif
            @elseif ($invoice->direction == 'Доход')
                @if ($invoice->file == '')
                    @if($invoice->status != 'Ожидается загрузка счета')
                        @can('create invoice draft')
                            @if (canWorkWithProject(optional($invoice->project)->id))
                                @if (!is_null($invoice->invoice_array))
                                    <a class="btn btn-sm btn-primary cursor-pointer" data-toggle="modal"
                                       data-invoice-id="{{ $invoice->id }}" data-target="#edit_draft_invoice_modal" id="edit_invoice_draft_emit">
                                        {{ __('invoice.edit_draft') }}
                                    </a>
                                @else
                                    <a class="btn btn-sm btn-primary cursor-pointer" data-toggle="modal"
                                       data-invoice-id="{{ $invoice->id }}" data-target="#create_draft_invoice_modal" id="create_invoice_draft_emit">
                                        {{ __('invoice.create_draft') }}
                                    </a>
                                @endif
                            @endif
                        @endcan
                        @can ('create invoice')
                            @if (!is_null($invoice->invoice_array))
                                <a class="btn btn-sm btn-primary cursor-pointer" data-toggle="modal"
                                   data-invoice-id="{{ $invoice->id }}" data-target="#create_invoice_modal" id="create_invoice_draft_emit">
                                    {{ __('invoice.create_invoice') }}
                                </a>
                            @else
                                {{ __('invoice.invoice_draft_not_created') }}
                            @endif
                        @endcan
                    @endif
                @else
                    @if(checkUploadedFileInvoice($invoice->id)['file'])
                        <a class="btn btn-app" href="{{ Storage::url(unserialize($invoice->file)['filename']) }}" download>
                            <i class="fas fa-download"></i> {{ __('general.download') }}
                        </a>
                    @else
                        <a class="btn btn-app" href="{{ Storage::url($invoice->file) }}" download>
                            <i class="fas fa-download"></i> {{ __('general.download') }}
                        </a>
                    @endif
                @endif
            @endif
        </td>
    </tr>
    </tbody>
</table>
