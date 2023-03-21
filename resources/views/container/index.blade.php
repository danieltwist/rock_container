@extends('layouts.project')
@section('title', __('container.all_containers'))
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">
                        @if (isset($_GET['using_now']))
                            {{ __('container.using_now') }}
                            @php
                                $filter = 'using_now';
                            @endphp
                        @elseif (isset($_GET['with_problem']))
                            {{ __('container.with_problem') }}
                            @php
                                $filter = 'with_problem';
                            @endphp
                        @elseif (isset($_GET['own']))
                            {{ __('container.own') }}
                            @php
                                $filter = 'own';
                            @endphp
                        @elseif (isset($_GET['rent']))
                            {{ __('container.in_rent') }}
                            @php
                                $filter = 'rent';
                            @endphp
                        @elseif (isset($_GET['archive']))
                            {{ __('container.in_archive') }}
                            @php
                                $filter = 'archive';
                            @endphp
                        @elseif (isset($_GET['free']))
                            {{ __('container.free') }}
                            @php
                                $filter = 'free';
                            @endphp
                        @else
                            {{ __('container.all_containers') }}
                            @php
                                $filter = 'all';
                            @endphp
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
                    <h3 class="card-title">{{ __('container.containers_list') }}</h3>
                    @if($filter != 'with_problem')
                        <div class="card-tools">
                            <form action="{{ route('containers_download') }}" method="POST">
                                @csrf
                                <input type="hidden" name="filter" value="{{ $filter }}">
                                <button type="submit" class="btn btn-block btn-success btn-xs download_file_directly"
                                        data-action='{"download_file":{"need_download": "true"}}'>
                                    <i class="fas fa-file-excel"></i>
                                    {{ __('general.export_to_excel') }}
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    <table class="table table-striped" id="containers_ajax_table">
                        <thead>
                        <tr>
                            <th style="width: 1%">
                                #
                            </th>
                            <th style="width: 20%">
                                {{ __('container.number') }}
                            </th>
                            <th style="width: 20%">
                                {{ __('container.using_table') }}
                            </th>
                            <th>
                                {{ __('container.using_conditions_table') }}
                            </th>
                            <th>
                                {{ __('container.places_tables') }}
                            </th>
                            <th style="width: 25%">
                                {{ __('general.actions') }}
                            </th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
