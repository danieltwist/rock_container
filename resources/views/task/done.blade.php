@extends('layouts.project')
@section('title', __('task.tasks_list'))
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">{{ __('task.done_tasks') }}
                        <a href="{{ route('task.index') }}" class="btn btn-default">
                            {{ __('task.all_tasks') }}
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
                        <div class="card-header cursor-pointer" data-card-widget="collapse">
                            <h3 class="card-title">{{ __('task.all_done_tasks_by_you') }}</h3>
                        </div>
                        <div class="card-body">
                            @if($done_tasks->isNotEmpty())
                                @include('task.table.task_ajax_table')
                            @else
                                {{ __('task.dont_have_done_tasks_by_you') }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
