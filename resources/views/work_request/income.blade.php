@extends('layouts.project')
@section('title', __('work_request.work_requests_list'))
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">{{ __('work_request.my_work_requests') }}</h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @include('layouts.info_block')
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header cursor-pointer" data-card-widget="collapse">
                            <h3 class="card-title">{{ __('work_request.accepted_work_requests') }}</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="card-body card-comments">
                            @if($accepted_tasks->isNotEmpty())
                                @foreach($accepted_tasks as $task)
                                    @php
                                        $model = $task->model;
                                    @endphp
                                        <div class="card-comment">
                                            @if($task->type == 'Система')
                                                <img class="img-circle img-sm" src="/storage/avatars/system.jpg">
                                                <div class="comment-text">
                                                <span class="username">
                                                    {{ __('general.system') }}
                                                    <span class="text-muted float-right">{{ $task->created_at->diffForHumans() }}</span>
                                                </span>
                                            @else
                                                <img class="img-circle img-sm" src="{{ Storage::url(optional($task->from)->avatar) }}">
                                                <div class="comment-text">
                                                <span class="username">
                                                    {{ optional($task->from)->name }}
                                                    <span class="text-muted float-right">{{ $task->created_at->diffForHumans() }}</span>
                                                </span>
                                            @endif
                                            @if($task->model != 'free')
                                                @if($task->model == 'upd')
                                                    {{ __('work_request.object') }}: <a href="{{ route('task.show', $task->id) }}"> {{ $task->object }}</a><br>
                                                @elseif($task->model == 'container_project')
                                                    {{ __('work_request.object') }}: <a href="{{ route('container_project.index').'?need_to_process' }}"> {{ $task->object }}</a><br>
                                                @else
                                                    {{ __('work_request.object') }}: <a href="{{ route($model.'.show',$task->model_id) }}"> {{ $task->object }}</a><br>
                                                @endif
                                            @endif
                                                <b>№: {{ $task->id }}</b><br>
                                                <b>{{ __('work_request.work_request_name') }}:
                                                    @if($task->name != '')
                                                        {{ $task->name }}
                                                    @endif
                                                    @if($task->name !='') <br> @endif
                                                    @nl2br($task->text)
                                                </b>
                                                <br>
                                                {{ __('work_request.current_status') }}: {{$task->status}}<br>
                                                @if($task->info != '')
                                                    {{ __('work_request.Redirected') }}: {{ $task->info }} <br>
                                                @endif
                                                @if($task->deadline != '')
                                                    @if($task->overdue)
                                                        <strong class="text-danger">{{ __('work_request.finish_before') }}: {{ $task->deadline }}</strong>
                                                    @else
                                                        {{ __('work_request.finish_before') }}: {{ $task->deadline }}
                                                    @endif
                                                    <br>
                                                @endif
                                                <div class="mt-3">
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
                                                            <button type="button" class="btn btn-success btn-sm work_request_handler"
                                                                    data-work_request_id="{{ $task->id }}"
                                                                    data-action="done_task"
                                                                    data-reload="true">
                                                                <i class="fas fa-check"></i>
                                                                {{ __('work_request.finish') }}
                                                            </button>
                                                        @endif
                                                    @endif
                                                    @if(!in_array($task->status, ['Отправлена на проверку', 'Выполнена']))
                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-warning btn-sm dropdown-toggle dropdown-icon"
                                                                    data-toggle="dropdown" aria-expanded="false">
                                                                <i class="fas fa-share"></i> {{ __('work_request.redirect') }} &nbsp;</button>
                                                            <div class="dropdown-menu" style="">
                                                                @foreach($users as $user)
                                                                    <a class="dropdown-item cursor-pointer work_request_handler"
                                                                       data-work_request_id="{{ $task->id }}"
                                                                       data-action="transfer_task"
                                                                       data-send_to="Пользователю"
                                                                       data-to_users="{{ $user->id }}">{{ $user->name }}</a>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <a class="btn btn-primary btn-sm" href="{{ route('work_request.show', $task->id) }}">
                                                        {{ __('work_request.go_to_work_request') }}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                @endforeach
                            @else
                                {{ __('work_request.dont_have_active_work_requests') }}
                            @endif
                        </div>
                        <div class="card-footer">
                            {{ $accepted_tasks->links() }}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header cursor-pointer" data-card-widget="collapse">
                            <h3 class="card-title">{{ __('work_request.all_income_work_requests') }}</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="card-body card-comments">
                            @if($income_tasks->isNotEmpty())
                                @foreach($income_tasks as $task)
                                    @php
                                        $model = $task->model;
                                    @endphp
                                    <div class="card-comment">
                                    @if($task->type == 'Система')
                                            <img class="img-circle img-sm" src="/storage/avatars/system.jpg">
                                            <div class="comment-text">
                                                <span class="username">
                                                    {{ __('general.system') }}
                                                    <span class="text-muted float-right">{{ $task->created_at->diffForHumans() }}</span>
                                                </span>
                                                @else
                                                    <img class="img-circle img-sm" src="{{ Storage::url(optional($task->from)->avatar) }}">
                                                    <div class="comment-text">
                                                <span class="username">
                                                    {{ optional($task->from)->name }}
                                                    <span class="text-muted float-right">{{ $task->created_at->diffForHumans() }}</span>
                                                </span>
                                                        @endif
                                                        @if($task->model != 'free')
                                                            @if($task->model == 'upd')
                                                                {{ __('work_request.object') }}: <a href="{{ route('work_request.show', $task->id) }}"> {{ $task->object }}</a><br>
                                                            @elseif($task->model == 'container_project')
                                                                {{ __('work_request.object') }}: <a href="{{ route('container_project.index').'?need_to_process' }}"> {{ $task->object }}</a><br>
                                                            @else
                                                                {{ __('work_request.object') }}: <a href="{{ route($model.'.show',$task->model_id) }}"> {{ $task->object }}</a><br>
                                                            @endif
                                                        @endif
                                                        <b>{{ __('work_request.number') }}: {{ $task->id }}</b><br>
                                                        <b>{{ __('work_request.work_request_name') }}:
                                                            @if($task->name != '')
                                                                {{ $task->name }}
                                                            @endif
                                                            @if($task->name !='') <br> @endif
                                                            @nl2br($task->text)
                                                        </b>
                                                        <br><br>
                                                        @if($task->deadline != '')
                                                            @if($task->overdue)
                                                                <strong class="text-danger">{{ __('work_request.finish_before') }}: {{ $task->deadline }}</strong>
                                                            @else
                                                                {{ __('work_request.finish_before') }}: {{ $task->deadline }}
                                                            @endif
                                                            <br>
                                                        @endif
                                                        {{ __('work_request.responsible') }}: {{ $task->send_to }}
                                                        @if($task->info != '')
                                                            ({{ __('work_request.redirected') }} {{ $task->info }})
                                                        @endif
                                                        @if(!empty($task->additional_users))
                                                            <br>
                                                            {{ __('work_request.co-workers') }}:
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
                                                        <div class="mt-3">
                                                            <button type="button"
                                                                    class="btn btn-success btn-sm work_request_handler"
                                                                    data-work_request_id="{{ $task->id }}"
                                                                    data-action="get_task"
                                                                    data-redirect_to_task="true">
                                                                <i class="fas fa-check"></i> {{ __('work_request.accept') }}
                                                            </button>
                                                            <div class="btn-group">
                                                                <button type="button"
                                                                        class="btn btn-warning btn-sm dropdown-toggle dropdown-icon"
                                                                        data-toggle="dropdown"
                                                                        aria-expanded="false">
                                                                    <i class="fas fa-share"></i> {{ __('work_request.redirect') }} &nbsp;</button>
                                                                <div class="dropdown-menu" style="">
                                                                    @foreach($users as $user)
                                                                        <a class="dropdown-item cursor-pointer work_request_handler"
                                                                           data-action="transfer_task"
                                                                           data-work_request_id="{{ $task->id }}"
                                                                           data-send_to="Пользователю"
                                                                           data-to_users="{{ $user->id }}">{{ $user->name }}</a>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                            <a class="btn btn-primary btn-sm" href="{{ route('work_request.show', $task->id) }}">
                                                                {{ __('work_request.go_to_work_request') }}
                                                            </a>
                                                        </div>
                                                    </div>
                                            </div>
                                @endforeach
                            @else
                                {{ __('work_request.no_income_work_requests') }}
                            @endif
                        </div>
                        <div class="card-footer">
                            {{ $income_tasks->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header cursor-pointer" data-card-widget="collapse">
                        <h3 class="card-title">{{ __('work_request.send_on_approval') }}</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="card-body card-comments">
                        @if($send_for_approval->isNotEmpty())
                            @foreach($send_for_approval as $task)
                                @php
                                    $model = $task->model;
                                @endphp
                                <div class="card-comment">
                                @if($task->type == 'Система')
                                    <img class="img-circle img-sm" src="/storage/avatars/system.jpg">
                                    <div class="comment-text">
                                        <span class="username">
                                            {{ __('general.system') }}
                                            <span class="text-muted float-right">{{ $task->created_at->diffForHumans() }}</span>
                                        </span>
                                            @else
                                                <img class="img-circle img-sm" src="{{ Storage::url(optional($task->from)->avatar) }}">
                                                <div class="comment-text">
                                        <span class="username">
                                            {{ optional($task->from)->name }}
                                            <span class="text-muted float-right">{{ $task->created_at->diffForHumans() }}</span>
                                        </span>
                                        @endif
                                        @if($task->model != 'free')
                                            @if($task->model == 'upd')
                                                {{ __('work_request.object') }}: <a href="{{ route('work_request.show', $task->id) }}"> {{ $task->object }}</a><br>
                                            @elseif($task->model == 'container_project')
                                                {{ __('work_request.object') }}: <a href="{{ route('container_project.index').'?need_to_process' }}"> {{ $task->object }}</a><br>
                                            @else
                                                {{ __('work_request.object') }}: <a href="{{ route($model.'.show',$task->model_id) }}"> {{ $task->object }}</a><br>
                                            @endif
                                        @endif
                                        <b>№: {{ $task->id }}</b><br>
                                        <b>{{ __('work_request.work_request_name') }}:
                                            @if($task->name != '')
                                                {{ $task->name }}
                                            @endif
                                            @if($task->name !='') <br> @endif
                                            @nl2br($task->text)
                                        </b>
                                        <br>
                                        {{ __('work_request.current_status') }}: {{$task->status}}<br>
                                        @if($task->info != '')
                                            {{ __('work_request.Redirected') }}: {{ $task->info }} <br>
                                        @endif
                                        @if($task->deadline != '')
                                            @if($task->overdue)
                                                <strong class="text-danger">{{ __('work_request.finish_before') }}: {{ $task->deadline }}</strong>
                                            @else
                                                {{ __('work_request.finish_before') }}: {{ $task->deadline }}
                                            @endif
                                            <br>
                                        @endif
                                        <div class="mt-3">
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
                                                    <button type="button" class="btn btn-success btn-sm work_request_handler"
                                                            data-work_request_id="{{ $task->id }}"
                                                            data-action="done_task"
                                                            data-reload="true">
                                                        <i class="fas fa-check"></i>
                                                        {{ __('work_request.finish') }}
                                                    </button>
                                                @endif
                                            @endif
                                            @if(!in_array($task->status, ['Отправлена на проверку', 'Выполнена']))
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-warning btn-sm dropdown-toggle dropdown-icon"
                                                            data-toggle="dropdown" aria-expanded="false">
                                                        <i class="fas fa-share"></i> {{ __('work_request.redirect') }} &nbsp;</button>
                                                    <div class="dropdown-menu" style="">
                                                        @foreach($users as $user)
                                                            <a class="dropdown-item cursor-pointer work_request_handler"
                                                               data-work_request_id="{{ $task->id }}"
                                                               data-action="transfer_task"
                                                               data-send_to="Пользователю"
                                                               data-to_users="{{ $user->id }}">{{ $user->name }}</a>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                            <a class="btn btn-primary btn-sm" href="{{ route('work_request.show', $task->id) }}">
                                                {{ __('work_request.go_to_work_request') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                {{ __('work_request.you_dont_have_work_requests_send_to_approval') }}
                            @endif
                        </div>
                        <div class="card-footer">
                            {{ $send_for_approval->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
</section>
@endsection
