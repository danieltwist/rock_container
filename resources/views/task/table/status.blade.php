<span class="badge badge-{{ $task->class }}">
    @include('task.status_switch', ['status' => $task->status])
</span>
<br>
<small>
    <b>{{ __('task.created') }}: </b>
    {{ $task->created_at }}
    @if($task->deadline != '')
        <br>
        @if($task->overdue)
            <strong class="text-danger">{{ __('task.deadline') }}: {{ $task->deadline }}</strong>
        @else
            <strong>{{ __('task.deadline') }}: </strong>{{ $task->deadline }}
        @endif
        @if($task->done == '')
            {{ !is_null($task->can_change_deadline) ? ' '.__('task.can_change') : '' }}
        @endif
    @endif
    @if($task->done != '')
        <br>
        <b>{{ __('task.finished') }}: </b>{{ $task->done }}
    @endif
</small>

