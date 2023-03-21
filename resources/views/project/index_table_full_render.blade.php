@extends('layouts.project')
@section('title',$page_title)
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
                                    <span></span> <i class="fa fa-caret-down"></i>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn btn-default" onclick="sortProjectsByDate();">
                                    <i class="fa fa-search"></i> {{ __('general.upload') }}
                                </button>
                            </div>
                        </div>
                    @endif
                    <div class="mt-4" id="search_results">
                        @include('project.layouts.projects_table_full_render')
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
