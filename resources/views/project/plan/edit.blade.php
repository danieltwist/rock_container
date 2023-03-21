@extends('layouts.project')
@section('title', __('project.edit_plan_for_project'))
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">{{__('project.edit_plan_for_project')}} {{ $project->name }}
                        <a href="{{ route('project.show', $project['id']) }}" class="btn btn-default">
                            {{ __('project.back_to_project') }}
                        </a>
                    </h1>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" value="{{ $project->id }}" id="project_id">
    <section class="content">
        <div class="container-fluid">
            @include('layouts.info_block')
            @if (canWorkWithProject($project->id))
                @can ('work with projects')
                    <div id="success_ajax"></div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __('project.choose_stages') }}</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse"
                                                title="Collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <h5>{{ __('project.choose_stage_and_move_right') }}</h5>
                                    <ul class="list-group mt-4" id="list_of_all_items">
                                        @foreach($block_items as $block_item)
                                            <li class="list-group-item cursor-pointer"
                                                style="cursor: move;">{{ $block_item->name }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __('project.chosen_stages') }}</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse"
                                                title="Collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="callout callout-danger">
                                        <h5>{{ __('project.attention') }}</h5>
                                        <p>{{ __('project.plan_attention_info') }}</p>
                                    </div>
                                    <h5>{{ __('project.move_chosen_stages_here') }}</h5>
                                    <ul class="list-group mt-4" id="choosed_items">
                                    </ul>
                                    <h5 class="mt-4">{{ __('project.current_project_plan') }}:</h5>
                                    <ul class="list-group mt-4">
                                        @foreach($blocks as $block)
                                            <form class="inline-block" action="{{ route('block.destroy', $block->id) }}"
                                                  method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <li class="list-group-item">{{ $block->name }}
                                                    <button type="submit"
                                                            class="btn btn-danger btn-sm float-right delete-btn">
                                                        {{ __('general.remove') }}
                                                    </button>
                                                </li>
                                            </form>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" id="save_project_plan" class="btn btn-primary float-right">
                                        {{ __('project.save_project_plan') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            @include('project.plan.create_new_block_card')
                        </div>
                    </div>
                @endcan
            @else
                @include('error.dont_have_access')
            @endif
        </div>
    </section>
@endsection


