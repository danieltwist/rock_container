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
