<small>
    <b>{{ __('task.from') }}: </b>
    @if($task->type == 'Система')
        {{ __('general.system') }}
    @else
        {{ optional($task->from)->name }}
    @endif
    @if(!is_null($task->accepted_user_id))
        |
        <b>{{ __('task.accepted') }}: </b>{{ optional(userInfo($task->accepted_user_id))->name }}
    @endif
    <br>
    <b>{{ __('task.responsible') }}: </b>{{ $task->send_to }}
    @if($task->info != '')
        ({{ __('task.redirected') }} {{ $task->info }})
    @endif
    @if(!empty($task->additional_users))
        <br>
        <b>{{ __('task.co-workers') }}: </b>
        @foreach($task->additional_users as $additional_user)
            @php
                $users_name [] = optional(userInfo($additional_user))->name;
            @endphp
        @endforeach
        @php
            echo implode(', ', $users_name);
            $users_name = [];
        @endphp
    @endif
</small>
