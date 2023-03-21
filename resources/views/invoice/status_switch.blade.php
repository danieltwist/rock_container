@switch($status)
    @case('Счет на согласовании')
    {{ __('invoice.status_agree') }}
    @break
    @case('Создан черновик инвойса')
    {{ __('invoice.status_draft_invoice') }}
    @break
    @case('Ожидается счет от поставщика')
    {{ __('invoice.status_waiting_for_invoice') }}
    @break
    @case('Ожидается создание инвойса')
    {{ __('invoice.status_waiting_for_create_invoice') }}
    @break
    @case('Ожидается оплата')
    {{ __('invoice.status_waiting_for_payment') }}
    @break
    @case('Ожидается загрузка счета')
    {{ __('invoice.status_waiting_upload_invoice') }}
    @break
    @case('Счет согласован на оплату')
    {{ __('invoice.status_agreed') }}
    @break
    @case('Согласована частичная оплата')
    {{ __('invoice.status_part_agreed') }}
    @break
    @case('Частично оплачен')
    {{ __('invoice.status_part_paid') }}
    @break
    @case('Оплачен')
    {{ __('invoice.status_paid') }}
    @break
    @case('Срочно')
    {{ __('invoice.sub_status_urgent') }}
    @break
    @case('Взаимозачет')
    {{ __('invoice.sub_status_compensation') }}
    @break
    @case('Отложен')
    {{ __('invoice.sub_status_postponed') }}
    @break
    @case('Без дополнительного статуса')
    {{ __('invoice.sub_status_without') }}
    @break
@endswitch
