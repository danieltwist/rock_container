@extends('layouts.project')
@section('title', __('container.project_container_project'))
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">{{ __('container.project_container_project') }} №{{ $container_project->id }} {{ __('general.for') }} {{optional(optional($container_project->container))->name}}
                        <a href="{{ route('container_project.index') }}" class="btn btn-default">{{ __('container.project_all_container_projects') }}</a>
                        <a href="{{ route('container.show', $container_project->container_id) }}" class="btn btn-default ml-1">{{ __('general.container') }} {{ optional($container_project->container)->name }}</a>
                    </h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
        @include('layouts.info_block')
            <div class="row" id="top_panel">
                @include('container.project.ajax.top_panel')
            </div>
            <div class="row">
                <div class="col-md-8">
                    <div class="timeline">
                        <div class="time-label">
                            <span class="bg-gray">
                                {{ __('container.project_start_place') }} - {{ $container_project->start_place != ''
                                    ? $container_project->start_place
                                    : __('container.project_not_set') }}
                                @if ($container_project->date_departure != '')
                                    - {{ $container_project->date_departure }}
                                @endif
                            </span>
                        </div>
                        <div id="contract_with_terminal">
                            @include('container.project.ajax.contract_with_terminal')
                        </div>
                        <div id="main_project">
                            @include('container.project.ajax.project')
                        </div>
                        <div id="client">
                            @include('container.project.ajax.client')
                        </div>
                        <div id="client_application">
                            @include('container.project.ajax.client_application')
                        </div>
                        <div id="rate_for_client">
                            @include('container.project.ajax.rate_for_client')
                        </div>
                        <div id="snp_for_client">
                            @include('container.project.ajax.snp_for_client')
                        </div>
                        <div id="contract_with_arrival_terminal">
                            @include('container.project.ajax.contract_with_arrival_terminal')
                        </div>
                        <div class="time-label">
                            <span class="bg-gray">
                                {{ __('container.project_place_of_arrival') }} - {{ $container_project->place_of_arrival != ''
                                    ? $container_project->place_of_arrival
                                    : __('container.project_not_set') }}
                                @if ($container_project->date_of_arrival != '')
                                    - {{ $container_project->date_of_arrival }}
                                @endif
                            </span>
                        </div>
                        <div id="inspection_report">
                            @include('container.project.ajax.inspection_report')
                        </div>
                        <div id="repair">
                            @include('container.project.ajax.repair')
                        </div>
                        <div id="moving">
                            @include('container.project.ajax.moving')
                        </div>
                        <div id="expenses">
                            @include('container.project.ajax.expenses')
                        </div>
                        <div id="photos">
                            @include('container.project.ajax.photos')
                        </div>
                        <div class="time-label">
                            @if($container_project->moving != 'Не требуется')
                                <span class="bg-gray">
                                    {{ __('container.project_drop_off_location') }} - {{ $container_project->drop_off_location != ''
                                            ? $container_project->drop_off_location
                                            : __('container.project_not_set') }}
                                </span>
                            @else
                                <span class="bg-gray">
                                    {{ __('container.project_drop_off_location') }} - {{ $container_project->place_of_arrival != ''
                                            ? $container_project->place_of_arrival
                                            : __('container.project_not_set') }}
                                </span>
                            @endif
                            <span class="bg-primary">
                                <a type="button" href="{{ route('container_project.create').'?container_id='.$container_project->container_id }}">
                                    {{ __('container.project_start_next') }}
                                </a>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mt-5">
                    <div class="card" id="status">
                        @include('container.project.ajax.status')
                    </div>
                    <div class="card" id="additional_info">
                        @include('container.project.ajax.additional_info')
                    </div>
                    <div class="card" id="places">
                        @include('container.project.ajax.places')
                    </div>
                    <div class="card" id="dates">
                        @include('container.project.ajax.dates')
                    </div>
                    <div class="card" id="paid">
                        @include('container.project.ajax.paid')
                    </div>
                </div>
            </div>
        </div>
</section>
@endsection

