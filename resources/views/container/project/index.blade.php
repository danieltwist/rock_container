@extends('layouts.project')
@section('title', __('container.project_all_container_projects'))
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">
                        @if(isset($_GET['need_to_process']))
                            {{ __('container.project_need_to_process') }}
                        @else
                            {{ __('container.project_all_container_projects') }}
                        @endif
                    </h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @include('layouts.info_block')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('container.project_all_container_projects_list') }}</h3>
                </div>
                <div class="card-body">
                    @include('container.project.table_all')
                </div>
            </div>
        </div>
    </section>
@endsection
