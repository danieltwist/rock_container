<a class="btn btn-app bg-primary" href="{{ route('task.show', $task->id) }}">
    <i class="far fa-eye">
    </i>
    {{ __('general.go') }}
</a>
<a class="btn btn-app bg-indigo" href="{{ route('task.edit', $task->id) }}">
    <i class="fas fa-pencil-alt">
    </i>
    {{ __('general.change') }}
</a>
@if(!is_null($task->deleted_at))
    <button
        class="btn btn-app bg-warning ajax-restore-row"
        data-action="restore_row"
        data-object="task"
        data-type="ajax"
        data-object-id="{{ $task->id }}">
        <i class="fas fa-trash-restore"></i>
        Восстановить
    </button>
    <button
        class="btn btn-app bg-danger ajax-delete-row"
        data-action="delete_row"
        data-object="task"
        data-type="ajax"
        data-object-id="{{ $task->id }}">
        <i class="fas fa-trash">
        </i>
        {{ __('general.remove') }}
    </button>
@else
    <button
        class="btn btn-app bg-danger ajax-delete-row"
        data-action="delete_row"
        data-object="task"
        data-type="ajax"
        data-object-id="{{ $task->id }}">
        <i class="fas fa-trash">
        </i>
        {{ __('general.remove') }}
    </button>
@endif
