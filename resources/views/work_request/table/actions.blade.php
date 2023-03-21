<a class="btn btn-app bg-primary" href="{{ route('work_request.show', $task->id) }}">
    <i class="far fa-eye"></i> {{ __('general.go') }}
</a>
<a class="btn btn-app bg-indigo" href="{{ route('work_request.edit', $task->id) }}">
    <i class="fas fa-pencil-alt"></i> {{ __('general.change') }}
</a>
<button
    class="btn btn-app bg-danger ajax-delete-row"
    data-action="delete_row"
    data-object="work_request"
    data-type="ajax"
    data-object-id="{{ $task->id }}">
    <i class="fas fa-trash"></i> {{ __('general.remove') }}
</button>
