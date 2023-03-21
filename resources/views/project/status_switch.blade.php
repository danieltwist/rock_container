@switch($status)
    @case('Черновик')
    {{ __('project.status_draft') }}
    @break
    @case('В работе')
    {{ __('project.status_active') }}
    @break
    @case('Завершен')
    {{ __('project.status_finished') }}
    @break
@endswitch
