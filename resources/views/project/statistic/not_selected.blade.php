@extends('layouts.project')
@section('title', __('project.analytics'))
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            @include('layouts.info_block')
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('project.analytics') }}
                        <a href="{{ route('project.index').'?finished' }}" class="btn btn-default">
                            {{ __('project.back_to_finished_projects') }}
                        </a>
                    </h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
        @include('layouts.info_block')
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('general.error') }}</h3>
                        </div>
                        <div class="card-body">
                            {{ __('general.projects_list_not_chosen') }}<br><br>
                            <a href="{{ route('finished_projects') }}" class="btn btn-default">
                                {{ __('project.back_to_finished_projects') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
