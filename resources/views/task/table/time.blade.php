<small>
    <b>{{ __('task.created') }}: </b>
    {{ $task->created_at }}
    @if($task->deadline != '')
        <br>
        <b>{{ __('task.deadline') }}: </b>{{ $task->deadline }}
        @if($task->done == '')
            {{ !is_null($task->can_change_deadline) ? ' '.__('task.can_change') : '' }}
        @endif
    @endif
    @if($task->done != '')
        <br>
        <b>{{ __('task.finished') }}: </b>{{ $task->done }}
    @endif
</small>
