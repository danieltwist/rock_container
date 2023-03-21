@extends('layouts.project')
@section('title', __('user.all_users'))
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                        <h1 class="m-0">{{ __('user.all_users') }}</h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @include('layouts.info_block')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('user.users_list') }}</h3>
                </div>
                <div class="card-body">
                    <table class="table table-striped" id="users_table">
                        <thead>
                        <tr>
                            <th style="width: 1%">
                                #
                            </th>
                            <th style="width: 15%">
                                {{ __('user.name') }}
                            </th>
                            <th style="width: 10%">
                                E-mail / {{ __('user.folder_on_yd') }}
                            </th>
                            <th style="width: 10%">
                                {{ __('user.register_date') }}
                            </th>
                            <th style="width: 20%">
                                {{ __('user.role') }}
                            </th>
                            <th style="width: 20%">
                                {{ __('general.actions') }}
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>
                                    {{ $user->id }}
                                </td>
                                <td>
                                    <a class="text-dark" href="{{ route('get_user_statistic', $user->id) }}">{{ $user->name }}</a>
                                     <br>
                                    <small>
                                        {{ $user->position }}
                                        <br>{{ $user->birthday != '' ? __('user.birthday').': '.Date::parse($user->birthday)->format('j F Y г.') : '' }}
                                    </small>
                                </td>
                                <td>
                                    {{ $user->email }}<br>
                                    <small>{{ $user->folder_on_yandex_disk }}</small>
                                </td>
                                <td>
                                    {{ $user->created_at }}
                                </td>
                                <td>
                                    <a class="cursor-pointer" data-toggle="collapse" data-target="#collapsePermissions{{$user->id}}" aria-expanded="false" aria-controls="collapseExample">
                                        {{ $user->roles->pluck('ru_name')[0] }}
                                    </a>

                                    <div class="collapse mt-2" id="collapsePermissions{{$user->id}}">
                                        <div class="card card-body">
                                            @foreach($user->getAllPermissions() as $permission)
                                                <li>@include('user.permissions_switch', ['permission' => $permission['ru_name']])</li>
                                            @endforeach
                                        </div>
                                    </div>

                                </td>
                                <td class="project-actions">
                                    @can('edit users')
                                        <a class="btn btn-app bg-indigo" href="{{ route('edit_user', $user->id) }}">
                                            <i class="fas fa-pencil-alt">
                                            </i>
                                            {{ __('general.change') }}
                                        </a>
                                    @endcan
                                    @can('remove users')
                                        <form class="button-delete-inline" action="{{ route('delete_user', $user->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-app bg-danger delete-btn">
                                                <i class="fas fa-trash">
                                                </i>
                                                {{ __('general.remove') }}
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
