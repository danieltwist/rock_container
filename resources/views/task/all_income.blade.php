@extends('layouts.project')
@section('title', __('task.tasks_list'))
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">{{ __('task.all_tasks_with_you') }}</h1>
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
                            <h3 class="card-title">{{ __('task.tasks_list') }}</h3>
                        </div>
                        <div class="card-body">
                            @if($all_income_tasks->isNotEmpty())
                                @include('task.task_filters')
                                <div class="mt-4">
                                    @include('task.table.task_ajax_table')
                                </div>
                            @else
                                {{ __('task.you_dont_have_tasks') }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
