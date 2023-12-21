@extends('layouts.project')
@section('title', $page_title)
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    @if (isset($_GET['client_id']))
                        <h1 class="m-0">{{ __('project.client_projects') }}</h1>
                    @elseif (isset($_GET['user_id']))
                        <h1 class="m-0">{{ __('project.user_projects') }}</h1>
                    @elseif (isset($_GET['draft']))
                        <h1 class="m-0">{{ __('project.draft_projects') }}</h1>
                    @elseif (isset($_GET['active']))
                        <h1 class="m-0">{{ __('project.active_projects') }}</h1>
                    @elseif (isset($_GET['finished']))
                        <h1 class="m-0">{{ __('project.finished_projects') }}</h1>
                    @elseif (isset($_GET['done_unpaid']))
                        <h1 class="m-0">{{ __('project.done_unpaid_projects') }}</h1>
                    @elseif (isset($_GET['trash']))
                        <h1 class="m-0">Удаленные проекты</h1>
                    @elseif (isset($_GET['archive']))
                        <h1 class="m-0">Архив проектов</h1>
                    @else
                        <h1 class="m-0">{{ $page_title }}</h1>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @include('layouts.info_block')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('project.projects_list') }}</h3>
                </div>
                <div class="card-body">
                    @if((in_array($page_title, ['Все проекты', 'Проекты в работе', 'Завершенные проекты'])) && (!isset($_GET['user_id'])) && (!isset($_GET['client_id'])))
                        <div class="row">
                            <div class="col-md-6">
                                <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                    <i class="fa fa-calendar"></i>&nbsp;
                                    <span>Все</span> <i class="fa fa-caret-down"></i>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn btn-default" onclick="sortProjectsByDate();">
                                    <i class="fa fa-search"></i> {{ __('general.upload') }}
                                </button>
{{--                                @if(isset($_GET['finished']))--}}
{{--                                    <form class="button-delete-inline" action="{{ route('get_projects_statistic') }}" method="POST">--}}
{{--                                        @csrf--}}
{{--                                        <input type="hidden" id="data_range" name="data_range" value="all">--}}
{{--                                        <button type="submit" class="btn btn-default d-none" id="get_projects_statistic">--}}
{{--                                            <i class="fas fa-chart-pie"></i>--}}
{{--                                            Аналитика--}}
{{--                                        </button>--}}
{{--                                    </form>--}}
{{--                                @endif--}}
                            </div>
                        </div>
                    @endif
                    <div class="mt-4" id="search_results">
                        @include('project.layouts.projects_table')
                    </div>
                </div>
                @if(in_array($role, ['director', 'accountant', 'super-admin']))
                    <div class="card-footer">
                    <form action="{{ route('projects_export_with_filter_to_excel') }}" id="get_excel_projects" method="GET">
                        @csrf
                        @if (isset($_GET['draft']))
                            @php
                                $parameters = [
                                    'filename' => 'черновики_проектов',
                                    'sorting_type' => 'Черновики проектов',
                                ];
                                $filter = 'draft';
                            @endphp
                        @elseif (isset($_GET['active']))
                            @php
                                $parameters = [
                                    'filename' => 'проекты_в_работе',
                                    'sorting_type' => 'Проекты в работе',
                                ];
                                $filter = 'active';
                            @endphp
                        @elseif (isset($_GET['finished']))
                            @php
                                $parameters = [
                                    'filename' => 'завершенные_проекты',
                                    'sorting_type' => 'Завершенные проекты',
                                ];
                                $filter = 'finished';
                            @endphp
                        @elseif (isset($_GET['done_unpaid']))
                            @php
                                $parameters = [
                                    'filename' => 'завершенные_неоплаченные_проекты',
                                    'sorting_type' => 'Завершенные неоплаченные проекты',
                                ];
                                $filter = 'done_unpaid';
                            @endphp
                        @elseif (isset($_GET['archive']))
                            @php
                                $parameters = [
                                    'filename' => 'архив_проектов',
                                    'sorting_type' => 'Архив проектов',
                                ];
                                $filter = 'archive';
                            @endphp
                        @else
                            @php
                                $parameters = [
                                    'filename' => 'все_проекты',
                                    'sorting_type' => 'Все проекты',
                                ];
                            @endphp
                        @endif
                        @if(isset($filter))
                            <input type="hidden" name="filter" value="{{ $filter }}">
                        @endif
                        <input type="hidden" name="parameters" value="{{ serialize($parameters) }}">
                        <button type="submit" class="btn btn-success download_file_directly"
                                data-action='{"download_file":{"need_download": "true"}}'>
                            <i class="fas fa-file-excel"></i>
                            {{ __('general.export_projects_to_excel') }}
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </section>
@endsection
