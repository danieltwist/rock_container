@extends('layouts.project')
@section('title', __('user.my_profile'))
@section('content')
    <div class="content-header" xmlns="http://www.w3.org/1999/html">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('user.my_profile') }}</h1>
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
                        <div class="card-header">
                            <h3 class="card-title">{{ __('user.change_my_info') }}</h3>
                        </div>
                        <form action="{{ route('update_profile') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label>{{ __('user.password') }}</label>
                                    <select class="form-control" name="type" id="user_edit_change_password">
                                        <option value="dont_change_password">{{ __('user.dont_change_password') }}</option>
                                        <option value="change_password">{{ __('user.change_password') }}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="name">{{ __('user.name') }}</label>
                                    <input type="text" class="form-control" name="name" placeholder="{{ __('user.name') }}"
                                           value="{{ $user->name }}">
                                </div>
                                <div class="form-group">
                                    <label for="email">E-mail</label>
                                    <input type="email" class="form-control" name="email" placeholder="E-mail"
                                           value="{{ $user->email }}">
                                </div>
                                <div class="form-group" id="user_edit_password_field" style="display: none;">
                                    <label for="password">{{ __('user.password') }}</label>
                                    <input type="password" class="form-control" name="password" placeholder="{{ __('user.password') }}">
                                </div>
                                <div class="form-group">
                                    <label for="name">{{ __('user.birthday') }}</label>
                                    <input type="text" class="form-control date_input" name="birthday" placeholder="{{ __('user.birthday') }}"
                                           value="{{ !is_null($user->birthday) ? $user->birthday->format('d.m.Y') : '' }}">
                                </div>
                                <div class="form-group">
                                    <label>{{ __('user.role') }}</label>
                                    <input type="text" class="form-control" value="{{ $user->role }}" disabled>
                                </div>
                                <div class="form-group">
                                    <label>{{ __('user.notifications') }}</label>
                                    <select class="form-control" name="notification_channel">
                                        <option value="Везде" {{ $user->notification_channel == 'Везде' ? 'selected' : ''}}>{{ __('user.notification_all') }}</option>
                                        <option value="Система" {{ $user->notification_channel == 'Система' ? 'selected' : ''}}>{{ __('user.system') }}</option>
                                        <option value="Telegram" {{ $user->notification_channel == 'Telegram' ? 'selected' : ''}}>Telegram</option>
                                    </select>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('general.update') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('user.upload_avatar') }}</h3>
                        </div>
                        <form action="{{ route('upload_avatar') }}" method="post" enctype="multipart/form-data">
                            @csrf
                        <div class="card-body">
                                <div class="form-group">
                                    <input name="avatar" type="file" class="file">
                                </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">{{ __('user.upload_avatar') }}</button>
                        </div>
                        </form>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('user.language') }}</h3>
                        </div>
                        <form action="{{ route('change_language') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label>{{ __('user.choose_language') }}</label>
                                    <select class="form-control" name="language">
                                        <option value="ru" {{ $user->language == 'ru' ? 'selected' : ''}}>{{ __('user.language_ru') }}</option>
                                        <option value="cn" {{ $user->language == 'cn' ? 'selected' : ''}}>{{ __('user.language_cn') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">{{ __('user.change_language') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('user.link_telegram') }}</h3>
                            @if(!is_null($user->telegram_chat_id))
                                <div class="card-tools">
                                    <form action="{{ route('unlink_telegram_account') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-default btn-xs">{{ __('user.unlink_telegram') }}</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                        <form action="{{ route('link_telegram_account') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label>{{ __('user.telegram_user_name') }}</label>
                                    <input type="text" class="form-control" name="telegram_login" placeholder="{{ __('user.telegram_user_name') }}"
                                           value="{{ $user->telegram_login }}">
                                </div>
                                <div class="form-group">
                                    <label>{{ __('user.telegram_chat_id') }}</label>
                                    <input type="text" class="form-control" value="{{ !is_null($user->telegram_chat_id) ? $user->telegram_chat_id : __('user.account_not_linked') }}" disabled>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('user.link_account') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
