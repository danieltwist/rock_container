@extends('layouts.project')
@section('title', __('container.container_group'))
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">
                        {{ __('container.container_group') }} {{$container_group->name}} {{ __('container.for_project') }} {{optional($container_group->project)->name}}
                    </h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @include('layouts.info_block')
            @php
                $group = $container_group;
            @endphp
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('container.info_about_group') }}</h3>
                </div>
                <div class="card-body">
                    {{ __('container.using_in_project') }}: <a href="{{ route('project.show', optional($container_group->project)->id)}}">{{ optional($container_group->project)->name }}</a><br>
                    {{ __('container.start_using') }}: {{ $container_group->start_date }}<br>
                    {{ __('container.border_date') }}: {{ $container_group->border_date }}<br>
                    {{ __('container.containers_list') }}: {{ $containers_list }}
                    <div id="project_containers_group_table_{{$container_group->id}}"
                         class="container_groups_project"
                         data-type="group"
                         data-group_id="{{ $container_group->id }}">
                        @include('project.layouts.containers_table', ['filter' => 'container_group'])
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
