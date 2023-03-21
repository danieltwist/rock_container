@extends('layouts.project')
@section('title', __('user.edit_user'))
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">{{ __('user.edit_user') }} {{ $user->name }}</h1>
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
                            <h3 class="card-title">{{ __('user.edit_user_info') }}</h3>
                        </div>
                        <form action="{{ route('update_user', $user->id) }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Пароль</label>
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
                                    <input type="password" class="form-control" name="{{ __('user.password') }}" placeholder="Пароль">
                                </div>
                                <div class="form-group">
                                    <label for="name">{{ __('user.position') }}</label>
                                    <input type="text" class="form-control to_uppercase" name="position" placeholder="{{ __('user.position') }}"
                                           value="{{ $user->position }}">
                                </div>
                                <div class="form-group">
                                    <label for="name">{{ __('user.folder_on_yandex_disk') }}</label>
                                    <input type="text" class="form-control"
                                           name="folder_on_yandex_disk" placeholder="{{ __('user.folder_on_yandex_disk') }}"
                                           value="{{ $user->folder_on_yandex_disk }}">
                                </div>
                                <div class="form-group">
                                    <label for="name">{{ __('user.birthday') }}</label>
                                    <input type="text" class="form-control date_input" name="birthday" placeholder="{{ __('user.birthday') }}"
                                           value="{{ $user->birthday }}">
                                </div>
                                <div class="form-group">
                                    <label>{{ __('user.role') }}</label>
                                    <select class="form-control" name="role">
                                        @foreach($roles as $role)
                                            <option
                                                value="{{ $role->name }}" {{ $role->name == $user->role ? 'selected' : '' }}>{{ $role->ru_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('user.edit_user_info') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('user.access_level') }}: {{ $user->roles->pluck('ru_name')[0] }}</h3>
                        </div>
                        <div class="card-body">
                            @if ($user->roles->pluck('name')[0]=='user')
                                {{ __('user.this_user_have_no_access_to_system') }}
                            @else
                                {{ __('user.available_permissions_to_user') }}:
                                <div class="form-group clearfix">
                                    @foreach($all_permissions as $permission)
                                        <div class="icheck-primary">
                                            <input type="checkbox"
                                                   class="change_user_permission"
                                                   data-user-id="{{ $user->id }}"
                                                   data-permission-name="{{ $permission->name }}"
                                                   data-permission-ru-name="{{ $permission->ru_name }}"
                                                   id="{{$permission->name}}"
                                                    @foreach($user->getAllPermissions() as $user_permission)
                                                        {{ $permission->name == $user_permission->name ? 'checked' : '' }}
                                                    @endforeach
                                                    @foreach($role_permissions as $role_permission)
                                                        {{ $permission->name == $role_permission->name ? 'disabled' : '' }}
                                                    @endforeach/>
                                            <label for="{{$permission->name}}">
                                                @include('user.permissions_switch', ['permission' => $permission['ru_name']])
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
