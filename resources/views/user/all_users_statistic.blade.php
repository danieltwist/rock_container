@extends('layouts.project')

@section('title', __('user.users_statistic'))

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">{{ __('user.users_with_projects_statistic') }}</h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @include('layouts.info_block')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('user.users_with_projects') }}</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($users_with_projects as $user)
                            <div class="col-md-3">
                                <div class="card card-{{ $user->user_class }}">
                                    <div class="card-header">
                                        <h3 class="card-title">{{ $user->name }}</h3>
                                    </div>
                                    <div class="card-body">
                                        {{ __('user.active_projects') }}: {{ $user->stat['active_projects_count'] }}<br>
                                        {{ __('user.active_projects_profit') }}: {{ number_format($user->stat['active_projects_profit'], 0, '.', ' ') }}р.<br><br>
                                        {{ __('user.active_projects_profit') }}: {{ $user->stat['finished_projects_count'] }}<br>
                                        {{ __('user.active_projects_profit') }}: {{ number_format($user->stat['finished_projects_profit'], 0, '.', ' ') }}р.
                                    </div>
                                    <div class="card-footer">
                                        <a href="{{ route('get_user_statistic', $user->id) }}" class="btn btn-default btn-block">
                                            {{ __('user.user_statistic') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
