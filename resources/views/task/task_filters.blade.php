<div class="btn-group">
    <button type="button" class="btn btn-default btn-sm tasks_filters" data-filter="">
        {{ __('general.all') }}
    </button>
    <button type="button" class="btn btn-default btn-sm tasks_filters"
            data-filter="Ожидает выполнения">{{ __('task.status_waiting') }}
    </button>
    <button type="button" class="btn btn-default btn-sm tasks_filters"
            data-filter="Выполняется">{{ __('task.status_in_work') }}
    </button>
    <button type="button" class="btn btn-default btn-sm tasks_filters"
            data-filter="Отправлена на проверку">{{ __('task.status_send_to_confirm') }}
    </button>
    <button type="button" class="btn btn-default btn-sm tasks_filters"
            data-filter="Просрочена">{{ __('task.status_delayed') }}
    </button>
    <button type="button" class="btn btn-default btn-sm tasks_filters"
            data-filter="Выполнена">{{ __('task.status_done') }}
    </button>
</div>
