@extends('layouts.project')

@section('title', __('home.home_page'))

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('home.home_page') }}</h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
        @include('layouts.info_block')
{{--            <div class="row">--}}
{{--                <div class="col-lg-3 col-6">--}}
{{--                    <div class="small-box bg-gradient-primary">--}}
{{--                        <div class="inner">--}}
{{--                            <h4>{{ $user->stat['active_projects_count'] }}</h4>--}}
{{--                            <p>{{ __('home.active_projects_count') }}</p>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="col-md-3 col-6">--}}
{{--                    <div class="small-box bg-gradient-navy">--}}
{{--                        <div class="inner">--}}
{{--                            <h4>{{ $user->stat['finished_projects_count'] }}</h4>--}}
{{--                            <p>{{ __('home.this_month_finished_projects_count') }}</p>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="col-md-3 col-6">--}}
{{--                    <div class="small-box bg-gradient-indigo">--}}
{{--                        <div class="inner">--}}
{{--                            <h4>{{ number_format($user->stat['active_projects_profit'], 0, '.', ' ') }}р.</h4>--}}
{{--                            <p>{{ __('home.active_projects_profit') }}</p>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="col-md-3 col-6">--}}
{{--                    <div class="small-box bg-gradient-success">--}}
{{--                        <div class="inner">--}}
{{--                            <h4>{{ number_format($user->stat['finished_projects_profit'], 0, '.', ' ') }}р.</h4>--}}
{{--                            <p>{{ __('home.finished_projects_profit') }}</p>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('home.my_projects') }}</h3>
                </div>
                <div class="card-body">
                    @if($my_projects_count != 0)
                        <div class="projects_homepage" data-type="my_projects">
                            @include('project.layouts.projects_table_ajax_filter', ['filter' => 'my_projects'])
                        </div>
                    @else
                        {{ __('home.no_my_projects') }}
                    @endif
                </div>
                @if($my_projects_count != 0)
                    <div class="card-footer">
                        <form action="{{ route('projects_export_with_filter_to_excel') }}" method="GET">
                            @csrf
                            @php
                                $parameters = [
                                    'filename' => 'все_проекты_в_работе_с_моим_участием',
                                    'sorting_type' => 'Все проекты в работе с участием пользователя '.auth()->user()->name,
                                ];
                            @endphp
                            <input type="hidden" name="filter" value="my_projects">
                            <input type="hidden" name="parameters" value="{{ serialize($parameters) }}">
                            <button type="submit" class="btn btn-success download_file_directly">
                                <i class="fas fa-file-excel"></i>
                                {{ __('general.export_projects_to_excel') }}
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
        @include('project.modals.confirm_invoice')
    </section>
@endsection
