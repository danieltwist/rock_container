@extends('layouts.project')
@section('title', __('user.create_new_user'))
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">{{ __('user.create_new_user') }}</h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @include('layouts.info_block')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('user.add_user') }}</h3>
                </div>
                <form action="{{ route('store_new_user') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">{{ __('user.name') }}</label>
                            <input type="text" class="form-control to_uppercase" name="name" placeholder="{{ __('user.name') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="name">{{ __('user.position') }}</label>
                            <input type="text" class="form-control to_uppercase" name="position" placeholder="{{ __('user.position') }}">
                        </div>
                        <div class="form-group">
                            <label for="name">{{ __('user.folder_on_yandex_disk') }}</label>
                            <input type="text" class="form-control" name="folder_on_yandex_disk" placeholder="{{ __('user.folder_on_yandex_disk') }}">
                        </div>
                        <div class="form-group">
                            <label for="name">{{ __('user.birthday') }}</label>
                            <input type="text" class="form-control date_input" name="birthday" placeholder="{{ __('user.birthday') }}">
                        </div>
                        <div class="form-group">
                            <label for="email">E-mail</label>
                            <input type="email" class="form-control" name="email" placeholder="E-mail" required>
                        </div>
                        <div class="form-group">
                            <label for="password">{{ __('user.password') }}</label>
                            <input type="text" class="form-control" name="password" placeholder="{{ __('user.password') }}" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('user.role') }}</label>
                            <select class="form-control" name="role" required>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}">{{$role->ru_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            {{ __('user.add_user') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
