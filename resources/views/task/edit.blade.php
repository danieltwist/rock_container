@extends('layouts.project')
@section('title', __('task.edit_task'))
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">{{ __('task.edit_task') }}</h1>
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
                            <h3 class="card-title">{{ __('general.task') }} №{{ $task->id }}</h3>
                        </div>
                        <form action="{{ route('task.update', $task->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                @livewire('createtask',['task_id' => $task->id, 'selectedModel' => 'invoice' ])
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">{{ __('general.update') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
