<a class="btn btn-app bg-success view_invoice_modal"
   data-toggle="modal"
   data-target="#view_invoice"
   data-invoice-id="{{ $invoice['id'] }}"
   data-type="ajax">
    <i class="far fa-eye">
    </i>
    {{ __('general.show') }}
</a>
@if (!is_null($invoice->project))
    @if ($invoice->direction == 'Доход')
        @if($invoice->status =='Создан черновик инвойса')
            <a class="btn btn-app bg-orange"
               data-toggle="modal"
               data-target="#create_task_modal"
               data-model="invoice"
               data-text="Создайте инвойс"
               data-send_to="Группа Бухгалтеры"
               data-user="Группе Бухгалтеры"
               data-model-id="{{ $invoice['id'] }}"
            >
        @elseif($invoice->status == 'Ожидается создание инвойса')
            <a class="btn btn-app bg-orange"
               data-toggle="modal"
               data-target="#create_task_modal"
               data-model="invoice"
               data-text="Создайте черновик инвойса"
               data-send_to="{{ $invoice->project->user->id }}"
               data-user="{{ $invoice->project->user->name }}"
               data-model-id="{{ $invoice['id'] }}"
            >
@else
    @if(in_array($invoice->status, ['Оплачен', 'Частично оплачен']))
    <a class="btn btn-app bg-orange"
       data-toggle="modal"
       data-target="#create_task_modal"
       data-model="invoice"
       data-text="Запросите платежку у клиента"
       data-send_to="{{ $invoice->project->user->id }}"
       data-user="{{ $invoice->project->user->name }}"
       data-model-id="{{ $invoice['id'] }}"
    >
    @else
        <a class="btn btn-app bg-orange"
           data-toggle="modal"
           data-target="#create_task_modal"
           data-model="invoice"
           data-text="Поторопите клиента к оплате"
           data-send_to="{{ $invoice->project->user->id }}"
           data-user="{{ $invoice->project->user->name }}"
           data-model-id="{{ $invoice['id'] }}"
        >
    @endif
@endif
    @elseif($invoice->direction == 'Расход')
        @if(in_array($invoice->status, ['Ожидается счет от поставщика']))
            <a class="btn btn-app bg-orange"
               data-toggle="modal"
               data-target="#create_task_modal"
               data-model="invoice"
               data-text="Загрузите счет от клиента"
               data-send_to="{{ $invoice->project->user->id }}"
               data-user="{{ $invoice->project->user->name }}"
               data-model-id="{{ $invoice['id'] }}"
            >
        @else
            <a class="btn btn-app bg-orange"
               data-toggle="modal"
               data-target="#create_task_modal"
               data-model="invoice"
               data-text="Загрузите платежку по счету"
               data-send_to="Группа Бухгалтеры"
               data-user="Группа Бухгалтеры"
               data-model-id="{{ $invoice['id'] }}"
            >
        @endif
    @endif
        <i class="fas fa-briefcase"></i>
        {{ __('general.task') }}
    </a>
@endif
@can ('edit invoices')
    <a class="btn btn-app bg-indigo" data-toggle="modal"
       data-type="ajax"
       data-target="#edit_invoice_modal"
       data-invoice-id="{{ $invoice['id'] }}">
        <i class="fas fa-pencil-alt"></i>
        {{ __('general.change') }}
    </a>
@endcan
@can ('remove invoices')
    @if(!is_null($invoice->deleted_at))
        <button
            class="btn btn-app bg-warning ajax-restore-row"
            data-action="restore_row"
            data-object="invoice"
            data-type="ajax"
            data-object-id="{{ $invoice->id }}">
            <i class="fas fa-trash-restore"></i>
            Восстановить
        </button>
        <button
            class="btn btn-app bg-danger ajax-delete-row"
            data-action="delete_row"
            data-object="invoice"
            data-type="ajax"
            data-object-id="{{ $invoice->id }}">
            <i class="fas fa-trash"></i>
            {{ __('general.remove') }}
        </button>
    @else
        <button
            class="btn btn-app bg-danger ajax-delete-row"
            data-action="delete_row"
            data-object="invoice"
            data-type="ajax"
            data-object-id="{{ $invoice->id }}">
            <i class="fas fa-trash"></i>
            {{ __('general.remove') }}
        </button>
    @endif
@endcan
