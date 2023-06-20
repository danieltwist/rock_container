<div class="card">
    <div class="card-header">
        <h3 class="card-title">Список контнейнеров из архива</h3>
    </div>
    <div class="card-body">
        <table class="table table-striped containers_archive_table"
               data-filter_type="{{ $table_filter_type }}"
               data-application_id="{{ $application_id }}"
               data-containers_names="{{ $load_from_archive }}"
               id="containers_history_extended_ajax_table">
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
