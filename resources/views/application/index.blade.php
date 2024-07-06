@extends('layouts.project')
@section('title', 'Все заявки')
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    @if (isset($_GET['trash']))
                        <h1 class="m-0">Удаленные заявки</h1>
                    @else
                        <h1 class="m-0">Все заявки</h1>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            @include('layouts.info_block')
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-default">
                        <div class="card-header">
                            <h3 class="card-title">Список заявок</h3>
                            <div id="loading_spinner"></div>
                        </div>
                        <div class="card-body">
                            <div id="containers_count"></div>
                            <div class="btn-group">
                                <button type="button" class="btn btn-default btn-sm applications_filters"
                                        data-filter_type="type"
                                        data-type="Все">{{ __('general.all') }}
                                </button>
                                @foreach([
                                            [
                                                'type' => 'Поставщик',
                                                'name' => 'Взять в аренду'
                                            ],
                                            [
                                                'type' => 'Клиент',
                                                'name' => 'Выдать в аренду'
                                            ],
                                            [
                                                'type' => 'Подсыл',
                                                'name' => 'Подсыл'
                                            ]
                                        ] as $key => $application_type)
                                    <button type="button" class="btn btn-default btn-sm applications_filters"
                                            data-filter_type="type"
                                            data-type="{{ $application_type['type'] }}">
                                        {{ $application_type['name'] }}
                                    </button>
                                @endforeach
                            </div>
                            <div class="btn-group">
                                @foreach(['Покупка', 'Продажа'] as $application_type)
                                    <button type="button" class="btn btn-default btn-sm applications_filters"
                                            data-filter_type="type"
                                            data-type="{{ $application_type }}">
                                        {{ $application_type }}
                                    </button>
                                @endforeach
                            </div>
                            <div class="mt-4">
                                <table class="table table-striped applications_table" id="applications_table">
                                    <thead>
                                    <tr>
                                        <th style="width: 1%">
                                            #
                                        </th>
                                        <th style="width: 13%">
                                            Название
                                        </th>
                                        <th style="width: 20%">
                                            Контрагент
                                        </th>
                                        <th style="width: 15%">
                                            Маршрут
                                        </th>
                                        <th style="width: 15%">
                                            Условия
                                        </th>
                                        <th style="width: 13%">
                                            Контейнеры
                                        </th>
                                        <th style="width: 5%">
                                            Статус
                                        </th>
                                        <th style="width: 22%">
                                            {{ __('general.actions') }}
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                        @if(in_array($role, ['director', 'accountant', 'super-admin']))
                            <div class="card-footer">
                                <form action="{{ route('applications_export_to_excel') }}" id="get_excel_applications" method="GET">
                                    @csrf
                                    @if (isset($_GET['draft']))
                                        @php
                                            $parameters = [
                                                'filename' => 'черновики_заявок',
                                                'sorting_type' => 'Черновики'
                                            ];
                                            $filter = 'draft';
                                        @endphp
                                    @elseif (isset($_GET['active']))
                                        @php
                                            $parameters = [
                                                'filename' => 'заявки_в_работе',
                                                'sorting_type' => 'Заявки в работе'
                                            ];
                                            $filter = 'active';
                                        @endphp
                                    @elseif (isset($_GET['done']))
                                        @php
                                            $parameters = [
                                                'filename' => 'завершенные_заявки',
                                                'sorting_type' => 'Завершенные заявки'
                                            ];
                                            $filter = 'done';
                                        @endphp
                                    @elseif (isset($_GET['trash']))
                                        @php
                                            $parameters = [
                                                'filename' => 'удаленные_заявки',
                                                'sorting_type' => 'Удаленные заявки'
                                            ];
                                            $filter = 'trash';
                                        @endphp
                                    @else
                                        @php
                                            $parameters = [
                                                'filename' => 'все_заявки',
                                                'sorting_type' => 'Все заявки',
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
                                        Скачать выгрузку заявок
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
