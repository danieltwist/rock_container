<div class="card">
{{--    <div class="card-header">--}}
{{--        <h3 class="card-title">{{ __('container.containers_list') }}</h3>--}}
{{--        <div class="card-tools">--}}
{{--            <div class="dropdown dropleft" id="container_card_buttons">--}}
{{--                @if(in_array($role, ['director', 'super-admin']))--}}
{{--                    <button type="button" class="btn btn-success btn-sm d-none"--}}
{{--                            id="unmark_processing">--}}
{{--                        <i class="fas fa-check"></i>--}}
{{--                        Разблокировать все--}}
{{--                    </button>--}}
{{--                @endif--}}
{{--                <button type="button" class="btn btn-warning btn-sm d-none"--}}
{{--                        id="unmark_my_processing">--}}
{{--                    <i class="fas fa-check"></i>--}}
{{--                    Разблокировать--}}
{{--                </button>--}}
{{--                <button type="button" class="btn btn-primary btn-sm"--}}
{{--                        data-toggle="modal"--}}
{{--                        data-target="#edit_containers_list">--}}
{{--                    <i class="fas fa-edit"></i>--}}
{{--                    {{ __('general.edit') }}--}}
{{--                </button>--}}
{{--                <button class="btn btn-default btn-sm dropdown-toggle"--}}
{{--                        type="button"--}}
{{--                        id="dropdownMenuButton"--}}
{{--                        data-toggle="dropdown"--}}
{{--                        aria-haspopup="true"--}}
{{--                        aria-expanded="false">--}}
{{--                    Список столбцов--}}
{{--                </button>--}}
{{--                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">--}}
{{--                    @foreach($columns as $key => $value)--}}
{{--                        @if(in_array($key, ['26', '39', '56']))--}}
{{--                            <div class="dropdown-divider"></div>--}}
{{--                        @endif--}}
{{--                        <div class="dropdown-item">--}}
{{--                            <div class="icheck-primary d-inline">--}}
{{--                                <input type="checkbox"--}}
{{--                                       class="containers_table_columns_visibility"--}}
{{--                                       id="containers_table_column_{{ $key }}"--}}
{{--                                       data-column_id="{{ $key }}"--}}
{{--                                       checked>--}}
{{--                                <label for="containers_table_column_{{ $key }}">{{ $value['name'] }}</label>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    @endforeach--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
    <div class="card-body">
        <div class="btn-group">
            <a type="button"
                    class="btn btn-secondary btn-sm containers_filters"
                    onclick="window.location.reload();"
                    data-filter="all">
                Все
            </a>
            <button type="button"
                    class="btn btn-default btn-sm containers_filters"
                    data-filter="main_info">
                Статичные данные
            </button>
            <button type="button"
                    class="btn btn-default btn-sm containers_filters"
                    data-filter="svv">
                СВВ
            </button>
            <button type="button"
                    class="btn btn-default btn-sm containers_filters"
                    data-filter="free">
                Простой
            </button>
            <button type="button"
                    class="btn btn-default btn-sm containers_filters"
                    data-filter="repair">
                Ремонт
            </button>
            <button type="button"
                    class="btn btn-default btn-sm containers_filters"
                    data-filter="manual">
                Инструкции
            </button>
            <button type="button"
                    class="btn btn-default btn-sm containers_filters"
                    data-filter="smgs">
                СМГС / Акты
            </button>
            <button type="button"
                    class="btn btn-default btn-sm containers_filters"
                    data-filter="kp">
                К/П
            </button>
        </div>
        <div class="float-right">
            <div class="dropdown dropleft" id="container_card_buttons">
                @if(in_array($role, ['director', 'super-admin']))
                    <button type="button" class="btn btn-success btn-sm d-none"
                            id="unmark_processing">
                        <i class="fas fa-check"></i>
                        Разблокировать все
                    </button>
                @endif
                <button type="button" class="btn btn-warning btn-sm d-none"
                        id="unmark_my_processing">
                    <i class="fas fa-check"></i>
                    Разблокировать
                </button>
                <button type="button" class="btn btn-primary btn-sm"
                        data-toggle="modal"
                        data-target="#edit_containers_list">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-default btn-sm dropdown-toggle"
                        type="button"
                        id="dropdownMenuButton"
                        data-toggle="dropdown"
                        aria-haspopup="true"
                        aria-expanded="false">
                    Список столбцов
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    @foreach($columns as $key => $value)
                        @if(in_array($key, ['26', '39', '56']))
                            <div class="dropdown-divider"></div>
                        @endif
                        <div class="dropdown-item">
                            <div class="icheck-primary d-inline">
                                <input type="checkbox"
                                       class="containers_table_columns_visibility"
                                       id="containers_table_column_{{ $key }}"
                                       data-column_id="{{ $key }}"
                                       checked>
                                <label for="containers_table_column_{{ $key }}">{{ $value['name'] }}</label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="mt-4">
            <div id="containers_extended_table_div">
                <table class="table table-striped containers_extended_table"
                       data-filter_type="{{ $table_filter_type }}"
                       data-application_id="{{ $application_id }}"
                       @if(isset($load_from_containers)) data-containers_names="{{ $load_from_containers }}" @endif
                       id="containers_extended_ajax_table">
                    <thead>
                    <tr>
                        @foreach($columns as $column)
                            <th class="text-sm no-sort {{ $column['search_type'] }}-filter"
                                style="width: {{ $column['width']['all'] }};"></th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <form action="{{ route('containers_export_to_excel') }}" method="GET">
            @csrf
            <input type="hidden" id="chosen_containers_id" name="chosen_containers_id" value="">
            <button type="submit" class="btn btn-success download_file_directly"
                    data-action='{"download_file":{"need_download": "true"}}'>
                <i class="fas fa-file-excel"></i>
                {{ __('general.export_to_excel') }}
            </button>
        </form>
    </div>
</div>
