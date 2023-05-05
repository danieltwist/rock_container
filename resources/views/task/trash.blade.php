@extends('layouts.project')
@section('title', __('task.tasks_list'))
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">Удаленные задачи
                        <a href="{{ route('task.index') }}" class="btn btn-default">{{ __('task.all_tasks') }}</a></h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @include('layouts.info_block')
            <div class="row">
                <div class="col-md-12">
                    @if(in_array(Auth::user()->getRoleNames()[0], ['super-admin','director']))
                        <div class="card">
                            <div class="card-header cursor-pointer" data-card-widget="collapse">
                                <h3 class="card-title">Список задач</h3>
                            </div>
                            <div class="card-body">
                                @include('task.task_filters')
                                <div class="mt-4">
                                    @include('task.table.task_ajax_table')
                                </div>
                            </div>
                        </div>
                    @else
                        @include('error.dont_have_access')
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
