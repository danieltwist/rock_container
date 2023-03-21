@extends('layouts.project')
@section('title', __('task.tasks_list'))
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">{{ __('task.all_tasks') }}</h1>
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
                        <div class="card-header cursor-pointer" data-card-widget="collapse">
                            <h3 class="card-title">{{ __('task.all_tasks_in_system') }}</h3>
                        </div>
                        <div class="card-body">
                            @if($tasks->isNotEmpty())
                                @include('task.task_filters')
                                <div class="mt-4">
                                    @include('task.table.task_ajax_table')
                                </div>
                            @else
                                {{ __('task.no_tasks_in_system_now') }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
