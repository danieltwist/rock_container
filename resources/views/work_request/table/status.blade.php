<span class="badge badge-{{ $task->class }}">
    @include('task.status_switch', ['status' => $task->status])
</span>
<br>
<small>
    <b>{{ __('work_request.created') }}: </b>
    {{ $task->created_at }}
    @if($task->deadline != '')
        <br>
        @if($task->overdue)
            <strong class="text-danger">{{ __('work_request.deadline') }}: {{ $task->deadline }}</strong>
        @else
            <strong>{{ __('work_request.deadline') }}: </strong>{{ $task->deadline }}
        @endif
        @if($task->done == '')
            {{ !is_null($task->can_change_deadline) ? ' '.__('work_request.can_change') : '' }}
        @endif
    @endif
    @if($task->done != '')
        <br>
        <b>{{ __('work_request.finished') }}: </b>{{ $task->done }}
    @endif
</small>

