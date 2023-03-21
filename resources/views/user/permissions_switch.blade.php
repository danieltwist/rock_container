@switch($permission)
    @case('Создать проект')
    {{ __('settings.role_create_projects') }}
    @break
    @case('Удалить проект')
    {{ __('settings.role_remove_projects') }}
    @break
    @case('Редактировать проект')
    {{ __('settings.role_edit_projects') }}
    @break
    @case('Оплатить счет')
    {{ __('settings.role_pay_invoices') }}
    @break
    @case('Согласовать счет на оплату')
    {{ __('settings.role_agree_invoices') }}
    @break
    @case('Удалить счет')
    {{ __('settings.role_remove_invoices') }}
    @break
    @case('Создать инвойс')
    {{ __('settings.role_create_invoices') }}
    @break
    @case('Удалить клиента')
    {{ __('settings.role_remove_clients') }}
    @break
    @case('Удалить поставщика')
    {{ __('settings.role_remove_suppliers') }}
    @break
    @case('Редактировать счет')
    {{ __('settings.role_edit_invoices') }}
    @break
    @case('Работать с проектом')
    {{ __('settings.role_work_with_projects') }}
    @break
    @case('Добавить пользователя')
    {{ __('settings.role_add_users') }}
    @break
    @case('Редактировать пользователя')
    {{ __('settings.role_edit_users') }}
    @break
    @case('Удалить пользователя')
    {{ __('settings.role_remove_users') }}
    @break
    @case('Удалить контейнер')
    {{ __('settings.role_remove_containers') }}
    @break
    @case('Создать черновик инвойса')
    {{ __('settings.role_create_invoice_draft') }}
    @break
    @case('Редактировать собственные контейнеры')
    {{ __('settings.role_edit_own_containers') }}
    @break
    @case('Менять статус оплаты завершенных проектов')
    {{ __('settings.role_edit_projects_paid_status') }}
    @break
@endswitch
