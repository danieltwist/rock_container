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
    @if (!is_null($invoice->agreement_date))
        <small>{{ $invoice->agreement_date->format('d.m.Y H:i:s') }}</small>
    @endif
@endif
@if (in_array($invoice->status, ['Оплачен', 'Частично оплачен', 'Не оплачен']))
    @if($invoice['accountant_comment']!='')
        <a class="cursor-pointer text-sm text-dark"
           data-toggle="collapse"
           data-target="#collapse_invoice_payment_info_{{ $invoice->id }}"
           aria-expanded="false"
           aria-controls="collapseExample">
            <i class="fa fa-angle-down"></i>
            {{ __('general.info') }}
        </a>
        <div class="collapse mt-2" id="collapse_invoice_payment_info_{{ $invoice->id }}">
            <small>{{ $invoice['accountant_comment'] }}</small>
        </div>
    @endif
@endif
