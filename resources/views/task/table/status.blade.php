<span class="badge badge-{{ $task->class }}">
    @include('task.status_switch', ['status' => $task->status])
</span>
<br>
<small>
    <b>{{ __('task.created') }}: </b>
    {{ !is_null($task->created_at) ? $task->created_at->format('d.m.Y H:i') : "" }}
    
    @if($task->deadline != '')
        <br>
        @if($task->overdue)
            <strong class="text-danger">{{ __('task.deadline') }}: {{ !is_null($task->deadline) ? $task->deadline->format('d.m.Y H:i') : "" }}</strong>
        @else
            <strong>{{ __('task.deadline') }}: </strong>{{ !is_null($task->deadline) ? $task->deadline->format('d.m.Y H:i') : "" }}
        @endif
        @if($task->done == '')
            {{ !is_null($task->can_change_deadline) ? ' '.__('task.can_change') : '' }}
        @endif
    @endif
    @if($task->done != '')
        <br>
        <b>{{ __('task.finished') }}: </b>{{ !is_null($task->done) ? $task->done->format('d.m.Y H:i') : "" }}
    @endif
</small>

