@extends('layouts.project')
@section('title', 'История использования контейнеров')
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">История использования контейнеров</h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @include('layouts.info_block')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Список контнейнеров из архива</h3>
                </div>
                <div class="card-body">
                    <table class="table table-striped containers_archive_table" id="containers_extended_ajax_table">
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
