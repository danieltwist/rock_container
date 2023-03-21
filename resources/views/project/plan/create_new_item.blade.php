@extends('layouts.project')

@section('title', __('project.edit_project_plan_items'))

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">{{ __('project.edit_project_plan_items') }}</h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @include('layouts.info_block')
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('project.project_plan_items') }}</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <ul class="list-group mt-4">
                                @foreach($block_items as $block_item)
                                    <li class="list-group-item">
                                        <div class="form-group">
                                            <label>{{ __('project.name') }}</label>
                                            <input type="text" class="form-control" placeholder="{{ __('project.name') }}" value="{{ $block_item->name }}">
                                        </div>
                                        <div class="form-group">
                                            <form class="inline-block" action="{{ route('block_items.destroy', $block_item->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm delete-btn">
                                                    {{ __('general.remove') }}
                                                </button>
                                            </form>
                                        </div>
                                        <form class="inline-block" action="{{ route('block_items.update', $block_item->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="form-group">
                                                <label>{{ __('project.available_block_statuses') }}</label>
                                                <textarea class="form-control" rows="3" name="statuses"
                                                          placeholder="{{ __('project.available_block_statuses') }}">{{ $block_item->statuses }}</textarea>
                                            </div>
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    {{ __('project.update_statuses') }}
                                                </button>
                                            </div>
                                        </form>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    @include('project.plan.create_new_block_card')
                </div>
            </div>
        </div>
    </section>
@endsection


