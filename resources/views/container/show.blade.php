@extends('layouts.project')

@section('title', __('container.show_container'))

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('general.container') }} {{ $container->name }}
                        @if (!is_null($container->archive))
                            <a href="{{ route('containers_extended_archive') }}" class="btn btn-default">
                                {{ __('container.all_archive') }}
                            </a>
                        @else
                            <a href="{{ route('containers_extended') }}" class="btn btn-default">
                                {{ __('container.all_containers') }}
                            </a>
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
                    <h3 class="card-title">История использования</h3>
                </div>
                <div class="card-body">
                    <table class="table table-striped containers_archive_table"
                           data-filter_type="history"
                           data-container_id="{{ $container->id }}"
                           id="containers_extended_ajax_table">
                        <thead>
                        <tr>
                            @foreach($columns as $column)
                                <th class="text-sm no-sort"
                                    style="width: {{ $column['width']['all'] }};">
                                </th>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

        </div>
    </section>
@endsection
