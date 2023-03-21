<table class="table table-striped" id="containers_extended_ajax_table">
    <thead>
    <tr>
        @foreach($columns as $column)
            <th class="text-sm no-sort {{ $column['search_type'] }}-filter"
                style="width: {{ $column['width'][$type] }};"></th>
        @endforeach
    </tr>
    </thead>
    <tbody></tbody>
</table>
