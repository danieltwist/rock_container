@extends('layouts.project')
@section('title', __('work_request.work_requests_list'))
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">{{ __('work_request.all_work_requests_with_you') }}
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
                        <div class="card-header cursor-pointer" data-card-widget="collapse">
                            <h3 class="card-title">{{ __('work_request.work_requests_list') }}</h3>
                        </div>
                        <div class="card-body">
                            @if($all_income_tasks->isNotEmpty())
                                @include('work_request.filters')
                                <div class="mt-4">
                                    @include('work_request.table.work_requests_ajax_table')
                                </div>
                            @else
                                {{ __('work_request.you_dont_have_work_requests') }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
