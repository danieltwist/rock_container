<b>{{ __('work_request.responsible_users') }}: </b> {{ $task->send_to }}
@if($task->info != '')
    ({{ __('work_request.redirected') }} {{ $task->info }})
@endif
<ul class="users-list clearfix mt-4">
    @foreach($task->responsible_user as $user)
        <li>
            <img class="avatar elevation-2"
                 src="{{ Storage::url(optional(userInfo($user))->avatar) }}"
                 alt="User Image">
            <a class="users-list-name mt-2">{{ optional(userInfo($user))->name }}</a>
            @if(!is_null($task->accepted_user_id) && $task->accepted_user_id == $user)
                <span class="users-list-date">{{ __('work_request.work_request_accepted') }}</span>
            @else
                <span class="users-list-date">&nbsp;</span>
            @endif
        </li>
    @endforeach
</ul>
@if(!is_null($task->additional_users))
    <b>{{ __('work_request.co-workers') }}: </b>
    <ul class="users-list clearfix">
        @foreach($task->additional_users as $user)
            <li>
                <img class="avatar elevation-2"
                     src="{{ Storage::url(optional(userInfo($user))->avatar) }}"
                     alt="User Image">
                <a class="users-list-name">{{ optional(userInfo($user))->name }}</a>
                @if(!is_null($task->accepted_user_id) && $task->accepted_user_id == $user)
                    <span class="users-list-date">{{ __('work_request.work_request_accepted') }}</span>
                @else
                    <span class="users-list-date">&nbsp;</span>
                @endif
            </li>
        @endforeach
    </ul>
@endif
