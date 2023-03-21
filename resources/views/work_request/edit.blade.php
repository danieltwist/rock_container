@extends('layouts.project')
@section('title', __('work_request.edit_work_request'))
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">{{ __('work_request.edit_work_request') }}
                        <a href="{{ route('work_request.index') }}" class="btn btn-default">
                            {{ __('work_request.all_work_requests') }}
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
                            <h3 class="card-title">{{ __('general.work_request') }} №{{ $work_request->id }}</h3>
                        </div>
                        <form action="{{ route('work_request.update', $work_request->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                @livewire('create-work-request', ['work_request_id' => $work_request->id])
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('general.update') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

