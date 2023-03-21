@extends('layouts.project')
@section('title', __('settings.remove_projects_from_stat'))
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">{{ __('settings.remove_projects_from_stat') }}</h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @include('layouts.info_block')
            <div class="row">
                <div class="col-md-12">
                    <form action="{{ route('remove_from_stat') }}" method="POST">
                        @csrf
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('settings.choose_projects_from_list') }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>{{ __('settings.all_projects') }}</label>
                                <select class="select2" multiple="multiple" data-placeholder="{{ __('settings.all_projects') }}" name="projects[]" style="width: 100%;">
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}" {{ !is_null($project->remove_from_stat) ? 'selected' : '' }}>{{ $project->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                {{ __('general.save') }}
                            </button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection


