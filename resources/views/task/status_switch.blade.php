@switch($status)
    @case('Ожидает выполнения')
    {{ __('task.status_waiting') }}
    @break
    @case('Выполняется')
    {{ __('task.status_in_work') }}
    @break
    @case('Выполняется')
    {{ __('task.status_in_work') }}
    @break
    @case('Отправлена на проверку')
    {{ __('task.status_send_to_confirm') }}
    @break
    @case('Просрочена')
    {{ __('task.status_delayed') }}
    @break
    @case('Выполнена')
    {{ __('task.status_done') }}
    @break
@endswitch
