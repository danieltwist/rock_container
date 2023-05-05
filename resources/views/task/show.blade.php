@extends('layouts.project')
@section('title', __('task.view_task'))
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">{{ __('task.view_task') }} №{{ $task->id }} {{ $task->name }} {{ is_null($task->deleted_at) ?: '(удалена)' }}
                        <a href="{{ route('task.index') }}" class="btn btn-default">
                            {{ __('task.all_tasks') }}
                        </a>
                    </h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @include('layouts.info_block')
            @if(in_array($role,['super-admin','director']) || in_array(auth()->user()->id, $task->have_access))
                <div class="row">
                    <div class="col-md-6">
                        <div class="card card-primary card-outline card-widget">
                            <div class="card-header">
                                <div class="user-block">
                                @if($task->type == 'Система')
                                        <img class="avatar elevation-2" src="/storage/avatars/system.jpg">
                                        <span class="username">{{ __('general.system') }}</span>
                                        <span class="description">{{ $task->created_at->format('d.m.Y H:i:s') }}</span>
                                    @else
                                        <img class="avatar elevation-2"
                                             src="{{ Storage::url(optional($task->from)->avatar) }}">
                                        <span class="username">{{ optional($task->from)->name }}</span>
                                        <span class="description">{{ $task->created_at->format('d.m.Y H:i:s') }}</span>
                                    @endif
                                </div>
                                <div class="card-tools">
                                    <small class="text-bold">{{$task->status}}</small>
                                </div>
                            </div>
                            <div class="card-body" id="main">
                                @include('task.ajax.main')
                            </div>
                        </div>
                        <div class="card card-primary card-outline card-tabs">
                            <div class="dropdown">
                                <div class="dropdown-menu" id="users_send_to">
                                    @foreach($task->users as $user)
                                        <a class="dropdown-item cursor-pointer chat-notify-user"
                                           data-notify_user="{{ $user->id }}">{{ $user->name }}</a>
                                    @endforeach
                                </div>
                            </div>
                            <div class="card-header p-0 pt-1 border-bottom-0">
                                <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="custom-tabs-three-home-tab" data-toggle="pill"
                                           href="#comments" role="tab" aria-controls="custom-tabs-three-home"
                                           aria-selected="true">{{ __('task.comments') }}</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="custom-tabs-three-profile-tab" data-toggle="pill"
                                           href="#files" role="tab" aria-controls="custom-tabs-three-profile"
                                           aria-selected="false">{{ __('task.files') }}</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="custom-tabs-three-profile-tab" data-toggle="pill"
                                           href="#add-file" role="tab" aria-controls="custom-tabs-three-profile"
                                           aria-selected="false">{{ __('task.add_file') }}</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="custom-tabs-three-profile-tab" data-toggle="pill"
                                           href="#history" role="tab" aria-controls="custom-tabs-three-profile"
                                           aria-selected="false">{{ __('task.history') }}</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content" id="custom-tabs-three-tabContent">
                                    <div class="tab-pane fade active show" id="comments" role="tabpanel"
                                         aria-labelledby="custom-tabs-three-home-tab">
                                        <div class="card-footer card-comments" id="chat">
                                        @include('task.ajax.chat')
                                        </div>
                                        <div class="card-footer">
                                            <form action="#" method="post">
                                                <img class="img-fluid img-circle img-sm"
                                                     src="{{ $current_user_avatar }}" alt="Alt Text">
                                                <div class="img-push">
                                                    <div class="input-group input-group-sm">
                                                        <textarea class="form-control custom-control" rows="5" name="comment" id="comment"></textarea>
                                                    </div>
                                                    <a class="cursor-pointer"
                                                       data-toggle="collapse"
                                                       data-target="#collapse_notify_users"
                                                       aria-expanded="false"
                                                       aria-controls="collapseExample">
                                                        <i class="fa fa-angle-down"></i>
                                                        {{ __('task.notify_users') }}
                                                    </a>
                                                    <div class="collapse mt-2" id="collapse_notify_users">
                                                        <select class="select2" multiple="multiple" data-placeholder="{{ __('task.choose_user') }}" id="comment_notify_users" name="notify_users[]" style="width: 100%;">
                                                            @foreach($task->users as $user)
                                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group mt-3">
                                                    <button type="button"
                                                            id="task_handler_submit"
                                                            class="btn btn-primary btn-flat task_handler_comments float-right"
                                                            data-task_id="{{ $task->id }}"
                                                            data-action="add_chat_record">
                                                        {{ __('general.send') }}
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="files" role="tabpanel"
                                         aria-labelledby="custom-tabs-three-profile-tab">
                                        @include('task.ajax.files')
                                    </div>
                                    <div class="tab-pane fade" id="add-file" role="tabpanel"
                                         aria-labelledby="custom-tabs-three-profile-tab">
                                        <form action="{{ route('upload_file_to_task') }}" method="post"
                                              enctype="multipart/form-data">
                                            @csrf
                                            @method('POST')
                                            <input type="hidden" name="task_id" value="{{$task->id}}">
                                            <div class="form-group">
                                                <label>{{ __('task.comment') }}</label>
                                                <textarea class="form-control" name="comment"
                                                          placeholder="{{ __('task.enter_comment_text') }}"></textarea>
                                            </div>
                                            <div class="form-group">
                                                <input type="file" class="form-control-file" name="files[]" multiple>
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-sm"
                                                    data-action='{"update_div":{"div_id":"files"},"update_div_chat":{"div_id":"chat"},"reset_form":{"need_reset": "true"}}'>
                                                {{ __('general.upload') }}
                                            </button>
                                        </form>
                                    </div>
                                    <div class="tab-pane fade" id="history" role="tabpanel"
                                         aria-labelledby="custom-tabs-three-profile-tab">
                                        @if(!is_null($task->history))
                                            <div class="card-footer card-comments">
                                                @foreach(unserialize($task->history) as $history)
                                                    <div class="card-comment">
                                                        <img class="img-circle img-sm"
                                                             src="{{ Storage::url(optional(userInfo($history['user']))->avatar) }}"
                                                             alt="User Image">
                                                        <div class="comment-text">
                                                            <span class="username">
                                                                {{ optional(userInfo($history['user']))->name }}
                                                                <span
                                                                    class="text-muted float-right">{{ $history['date'] }}</span>
                                                            </span>
                                                            {{ $history['text'] }}
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            {{ __('task.history_empty') }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card card-primary card-outline card-widget">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('task.task_users') }}</h3>
                                <div class="card-tools">
                                    <span class="badge badge-secondary">{{ __('task.total_task_users') }}: {{ count($task->to_users) }}</span>
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="task_users">
                                    @include('task.ajax.users')
                                </div>
                                <div class="mt-3">
                                    @if(in_array($role,['super-admin','director']) || in_array(auth()->user()->id, $task->to_users))
                                        @if($task->status == 'Ожидает выполнения' && in_array(auth()->user()->id, $task->to_users))
                                            <button type="button" class="btn btn-success btn-sm task_handler"
                                                    data-task_id="{{ $task->id }}"
                                                    data-action="get_task">
                                                <i class="fas fa-check"></i>
                                                {{ __('task.accept') }}
                                            </button>
                                        @endif
                                        @if(!in_array($task->status, ['Отправлена на проверку', 'Выполнена']))
                                            <div class="btn-group">
                                                <button type="button"
                                                        class="btn btn-warning btn-sm dropdown-toggle dropdown-icon"
                                                        data-toggle="dropdown"
                                                        aria-expanded="false">
                                                    <i class="fas fa-share"></i> {{ __('task.redirect') }}
                                                </button>
                                                <div class="dropdown-menu">
                                                    @foreach($task->users as $user)
                                                        <a class="dropdown-item cursor-pointer task_handler"
                                                           data-action="transfer_task"
                                                           data-task_id="{{ $task->id }}"
                                                           data-send_to="Пользователю"
                                                           data-reload="true"
                                                           data-to_users="{{ $user->id }}">{{ $user->name }}
                                                        </a>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @else
                                            @if(in_array($role,['super-admin','director']) || auth()->user()->id == $task->from_user_id || auth()->user()->id == $task->accepted_user_id)
                                                <button type="button" class="btn btn-primary btn-sm task_handler"
                                                        data-task_id="{{ $task->id }}"
                                                        data-action="reload_task">
                                                    <i class="fas fa-redo"></i>
                                                    {{ __('task.reload_task') }}
                                                </button>
                                            @endif
                                        @endif
                                    @endif
                                    @if(($task->status == 'Отправлена на проверку') && (in_array($role,['super-admin','director']) || auth()->user()->id == $task->from_user_id))
                                        <button type="button" class="btn btn-success btn-sm task_handler"
                                                data-task_id="{{ $task->id }}"
                                                data-action="confirm_done_task">
                                            <i class="fas fa-check"></i>
                                            {{ __('task.confirm_finish') }}
                                        </button>
                                    @endif
                                    @if(in_array($role,['super-admin','director']) || auth()->user()->id == $task->from_user_id || auth()->user()->id == $task->accepted_user_id)
                                        <div class="btn-group">
                                            <button type="button"
                                                    class="btn btn-info btn-sm dropdown-toggle dropdown-icon"
                                                    data-toggle="dropdown"
                                                    aria-expanded="false"
                                                    data-reload="false">
                                                <i class="fas fa-plus"></i> {{ __('task.add') }}
                                            </button>
                                            <div class="dropdown-menu" style="">
                                                @foreach($task->users as $user)
                                                    <a class="dropdown-item cursor-pointer task_handler"
                                                       data-action="add_user"
                                                       data-task_id="{{ $task->id }}"
                                                       data-send_to="Пользователю"
                                                       data-reload="false"
                                                       data-to_users="{{ $user->id }}">{{ $user->name }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                @include('error.dont_have_access')
            @endif
        </div>
    </section>
@endsection
