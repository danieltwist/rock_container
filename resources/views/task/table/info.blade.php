@if($task->name != '')
    <a class="text-dark" href="{{ route('task.show', $task->id) }}">{{ $task->name }}</a>
@endif
@if($task->name !='' && $task->text !='') <br> @endif
@if(mb_strlen($task->text)>120)
    <div id="collapse_task_text_compact_{{ $task->id }}">
        {{ \Illuminate\Support\Str::limit($task->text, 120, $end='...') }}
        <a class="cursor-pointer collapse-trigger" data-div_id="collapse_task_text_full_{{ $task->id }}">
            <i class="fa fa-angle-down"></i> {{ __('general.expand') }}
        </a>
    </div>
    <div id="collapse_task_text_full_{{ $task->id }}" class="d-none">
        {{ $task->text }}
        <a class="cursor-pointer collapse-trigger" data-div_id="collapse_task_text_compact_{{ $task->id }}">
            <i class="fa fa-angle-up"></i> {{ __('general.collapse') }}
        </a>
    </div>
@else
    {{ $task->text }}
@endif
@if($task->model != 'free')
    @php
        $model = $task->model;
    @endphp
    <br>
    @if($task->model == 'upd')
        {{ __('task.object') }}: <a href="{{ route('task.show', $task->id) }}"> {{ $task->object }}</a><br>
    @elseif($task->model == 'container_project')
        {{ __('task.object') }}: <a href="{{ route('container_project.index').'?need_to_process' }}"> {{ $task->object }}</a><br>
    @else
        {{ __('task.object') }}: <a href="{{ route($model.'.show',$task->model_id) }}"> {{ $task->object }}</a><br>
    @endif
    @if($task->object != 'project' && !is_null($task->project_id))
        @if($task->for_project)
            <small>{{ __('general.project') }}: <a href="{{ route('project.show', $task->project_id) }}">{{ optional($task->for_project)->name }}</a></small>
        @else
            <small>{{ __('general.project_delete') }}</small>
        @endif
    @endif
@endif
