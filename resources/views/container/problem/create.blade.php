@extends('layouts.project')
@section('title', __('container.problem_with_container'))
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('container.problem_with_container') }} {{ $container->name }}</h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @include('layouts.info_block')
            <form action="{{ route('container_problem.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="container_id" value="{{ $container->id }}">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('container.info_about_problem') }}</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="problem">{{ __('container.problem_type') }}</label>
                        <input type="text" class="form-control" name="problem" placeholder="{{ __('container.problem_type') }}" value="Поврежден" required>
                    </div>
                    <div class="form-group">
                        <label for="who_fault">{{ __('container.problem_who_fault') }}</label>
                        <input type="text" class="form-control" name="who_fault" placeholder="{{ __('container.problem_who_fault') }}">
                    </div>
                    <div class="form-group">
                        <label for="problem_photos">{{ __('container.problem_photos') }}</label>
                        <input type="file" class="form-control-file" name="problem_photos[]" multiple>
                    </div>
                    <div class="form-group">
                        <label>{{ __('general.additional_info') }}</label>
                        <textarea class="form-control" rows="3" name="additional_info" placeholder="{{ __('general.additional_info') }}"></textarea>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">{{ __('general.save') }}</button>
                </div>
            </div>
            </form>
        </div>
    </section>
@endsection
