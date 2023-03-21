<small>
    <b>{{ __('work_request.created') }}: </b>
    {{ $task->created_at }}
    @if($task->deadline != '')
        <br>
        <b>{{ __('work_request.deadline') }}: </b>{{ $task->deadline }}
        @if($task->done == '')
            {{ !is_null($task->can_change_deadline) ? ' '.__('work_request.can_change') : '' }}
        @endif
    @endif
    @if($task->done != '')
        <br>
        <b>{{ __('work_request.finished') }}: </b>{{ $task->done }}
    @endif
</small>
