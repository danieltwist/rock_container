@if($task->text != '')
    <b>{{ __('general.work_request') }}:</b> @nl2br($task->text)
@endif
@php
    $model = $task->model;
@endphp

@if(!is_null($model) && $task->model != 'free')
    <br>
    @if($task->model == 'upd')
        <b>{{ __('work_request.object') }}: </b><a
            href="{{ route('upload_upd', $task->id) }}"> {{ $task->object }}</a><br>
    @elseif($task->model == 'container_project')
        <b>{{ __('work_request.object') }}: </b><a
            href="{{ route('container_project.index').'?need_to_process' }}"> {{ $task->object }}</a>
        <br>
    @else
        <b>{{ __('work_request.object') }}: </b><a
            href="{{ route($model.'.show',$task->model_id) }}"> {{ $task->object }}</a>
        <br>
    @endif
@endif

@if(!is_null($model) && $task->model != 'project' && $task->project_id != '')
    <br>
    <b>{{ __('general.project') }}: </b>
    <a href="{{ route('project.show',$task->project_id) }}">
        {{ optional($task->for_project)->name }}
    </a>
    <br>
@endif
@if (!is_null($task->file))
    <div class="attachment-block clearfix mt-3">
        <p>{{ __('work_request.added_files_to_work_request') }}:</p>
        <ul>
            @foreach(unserialize($task->file) as $file)
                <li>
                    <a href="{{ Storage::url($file['url']) }}"
                       download>{{ $file['name'] }}</a>
                </li>
            @endforeach
        </ul>
    </div>
@endif

@if($task->deadline != '')
    <div class="mt-2">
        @if((in_array($role,['super-admin','director'])
            || auth()->user()->id == $task->accepted_user_id)
            && !in_array($task->status, ['Отправлен на проверку', 'Выполнен'])
            && !is_null($task->can_change_deadline)
            && !in_array($task->status, ['Отправлен на проверку', 'Выполнен']))
            <div class="form-group">
                @if($task->overdue)
                    <label class="text-danger">{{ __('work_request.finish_before') }}</label>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control task_deadline text-danger"
                               value="{{ $task->deadline }}"
                               id="task_deadline"
                               placeholder="{{ __('work_request.work_request_deadline') }}">
                        <span class="input-group-append">
                            <button type="button"
                                    class="btn btn-info btn-flat work_request_handler"
                                    data-work_request_id="{{ $task->id }}"
                                    data-action="change_deadline">
                                {{ __('general.update_') }}
                            </button>
                        </span>
                    </div>
                @else
                    <label>{{ __('work_request.finish_before') }}</label>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control task_deadline"
                               value="{{ $task->deadline }}"
                               id="task_deadline"
                               placeholder="{{ __('work_request.work_request_deadline') }}">
                        <span class="input-group-append">
                            <button type="button"
                                    class="btn btn-info btn-flat work_request_handler"
                                    data-work_request_id="{{ $task->id }}"
                                    data-action="change_deadline">
                                {{ __('general.update_') }}
                            </button>
                        </span>
                    </div>
                @endif
            </div>
        @else
            @if($task->overdue)
                <strong class="text-danger"><strong>{{ __('work_request.finish_before') }}:</strong> {{$task->deadline}}</strong>
            @else
                <strong>{{ __('work_request.finish_before') }}:</strong> {{$task->deadline}}
            @endif
        @endif
    </div>
@endif
<div class="mt-4">
    @if(in_array($role,['super-admin','director']) || auth()->user()->id == $task->from_user_id)
        <a class="btn bg-indigo" href="{{ route('work_request.edit', $task->id) }}">
            <i class="fas fa-pencil-alt">
            </i>
            {{ __('general.change') }}
        </a>
    @endif
    @if($task->status == 'Выполняется' && auth()->user()->id == $task->accepted_user_id)
        @if(!is_null($task->check_work))
            <button type="button" class="btn btn-success btn-sm work_request_handler"
                    data-work_request_id="{{ $task->id }}"
                    data-action="done_task"
                    data-reload="true">
                <i class="fas fa-check"></i>
                {{ __('work_request.finish_and_send_to_confirm') }}
            </button>
        @else
            <button type="button" class="btn btn-success work_request_handler"
                    data-work_request_id="{{ $task->id }}"
                    data-action="done_task"
                    data-reload="true">
                <i class="fas fa-check"></i>
                {{ __('work_request.finish') }}
            </button>
        @endif
    @endif
</div>
