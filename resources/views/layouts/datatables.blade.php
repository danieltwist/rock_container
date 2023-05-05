<script>

    let page_href = window.location.href;
    let page_filter = page_href.split('?');
    let task_filter = window.location.pathname.replace('public/', '').split('/');
    let work_request_filter = window.location.pathname.replace('public/', '').split('/');
    let $invoice_sort_by_date = $('#invoice_sort_by_date');
    let $invoices_ajax_table = $('.invoices_ajax_table');
    let $task_table = $('.tasks_ajax_table');
    let $work_request_table = $('.work_request_ajax_table');
    let $clients_table = $('#clients_table');
    let $suppliers_table = $('#suppliers_table');
    let containers_table_filters = [];
    let containers_table_columns = [];
    let chosen_containers_id = [];
    let chosen_containers_names = [];
    let chosen_containers_filter = '';
    let $containers_extended_table = $('#containers_extended_ajax_table');

    let invoice_second_filter = {
        "status": '',
        "sub_status": 'Все',
        "second_filter": ''
    };

    let supplier_filters = {
        "country": '',
        "type": ''
    };

    let containers_filters = {};

    let applications_filters = {"filter": page_filter[1]};

    let audits_filter = {};

    $(function () {
        let urlpath = window.location.pathname.replace('public/', '').split('/');

        if (urlpath[1] === 'invoice') {
            if (getInvoicesPageType() === 'agree') getAgreedInvoicesAmount();
        }
        if (urlpath[1] === 'container' || urlpath[1] === 'application') {
            getContainersColumns();
            getContainersFilters('');
            checkProcessing();
            setTimeout(function () {
                $('.containers_extended_table').each(function () {
                    let filter_type = $(this).data('filter_type');
                    if(filter_type === 'application'){
                        containers_filters.application = $(this).data('application_id');
                    }
                    let $table_id = $('#containers_extended_ajax_table');
                    $table_id.DataTable({
                        fixedHeader: true,
                        processing: true,
                        serverSide: true,
                        searching: true,
                        ordering: true,
                        pageLength: 25,
                        lengthChange: true,
                        order: [0, 'desc'],
                        info: true,
                        scrollX: true,
                        ajax: {
                            url: "{{route('containers_extended_table')}}",
                            type: "POST",
                            data: containers_filters
                        },
                        language: {
                            "url": "/admin/plugins/datatables-ru-lang/{{ auth()->user()->language }}.json"
                        },
                        columns: [
                            {data: "id"},
                            {data: "name"},
                            {data: "status"},
                            {data: "type"},
                            {data: "owner_name"},
                            {data: "size"},
                            {data: "supplier_application_name"},
                            {data: "supplier_price_amount"},
                            {data: "supplier_grace_period"},
                            {data: "supplier_snp_after_range"},
                            {data: "supplier_country"},
                            {data: "supplier_city"},
                            {data: "supplier_terminal"},
                            {data: "supplier_date_get"},
                            {data: "supplier_date_start_using"},
                            {data: "supplier_days_using"},
                            {data: "supplier_snp_total"},
                            {data: "supplier_place_of_delivery_country"},
                            {data: "supplier_place_of_delivery_city"},
                            {data: "svv"},
                            {data: "supplier_terminal_storage_amount"},
                            {data: "supplier_payer_tx"},
                            {data: "supplier_renewal_reexport_costs_amount"},
                            {data: "supplier_repair_amount"},
                            {data: "supplier_repair_status"},
                            {data: "supplier_repair_confirmation"},
                            {data: "relocation_counterparty_name"},
                            {data: "relocation_application_name"},
                            {data: "relocation_price_amount"},
                            {data: "relocation_date_send"},
                            {data: "relocation_date_arrival_to_terminal"},
                            {data: "relocation_place_of_delivery_city"},
                            {data: "relocation_place_of_delivery_terminal"},
                            {data: "relocation_delivery_time_days"},
                            {data: "relocation_snp_after_range"},
                            {data: "relocation_snp_total"},
                            {data: "relocation_repair_amount"},
                            {data: "relocation_repair_status"},
                            {data: "relocation_repair_confirmation"},
                            {data: "client_counterparty_name"},
                            {data: "client_application_name"},
                            {data: "client_price_amount"},
                            {data: "client_grace_period"},
                            {data: "client_snp_after_range"},
                            {data: "client_date_get"},
                            {data: "client_date_return"},
                            {data: "client_place_of_delivery_city"},
                            {data: "client_days_using"},
                            {data: "client_snp_total"},
                            {data: "client_repair_amount"},
                            {data: "client_repair_status"},
                            {data: "client_repair_confirmation"},
                            {data: "client_smgs"},
                            {data: "client_manual"},
                            {data: "client_location_request"},
                            {data: "client_date_manual_request"},
                            {data: "client_return_act"},
                            {data: "own_date_buy"},
                            {data: "own_date_sell"},
                            {data: "own_sale_price"},
                            {data: "own_buyer"},
                            {data: "processing"},
                            {data: "removed"},
                            {data: "additional_info"}
                        ],
                        columnDefs: [
                            {targets: 'no-sort', orderable: false}
                        ],
                        initComplete: function () {
                            this.api().columns().every(function () {
                                let column = this;
                                let column_id = column[0][0];
                                if (column_id !== 0) {
                                    $('<span id="sorting_column_' + column_id + '" class="cursor-pointer sorting_containers_table" data-column_id="' + column_id + '" data-ordering_direction="asc">' + containers_table_columns[column_id].name + '</span>')
                                        .prependTo($(column.header()).append());
                                }
                            });
                            this.api().columns('.select-filter').every(function () {
                                let column = this;
                                let column_id = column[0][0];
                                let select = $('<select class="form-control select2 init_select2" id="column_' + column_id + '" style="height: calc(1.8125rem + 2px); font-size: small;"><option value=""></option></select>')
                                    .prependTo($(column.header()).append())
                                    .on('change', function (data) {
                                        column.search($(this).val(), false, false).draw();
                                    });
                                containers_table_filters[[column[0][0]]].forEach(function (value) {
                                    select.append('<option value="' + value + '">' + value + '</option>');
                                });
                            });
                            this.api().columns('.input-filter').every(function () {
                                let column = this;
                                $('<input type="text" class="form-control" style="height: calc(1.8125rem + 2px); font-size: small;">')
                                    .prependTo($(column.header()).append())
                                    .on('keyup', function (data) {
                                        column.search($(this).val(), false, false).draw();
                                    });
                            });

                            if (typeof $.cookie('containers_hidden_columns') !== 'undefined') {
                                JSON.parse($.cookie('containers_hidden_columns')).forEach(function (value) {
                                    $containers_extended_table.DataTable().column(value).visible(false);
                                    $('#containers_table_column_' + value).prop("checked", false);
                                });
                            }
                        },
                        drawCallback: function () {
                            $('.xedit').editable({
                                mode: 'inline',
                                url: '{{url("xeditable/update")}}',
                                title: '{{ __('general.update_') }}',
                                emptytext: '{{ __('general.empty') }}',
                                params: function (params) {
                                    params.model = $(this).data('model');
                                    return params;
                                },
                                success: function (response, newValue) {
                                    $containers_extended_table.DataTable().draw(false);
                                    if (response.status === 'error') console.log('Ошибка');
                                }
                            });
                            chosen_containers_id = $table_id.DataTable().ajax.json().id_list;
                            $('#chosen_containers_id').val(chosen_containers_id);
                            chosen_containers_names = $table_id.DataTable().ajax.json().prefix_list;
                        },
                        createdRow: function (row, data, dataIndex) {
                            if (data.class !== '') {
                                $(row).addClass(data.class);
                            }
                            if($table_id.DataTable().ajax.json().collapsed_exist){
                                console.log('collapsed-exits');
                                $(row).addClass('height-72');
                            }
                        }
                    });
                    if(filter_type === 'application'){
                        setTimeout(function () {
                            fixed_header_enabled = false;
                            if(fixed_header_enabled){
                                $('#container_card_buttons').prepend('<button type="button" class="btn btn-secondary btn-sm fixed_header_toggle" data-fixed_state="blocked"> <i class="fas fa-unlock"></i> </button>')
                            }
                            else {
                                $('#container_card_buttons').prepend('<button type="button" class="btn btn-secondary btn-sm fixed_header_toggle" data-fixed_state="unblocked"> <i class="fas fa-lock"></i> </button>')
                            }
                            $table_id.DataTable().fixedHeader.disable();
                        }, 2000);

                    }
                });
            }, 500);
        }
    });

    function getContainersFilters(filter) {
        chosen_containers_filter = filter;
        $.ajax({
            type: "GET",
            url: "{{route('containers_extended_table_get_filters')}}",
            data: {
                filter: filter
            },
            success: function (data) {
                containers_table_filters = data.filters;
            },
            error: function (XMLHttprequest, textStatus, errorThrown) {
                console.log(textStatus);
            }
        });
    }

    function getContainersColumns() {
        $.ajax({
            type: "GET",
            url: "{{route('containers_extended_table_get_columns')}}",
            success: function (data) {
                containers_table_columns = data.columns;
            },
            error: function (XMLHttprequest, textStatus, errorThrown) {
                console.log(textStatus);
            }
        });
    }

    function checkProcessing() {
        $.ajax({
            type: "POST",
            url: "{{route('check_processing')}}",
            success: function (data) {
                if (data.processing_by_me === 'yes') {
                    $('#unmark_my_processing').removeClass('d-none');
                }
                if (data.processing === 'yes') {
                    $('#unmark_processing').removeClass('d-none');
                }
            },
            error: function (XMLHttprequest, textStatus, errorThrown) {
                console.log(textStatus);
            }
        });
    }

    function getInvoicesPageType() {
        if (['?for_approval', '?agreed', '?on_approval'].includes(window.location.search))
            return 'agree';
        else return 'all';
    }

    function getAgreedInvoicesAmount() {
        $('#loading_spinner').html('&nbsp;&nbsp' + loading_spinner_small);
        $.ajax({
            type: "GET",
            data: {
                "invoice_status": invoice_second_filter.status,
                "sub_status": invoice_second_filter.sub_status,
                "filter": page_href.split('?')[1],
                "data_range": $('#reportrange span').html()
            },
            url: APP_URL + "/invoice/get_agreed_invoices_amount/",
            success: function (response) {
                $('#loading_spinner').html('');
                $('#invoices_agree_amount').html(response);
            },
            error: function (XMLHttprequest, textStatus, errorThrown) {
                console.log(textStatus);
            }
        });
    }

    function sortInvoicesByDate(filter) {
        let ordering = 0;
        if(filter === 'Счет согласован на оплату'  || filter === 'Согласована частичная оплата' ){
            ordering = 5;
        }
        $('#get_excel_invoices').append('<input type="hidden" name="data_range" value="' + $('#reportrange span').html() + '">');
        if (getInvoicesPageType() === 'agree') getAgreedInvoicesAmount();
        $invoices_ajax_table.DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            searching: true,
            ordering: true,
            order: [ordering, 'desc'],
            info: true,
            autoWidth: false,
            responsive: true,
            ajax: {
                url: "{{ route('get_invoices_table') }}",
                data: {
                    "invoice_status": invoice_second_filter.status,
                    "sub_status": invoice_second_filter.sub_status,
                    "filter": page_href.split('?')[1],
                    "data_range": $('#reportrange span').html()
                }
            },
            language: {
                "url": "/admin/plugins/datatables-ru-lang/{{ auth()->user()->language }}.json"
            },
            columns: [
                {data: 'id'},
                {data: 'direction'},
                {data: 'project_id'},
                {data: 'amount'},
                {data: 'amount_paid'},
                {data: 'agreement_date'},
                {data: 'created_at'},
            ],
            preDrawCallback: function () {
                $invoices_ajax_table.css({visibility: "hidden"});
            },
            drawCallback: function () {
                $invoices_ajax_table.css({visibility: "visible"});
            },
            createdRow: function (row, data, dataIndex) {
                if (data.class !== '') {
                    $(row).addClass(data.class);
                }
            }
        });
    }

    function getInvoicesWithFilters() {
        $('#get_excel_invoices').append('<input type="hidden" name="data_range" value="' + $('#reportrange span').html() + '">');
        $invoices_ajax_table.DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            searching: true,
            ordering: true,
            order: [0, 'desc'],
            info: true,
            autoWidth: false,
            responsive: true,
            ajax: {
                url: "{{ route('get_invoices_table') }}",
                data: {
                    "invoice_status": invoice_second_filter.status,
                    "sub_status": invoice_second_filter.sub_status,
                    "filter": page_href.split('?')[1],
                    "data_range": $('#reportrange span').html()
                }
            },
            language: {
                "url": "/admin/plugins/datatables-ru-lang/{{ auth()->user()->language }}.json"
            },
            columns: [
                {data: 'id'},
                {data: 'direction'},
                {data: 'project_id'},
                {data: 'amount'},
                {data: 'amount_paid'},
                {data: 'agreement_date'},
                {data: 'created_at'},
            ],
            preDrawCallback: function () {
                $invoices_ajax_table.css({visibility: "hidden"});
            },
            drawCallback: function () {
                $invoices_ajax_table.css({visibility: "visible"});
            },
            createdRow: function (row, data, dataIndex) {
                if (data.class !== '') {
                    $(row).addClass(data.class);
                }
            }
        });
    }

    function sortProjectsStaticTableByDate() {
        let $reportrange = $('#reportrange span');
        $('#data_range').val($reportrange.html());
        $('#search_results').html(loading_spinner)
        $.ajax({
            type: "GET",
            url: APP_URL + "/project/sort_by_date",
            data: {
                data_range: $reportrange.html(),
                type: $('h1').first().text()
            },
            success: function (data) {
                $('#get_projects_statistic').removeClass('d-none');
                $('#search_results').html(data);
                $('#projects_table').DataTable({
                    "paging": true,
                    "lengthChange": true,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false,
                    "responsive": true,
                    "language": {
                        "url": "/admin/plugins/datatables-ru-lang/{{ auth()->user()->language }}.json"
                    }
                });
            },
            error: function (XMLHttprequest, textStatus, errorThrown) {
                console.log(textStatus);
            }
        });
    }

    function sortProjectsByDate() {
        let $reportrange = $('#reportrange span');
        $('#get_excel_projects').append('<input type="hidden" name="data_range" value="' + $reportrange.html() + '">');
        $('.projects_ajax_table').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            searching: true,
            ordering: true,
            order: [0, 'desc'],
            info: true,
            autoWidth: false,
            responsive: true,
            ajax: {
                url: "{{ route('get_projects_table_with_filter') }}",
                data: {
                    "filter": page_href.split('?')[1],
                    "data_range": $reportrange.html()
                }
            },
            language: {
                "url": "/admin/plugins/datatables-ru-lang/{{ auth()->user()->language }}.json"
            },
            columns: [
                {data: 'id'},
                {data: 'name'},
                {data: 'client_id'},
                {data: 'freight_amount'},
                {data: 'status'},
                {data: 'created_at'},
            ],
            preDrawCallback: function () {
                $('.projects_ajax_table').addClass('d-none');
            },
            drawCallback: function () {
                $('.projects_ajax_table').removeClass('d-none');
            }
        });
    }

    function initInvoiceDatatables(data, route_link, filter_type, table_id) {
        let ordering = 0;
        if(data.filter === 'agreed'){
            ordering = 5;
        }
        $('#' + table_id).DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ordering: true,
            order: [ordering, 'desc'],
            info: true,
            autoWidth: false,
            responsive: true,
            ajax: {
                url: route_link,
                data: data
            },
            language: {
                "url": "/admin/plugins/datatables-ru-lang/{{ auth()->user()->language }}.json"
            },
            columns: [
                {data: 'id'},
                {data: 'direction'},
                {data: 'project_id'},
                {data: 'amount'},
                {data: 'amount_paid'},
                {data: 'agreement_date'},
                {data: 'created_at'},
            ],
            createdRow: function (row, data, dataIndex) {
                if (data.class !== '') {
                    $(row).addClass(data.class);
                }
            }
        });
    }

    function filterSupplierTable() {
        $suppliers_table.DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            searching: true,
            ordering: true,
            order: [0, 'desc'],
            info: true,
            autoWidth: false,
            responsive: true,
            ajax: {
                url: "{{ route('get_suppliers_table') }}",
                data: {"country": supplier_filters.country, "type": supplier_filters.type, "filter": page_filter[1]},
            },
            language: {
                "url": "/admin/plugins/datatables-ru-lang/{{ auth()->user()->language }}.json"
            },
            columns: [
                {data: 'id'},
                {data: 'name'},
                {data: 'requisites'},
                {data: 'info'},
                {data: 'resources'},
                {data: 'actions'},
            ],
            preDrawCallback: function () {
                $suppliers_table.addClass('d-none');
            },
            drawCallback: function () {
                $suppliers_table.removeClass('d-none');
            },
        });
    }

    function filterContainerTable(filter) {
        $('#containers_extended_table_div').addClass('d-none');
        console.log('filter');
        getContainersFilters(filter);
        containers_filters.filter = filter;
        $.ajax({
            type: "POST",
            url: "{{ route('load_table_for_filter') }}",
            data: {
                type: filter
            },
            success: function (response) {
                $('#containers_extended_ajax_table').DataTable().destroy();
                $('#containers_extended_table_div').html(response.view);
                setTimeout(function () {
                    $('#containers_extended_ajax_table').DataTable({
                        fixedHeader: true,
                        processing: true,
                        serverSide: true,
                        searching: true,
                        ordering: true,
                        pageLength: 25,
                        lengthChange: true,
                        order: [0, 'desc'],
                        info: true,
                        scrollX: true,
                        ajax: {
                            url: "{{route('containers_extended_table')}}",
                            data: containers_filters,
                            type: "POST"
                        },
                        language: {
                            "url": "/admin/plugins/datatables-ru-lang/{{ auth()->user()->language }}.json"
                        },
                        columns: [
                            {data: "id"},
                            {data: "name"},
                            {data: "status"},
                            {data: "type"},
                            {data: "owner_name"},
                            {data: "size"},
                            {data: "supplier_application_name"},
                            {data: "supplier_price_amount"},
                            {data: "supplier_grace_period"},
                            {data: "supplier_snp_after_range"},
                            {data: "supplier_country"},
                            {data: "supplier_city"},
                            {data: "supplier_terminal"},
                            {data: "supplier_date_get"},
                            {data: "supplier_date_start_using"},
                            {data: "supplier_days_using"},
                            {data: "supplier_snp_total"},
                            {data: "supplier_place_of_delivery_country"},
                            {data: "supplier_place_of_delivery_city"},
                            {data: "svv"},
                            {data: "supplier_terminal_storage_amount"},
                            {data: "supplier_payer_tx"},
                            {data: "supplier_renewal_reexport_costs_amount"},
                            {data: "supplier_repair_amount"},
                            {data: "supplier_repair_status"},
                            {data: "supplier_repair_confirmation"},
                            {data: "relocation_counterparty_name"},
                            {data: "relocation_application_name"},
                            {data: "relocation_price_amount"},
                            {data: "relocation_date_send"},
                            {data: "relocation_date_arrival_to_terminal"},
                            {data: "relocation_place_of_delivery_city"},
                            {data: "relocation_place_of_delivery_terminal"},
                            {data: "relocation_delivery_time_days"},
                            {data: "relocation_snp_after_range"},
                            {data: "relocation_snp_total"},
                            {data: "relocation_repair_amount"},
                            {data: "relocation_repair_status"},
                            {data: "relocation_repair_confirmation"},
                            {data: "client_counterparty_name"},
                            {data: "client_application_name"},
                            {data: "client_price_amount"},
                            {data: "client_grace_period"},
                            {data: "client_snp_after_range"},
                            {data: "client_date_get"},
                            {data: "client_date_return"},
                            {data: "client_place_of_delivery_city"},
                            {data: "client_days_using"},
                            {data: "client_snp_total"},
                            {data: "client_repair_amount"},
                            {data: "client_repair_status"},
                            {data: "client_repair_confirmation"},
                            {data: "client_smgs"},
                            {data: "client_manual"},
                            {data: "client_location_request"},
                            {data: "client_date_manual_request"},
                            {data: "client_return_act"},
                            {data: "own_date_buy"},
                            {data: "own_date_sell"},
                            {data: "own_sale_price"},
                            {data: "own_buyer"},
                            {data: "processing"},
                            {data: "removed"},
                            {data: "additional_info"}
                        ],
                        columnDefs: [
                            {targets: 'no-sort', orderable: false}
                        ],
                        initComplete: function () {
                            console.log('init');
                            this.api().columns().every(function () {
                                let column = this;
                                let column_id = column[0][0];
                                if (column_id !== 0) {
                                    $('<span id="sorting_column_' + column_id + '" class="cursor-pointer sorting_containers_table" data-column_id="' + column_id + '" data-ordering_direction="asc">' + containers_table_columns[column_id].name + '</span>')
                                        .prependTo($(column.header()).append());
                                }
                            });
                            this.api().columns('.select-filter').every(function () {
                                let column = this;
                                let column_id = column[0][0];
                                let select = $('<select class="form-control select2 init_select2" id="column_' + column_id + '" style="height: calc(1.8125rem + 2px); font-size: small;"><option value=""></option></select>')
                                    .prependTo($(column.header()).append())
                                    .on('change', function (data) {
                                        column.search($(this).val(), false, false).draw();
                                    });
                                containers_table_filters[[column[0][0]]].forEach(function (value) {
                                    select.append('<option value="' + value + '">' + value + '</option>');
                                });
                            });
                            this.api().columns('.input-filter').every(function () {
                                let column = this;
                                $('<input type="text" class="form-control" style="height: calc(1.8125rem + 2px); font-size: small;">')
                                    .prependTo($(column.header()).append())
                                    .on('keyup', function (data) {
                                        column.search($(this).val(), false, false).draw();
                                    });
                            });
                        },
                        drawCallback: function () {
                            console.log('draw');
                            chosen_containers_id = $('#containers_extended_ajax_table').DataTable().ajax.json().id_list;
                            $('#chosen_containers_id').val(chosen_containers_id);
                            chosen_containers_names = $('#containers_extended_ajax_table').DataTable().ajax.json().prefix_list;
                        },
                        createdRow: function (row, data, dataIndex) {
                            if (data.class !== '') {
                                $(row).addClass(data.class);
                            }
                            if($('#containers_extended_ajax_table').DataTable().ajax.json().collapsed_exist){
                                console.log('collapsed-exits');
                                $(row).addClass('height-72');
                            }
                        }
                    });
                }, 100);
                setTimeout(function () {
                    if (window.location.pathname.replace('public/', '').split('/')[1] === 'application') {
                        $('#containers_extended_ajax_table').DataTable().fixedHeader.disable();
                    }
                }, 2000);

            },
            error: function (XMLHttprequest, textStatus, errorThrown) {
                console.log(textStatus);
            }
        });

    }

    function initApplicationTables() {
        $('#applications_table').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            searching: true,
            ordering: true,
            pageLength: 10,
            order: [0, 'desc'],
            info: true,
            ajax: {
                url: "{{route('get_applications_table')}}",
                type: "GET",
                data: applications_filters
            },
            language: {
                "url": "/admin/plugins/datatables-ru-lang/{{ auth()->user()->language }}.json"
            },
            columns: [
                {data: "id"},
                {data: "name"},
                {data: "counterparty_type"},
                {data: "send_from_country"},
                {data: "price_amount"},
                {data: "containers_amount"},
                {data: "status"},
                {data: "created_at"}
            ]
        });
    }

    function initAuditsTables() {
        $('#audits_table').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            searching: false,
            ordering: true,
            pageLength: 10,
            order: [0, 'desc'],
            info: true,
            ajax: {
                url: "{{route('get_audits_table')}}",
                type: "GET",
                data: audits_filter
            },
            language: {
                "url": "/admin/plugins/datatables-ru-lang/{{ auth()->user()->language }}.json"
            },
            columns: [
                {data: "id"},
                {data: "created_at"},
                {data: "user"},
                {data: "auditable_type"},
                {data: "event"},
                {data: "old_values"},
                {data: "new_values"}
            ]
        });
    }

    $(document).ready(function () {

        // fix to get fixedHeader and scrollX working together in datatables
        $(document).on('preInit.dt', function (e, settings) {
            if(
                settings.oInit
                && settings.oInit.fixedHeader
                && settings.oInit.scrollX
            ) {
                $(settings.nScrollBody).scroll(function() {
                    if(!settings._fixedHeader) {
                        return false;
                    }

                    let tableFloatingHeader = settings._fixedHeader.dom.header.floating;
                    if(tableFloatingHeader) {
                        tableFloatingHeader.css('left', this.getBoundingClientRect().x - parseInt($(this).scrollLeft()) + 'px');
                    }
                });
            }
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        ///-------------------------- ИНВОЙСЫ --------------------------///

        /// Основная страница счетов
        $invoices_ajax_table.each(function () {
            let filter_type = page_href.split('?')[1];
            let data = {
                "filter": filter_type,
                "data_range": $('#reportrange span').html()
            }
            let route_link = "{{ route('get_invoices_table') }}";
            let table_id = 'invoices_table';

            initInvoiceDatatables(data, route_link, filter_type, table_id);
        });

        $('.invoices_filters').on('click', function () {
            $('.invoices_filters').each(function () {
                $(this).removeClass('btn-secondary').addClass('btn-default');
            });
            $(this).removeClass('btn-default').addClass('btn-secondary');

            invoice_second_filter.status = $(this).data('filter');
            $('#get_excel_invoices').append('<input type="hidden" name="status" value="' + $(this).data('filter') + '">');

            sortInvoicesByDate($(this).data('filter'));
        });

        $('.invoices_agree_filters').on('click', function () {
            console.log($(this).data('filter'));
            $('.invoices_agree_filters').each(function () {
                $(this).removeClass('btn-secondary').addClass('btn-default');
            });
            $(this).removeClass('btn-default').addClass('btn-secondary');

            invoice_second_filter.sub_status = $(this).data('filter');
            $('#get_excel_invoices').append('<input type="hidden" name="sub_status" value="' + $(this).data('filter') + '">');

            sortInvoicesByDate();
        });

        /// таблицы инвойсы главная страница
        $('.invoices_homepage').each(function (i) {
            let filter_type = ($(this).data('type'));
            let data = {"filter": filter_type}
            let route_link = "{{ route('get_invoices_with_filter') }}";
            let table_id = 'invoices_ajax_table_content_' + filter_type;
            setTimeout(function () {
                initInvoiceDatatables(data, route_link, filter_type, table_id);
            }, 500 * i);
        });

        //таблица инвойсы отчеты по проектам
        $('.invoices_project_report').each(function () {
            let filter_type = ($(this).data('type'));
            let project_type = ($(this).data('project_type'));
            let user_id = ($(this).data('user_id'));
            let manager_id = ($(this).data('manager_id'));
            let data_range = ($(this).data('range'));
            let data = {
                "filter": filter_type, "data_range": data_range, "project_filter_array": {
                    'filter': project_type,
                    'user_id': user_id,
                    'manager_id': manager_id,
                }
            }
            let route_link = "{{ route('get_invoice_for_project_analytics') }}";
            let table_id = 'invoices_ajax_table_content_' + filter_type;

            initInvoiceDatatables(data, route_link, filter_type, table_id);
        });

        //таблица инвойсы аналитика проектов
        $('.invoices_analytics').each(function () {
            let filter_type = ($(this).data('type'));
            let data = {"filter": filter_type, "project_filter": 'finished', "data_range": $('#data_range').val()}
            let route_link = "{{ route('get_invoice_for_project_analytics') }}";
            let table_id = 'invoices_ajax_table_content_' + filter_type;

            initInvoiceDatatables(data, route_link, filter_type, table_id);
        });

        //инициализация таблицы инвойсов с любыми фильтрами
        $('.invoices_with_filters').each(function () {
            let data = {};
            $.each($(this).data(), function (prop, value) {
                data[prop] = value;
            });
            console.log(data);
            let route_link = "{{ route('get_invoices_with_filter') }}";
            let table_id = 'invoices_ajax_table_content_' + data.table_id;

            initInvoiceDatatables(data, route_link, '', table_id);
        });

        //таблица инвойсы аналитика контрагентов
        $('.invoices_counterparty').each(function () {
            let filter_type = ($(this).data('type'));
            let data = {"filter": filter_type + '=' + $('#counterparty_id').val(), "data_range": $('#data_range').val()}

            if ($(this).data('personal') === true) {
                data.second_filter = 'my';
                invoice_second_filter.second_filter = 'my';
            }

            let route_link = "{{ route('get_invoices_with_filter') }}";
            let table_id = 'invoices_ajax_table_content_' + filter_type;

            initInvoiceDatatables(data, route_link, filter_type, table_id);
        });

        //таблица инвойсы отчеты дебит кредит
        $('.invoices_debit_credit').each(function () {
            let filter_type = ($(this).data('type'));
            let data = {"filter": filter_type};
            let route_link = "{{ route('get_invoices_with_filter') }}";
            let table_id = 'invoices_ajax_table_content_' + filter_type;

            initInvoiceDatatables(data, route_link, filter_type, table_id);
        });

        //таблица инвойсы в проекте
        $('.invoices_project').each(function () {
            console.log($(this).data('personal'));
            let project_id = ($(this).data('object_id'));
            let filter_type = ($(this).data('type'));
            let data = {};
            if ($(this).data('personal') === true) {
                data = {"project": project_id, "second_filter": 'my'};
                invoice_second_filter.second_filter = 'my';
            } else {
                data = {"project": project_id};
            }

            let route_link = "{{ route('get_invoices_with_filter') }}";
            let table_id = 'invoices_ajax_table_content_' + filter_type;
            console.log(data);
            initInvoiceDatatables(data, route_link, filter_type, table_id);
        });

        //таблица инвойсы в заявке
        $('.invoices_application').each(function () {
            console.log('init');
            let application_id = ($(this).data('object_id'));
            let filter_type = ($(this).data('type'));
            let data = {};
            if ($(this).data('personal') === true) {
                data = {"application": application_id, "second_filter": 'my'};
                invoice_second_filter.second_filter = 'my';
            } else {
                data = {"application": application_id};
            }

            let route_link = "{{ route('get_invoices_with_filter') }}";
            let table_id = 'invoices_ajax_table_content_' + filter_type;
            initInvoiceDatatables(data, route_link, filter_type, table_id);
        });

        $('.invoices_object_filters').on('click', function () {
            $('#alternative_block_invoices').addClass('d-none');
            $('#standard_block_invoices').removeClass('d-none');

            $('.invoices_object_filters').each(function () {
                $(this).removeClass('btn-secondary').addClass('btn-default');
            });

            $('.show_alternative_block_invoices').addClass('btn-default');

            $(this).removeClass('btn-default').addClass('btn-secondary');

            let object_id = $('.filter_table_div').data('object_id');
            let object_type = $('.filter_table_div').data('type');

            let data = {};
            data[object_type] = object_id;

            if ($(this).data('filter') != '') {
                let filter_type = $(this).data('filter_type');
                data[filter_type] = $(this).data('filter');
            }

            if ($(this).data('personal') === true) {
                data['second_filter'] = 'my';
                invoice_second_filter.second_filter = 'my';
            }

            console.log(data);
            let route_link = "{{ route('get_invoices_with_filter') }}";
            let table_id = 'invoices_ajax_table_content_' + object_type;

            $('#' + table_id).DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                searching: true,
                ordering: true,
                order: [0, 'desc'],
                info: true,
                autoWidth: false,
                responsive: true,
                ajax: {
                    url: route_link,
                    data: data
                },
                language: {
                    "url": "/admin/plugins/datatables-ru-lang/{{ auth()->user()->language }}.json"
                },
                columns: [
                    {data: 'id'},
                    {data: 'direction'},
                    {data: 'project_id'},
                    {data: 'amount'},
                    {data: 'amount_paid'},
                    {data: 'status'},
                    {data: 'created_at'},
                ],
                createdRow: function (row, data, dataIndex) {
                    if (data.class !== '') {
                        $(row).addClass(data.class);
                    }
                },
                preDrawCallback: function () {
                    $('#' + table_id).css({visibility: "hidden"});
                },
                drawCallback: function () {
                    $('#' + table_id).css({visibility: "visible"});
                }
            });
        });

        $('.show_alternative_block_invoices').on('click', function () {

            $('.invoices_object_filters').each(function () {
                $(this).removeClass('btn-secondary').addClass('btn-default');
            });
            $(this).removeClass('btn-default').addClass('btn-secondary');

            $('#alternative_block_invoices').removeClass('d-none');
            $('#standard_block_invoices').addClass('d-none');

        });

        $('#containers_ajax_table').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ordering: true,
            order: [0, 'desc'],
            info: true,
            autoWidth: false,
            responsive: true,
            ajax: {
                url: "{{route('get_containers_table')}}",
                data: {"filter": page_filter[1]}
            },
            language: {
                "url": "/admin/plugins/datatables-ru-lang/{{ auth()->user()->language }}.json"
            },
            columns: [
                {data: 'id'},
                {data: 'name'},
                {data: 'start_date'},
                {data: 'grace_period_for_client'},
                {data: 'country'},
                {data: 'created_at'},
            ],
            drawCallback: function (settings) {
                $('.xedit').editable({
                    mode: 'inline',
                    url: '{{url("xeditable/update")}}',
                    title: '{{ __('general.update_') }}',
                    emptytext: '{{ __('general.empty') }}',
                    params: function (params) {
                        params.model = $(this).data('model');
                        return params;
                    },
                    success: function (response, newValue) {
                        $('#containers_ajax_table').DataTable().draw(false);
                        if (response.status === 'error') console.log('Ошибка');
                    }
                });
            }
        });

        /////////////////////////////////////////////
        let fixed_header_enabled = true;

        $('.containers_archive_table').each(function () {
            let filter_type = $(this).data('filter_type');
            if(filter_type === 'application'){
                containers_filters.application = $(this).data('application_id');
            }
            if(filter_type === 'history'){
                containers_filters.history = $(this).data('container_id');
            }
            let $table_id = $('#containers_extended_ajax_table');
            $table_id.DataTable({
                processing: true,
                serverSide: true,
                searching: true,
                ordering: true,
                pageLength: 25,
                order: [0, 'desc'],
                info: true,
                scrollX: true,
                ajax: {
                    url: "{{route('get_archive_containers_table')}}",
                    type: "POST",
                    data: containers_filters
                },
                language: {
                    "url": "/admin/plugins/datatables-ru-lang/{{ auth()->user()->language }}.json"
                },
                columns: [
                    {data: "id"},
                    {data: "name"},
                    {data: "status"},
                    {data: "type"},
                    {data: "owner_name"},
                    {data: "size"},
                    {data: "supplier_application_name"},
                    {data: "supplier_price_amount"},
                    {data: "supplier_grace_period"},
                    {data: "supplier_snp_after_range"},
                    {data: "supplier_country"},
                    {data: "supplier_city"},
                    {data: "supplier_terminal"},
                    {data: "supplier_date_get"},
                    {data: "supplier_date_start_using"},
                    {data: "supplier_days_using"},
                    {data: "supplier_snp_total"},
                    {data: "supplier_place_of_delivery_country"},
                    {data: "supplier_place_of_delivery_city"},
                    {data: "svv"},
                    {data: "supplier_terminal_storage_amount"},
                    {data: "supplier_payer_tx"},
                    {data: "supplier_renewal_reexport_costs_amount"},
                    {data: "supplier_repair_amount"},
                    {data: "supplier_repair_status"},
                    {data: "supplier_repair_confirmation"},
                    {data: "relocation_counterparty_name"},
                    {data: "relocation_application_name"},
                    {data: "relocation_price_amount"},
                    {data: "relocation_date_send"},
                    {data: "relocation_date_arrival_to_terminal"},
                    {data: "relocation_place_of_delivery_city"},
                    {data: "relocation_place_of_delivery_terminal"},
                    {data: "relocation_delivery_time_days"},
                    {data: "relocation_snp_after_range"},
                    {data: "relocation_snp_total"},
                    {data: "relocation_repair_amount"},
                    {data: "relocation_repair_status"},
                    {data: "relocation_repair_confirmation"},
                    {data: "client_counterparty_name"},
                    {data: "client_application_name"},
                    {data: "client_price_amount"},
                    {data: "client_grace_period"},
                    {data: "client_snp_after_range"},
                    {data: "client_date_get"},
                    {data: "client_date_return"},
                    {data: "client_place_of_delivery_city"},
                    {data: "client_days_using"},
                    {data: "client_snp_total"},
                    {data: "client_repair_amount"},
                    {data: "client_repair_status"},
                    {data: "client_repair_confirmation"},
                    {data: "client_smgs"},
                    {data: "client_manual"},
                    {data: "client_location_request"},
                    {data: "client_date_manual_request"},
                    {data: "client_return_act"},
                    {data: "own_date_buy"},
                    {data: "own_date_sell"},
                    {data: "own_sale_price"},
                    {data: "own_buyer"},
                    {data: "processing"},
                    {data: "removed"},
                    {data: "additional_info"}
                ],
                columnDefs: [
                    {targets: 'no-sort', orderable: false}
                ],
                initComplete: function () {
                    this.api().columns().every(function () {
                        let column = this;
                        let column_id = column[0][0];
                        if (column_id !== 0) {
                            $('<span id="sorting_column_' + column_id + '" class="cursor-pointer sorting_containers_table" data-column_id="' + column_id + '" data-ordering_direction="asc">' + containers_table_columns[column_id].name + '</span>')
                                .prependTo($(column.header()).append());
                        }
                    });
                },
                drawCallback: function () {
                    chosen_containers_id = $table_id.DataTable().ajax.json().id_list;
                    $('#chosen_containers_id').val(chosen_containers_id);
                    chosen_containers_names = $table_id.DataTable().ajax.json().prefix_list;
                },
                createdRow: function (row, data, dataIndex) {
                    if (data.class !== '') {
                        $(row).addClass(data.class);
                    }
                }
            });
        });

        $('.applications_table').each(function () {
            let filter_type = $(this).data('filter_type');
            if(filter_type === 'supplier'){
                applications_filters.supplier = $(this).data('supplier_id');
            }
            if(filter_type === 'client'){
                applications_filters.client = $(this).data('client_id');
            }

            console.log(applications_filters);
            initApplicationTables();
        });

        $(document).on("click", ".applications_filters", function () {
            let filter_type = $(this).data('filter_type');
            if(filter_type === 'type'){
                applications_filters.type = $(this).data('type');
            }
            $('.applications_filters').each(function () {
                $(this).removeClass('btn-secondary').addClass('btn-default');
            });
            $(this).removeClass('btn-default').addClass('btn-secondary');

            initApplicationTables();
        });

        $(document).on("click", ".containers_table_columns_visibility", function () {
            let column_id = $(this).data('column_id');
            if ($(this).is(":checked")) {
                $containers_extended_table.DataTable().column(column_id).visible(true);
                $('.select2').select2({
                    "language": "ru",
                });
                $('.select2').addClass('mb-2');
                $('.select2').css('font-weight', '400');
            } else {
                $containers_extended_table.DataTable().column(column_id).visible(false);
            }
            writeCookieContainersColumns();
        });

        $(document).on("click", ".containers_filters", function () {
            let filter_type = $(this).data('filter');
            let columns_show = [];

            $('.containers_filters').each(function () {
                $(this).removeClass('btn-secondary').addClass('btn-default');
            });
            $(this).removeClass('btn-default').addClass('btn-secondary');

            let all_columns = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30", "31", "32", "33", "34", "35", "36", "37", "38", "39", "40", "41", "42", "43", "44", "45", "46", "47", "48", "49", "50", "51", "52", "53", "54", "55", "56", "57", "58", "59", "60", "61", "62", "63"];
            switch (filter_type) {
                case 'main_info':
                    columns_show = ["0", "1", "4", "5", "6", "39", "40", "61"];
                    break;
                case 'svv':
                    columns_show = ["0", "1", "3", "4", "5", "6", "19"];
                    break;
                case 'free':
                    columns_show = ["0", "1", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15", "16", "17", "19", "22", "30", "31"];
                    break;
                case 'repair':
                    columns_show = ["0", "1", "3", "4", "5", "6", "23", "24", "25", "39", "40", "49", "50", "51", "61"];
                    break;
                case 'manual':
                    columns_show = ["0", "1", "3", "4", "5", "6", "18", "39", "40", "53", "55", "61"];
                    break;
                case 'smgs':
                    columns_show = ["0", "1", "3", "4", "5", "6", "39", "40", "44", "45", "52", "56", "61"];
                    break;
                case 'all':
                    columns_show = all_columns;
                    break;
            }

            let columns_for_hide = all_columns.filter(x => !columns_show.includes(x));
            filterContainerTable(filter_type);

            setTimeout(function () {
                all_columns.forEach((column_id) => {
                    if (columns_for_hide.includes(column_id) === true) {
                        if ($('#containers_extended_ajax_table').DataTable().column(column_id).visible() === true) {
                            $('#containers_extended_ajax_table').DataTable().column(column_id).visible(false, false);
                        }
                    }
                    if (columns_show.includes(column_id) === true) {
                        if ($('#containers_extended_ajax_table').DataTable().column(column_id).visible() === false) {
                            $('#containers_extended_ajax_table').DataTable().column(column_id).visible(true);
                        }
                    }
                });
                $('#containers_extended_table_div').removeClass('d-none');
                $('#containers_extended_ajax_table').DataTable().columns.adjust().draw();
            }, 1000);
        });

        function writeCookieContainersColumns() {
            let hidden_columns = [];
            $('.containers_table_columns_visibility').each(function () {
                if ($(this).is(":checked") === false) {
                    hidden_columns.push($(this).data('column_id'));
                }
            });
            $.cookie('containers_hidden_columns', JSON.stringify(hidden_columns), {expires: 365});
        }

        $(document).on("mouseenter", ".init_select2", function () {
            let select_id = $(this).attr('id');
            console.log(select_id);
            $(this).select2({
                "language": "ru",
                "multiple": true
            });
            $("#" + select_id + " option[value='']").remove();
            $("#" + select_id).addClass('mb-2');
            $("#" + select_id).css('font-weight', '400');
        });

        $(document).on("click", ".sorting_containers_table", function () {
            let column_id = $(this).data('column_id');
            let ordering_direction = $(this).data('ordering_direction');
            console.log(ordering_direction);
            $('#containers_extended_ajax_table').DataTable().order([column_id, ordering_direction]).draw();
            if (ordering_direction === 'asc') {
                $('#sorting_column_' + column_id).data('ordering_direction', 'desc');
            } else {
                $('#sorting_column_' + column_id).data('ordering_direction', 'asc');
            }
        });


        ////////////////////////////////////////////////////////////////////////////////


        $('.audits_table').each(function () {
            let filter_type = $(this).data('filter_type');
            if(filter_type !== undefined){
                audits_filter[filter_type] = $(this).data('id');
            }
            console.log(audits_filter);
            initAuditsTables();
        });

        $('.projects_ajax_table').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ordering: true,
            order: [0, 'desc'],
            info: true,
            autoWidth: false,
            responsive: true,
            ajax: {
                url: "{{route('get_projects_table')}}",
                data: {"filter": page_filter[1]}
            },
            language: {
                "url": "/admin/plugins/datatables-ru-lang/{{ auth()->user()->language }}.json"
            },
            columns: [
                {data: 'id'},
                {data: 'name'},
                {data: 'client_id'},
                {data: 'freight_amount'},
                {data: 'status'},
                {data: 'created_at'},
            ]
        });

        //// ---------------- Таблица Задачи ------------------////

        let task_global_filter = {};

        //задачи пользователя
        $('.tasks_user').each(function () {
            let user_id = $(this).data('user_id');
            task_global_filter = {
                "user_tasks": user_id
            };
        });

        if (jQuery.isEmptyObject(task_global_filter)) {
            task_global_filter = {
                "filter": task_filter[2],
            };
        }

        $task_table.DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ordering: true,
            order: [0, 'desc'],
            info: true,
            autoWidth: false,
            responsive: true,
            ajax: {
                url: "{{ route('get_tasks_table') }}",
                data: task_global_filter
            },
            language: {
                "url": "/admin/plugins/datatables-ru-lang/{{ auth()->user()->language }}.json"
            },
            columns: [
                {data: 'id'},
                {data: 'info'},
                {data: 'accepted_user_id'},
                {data: 'status'},
                {data: 'actions'},
            ]
        });

        $('.tasks_filters').on('click', function () {
            $('.tasks_filters').each(function () {
                $(this).removeClass('btn-secondary').addClass('btn-default');
            });
            $(this).removeClass('btn-default').addClass('btn-secondary');

            task_global_filter.status = $(this).data('filter');
            $task_table.DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                searching: true,
                ordering: true,
                order: [0, 'desc'],
                info: true,
                autoWidth: false,
                responsive: true,
                ajax: {
                    url: "{{ route('get_tasks_table') }}",
                    data: task_global_filter,
                },
                language: {
                    "url": "/admin/plugins/datatables-ru-lang/{{ auth()->user()->language }}.json",

                },
                columns: [
                    {data: 'id'},
                    {data: 'info'},
                    {data: 'accepted_user_id'},
                    {data: 'status'},
                    {data: 'actions'},
                ],
                preDrawCallback: function () {
                    $('.tasks_ajax_table').addClass('d-none');
                },
                drawCallback: function () {
                    $('.tasks_ajax_table').removeClass('d-none');
                }
            });
        });
        ///////////////////////////////////////////////////////////

        //// ---------------- Таблица Запросы ------------------////

        let work_request_global_filter = {};

        //задачи пользователя
        $('.work_requests_user').each(function () {
            let user_id = $(this).data('user_id');
            work_request_global_filter = {
                "user_tasks": user_id
            };
        });

        if (jQuery.isEmptyObject(work_request_global_filter)) {
            work_request_global_filter = {
                "filter": work_request_filter[2]
            };
        }

        $work_request_table.DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ordering: true,
            order: [0, 'desc'],
            info: true,
            autoWidth: false,
            responsive: true,
            ajax: {
                url: "{{ route('get_work_requests_table') }}",
                data: work_request_global_filter
            },
            language: {
                "url": "/admin/plugins/datatables-ru-lang/{{ auth()->user()->language }}.json"
            },
            columns: [
                {data: 'id'},
                {data: 'info'},
                {data: 'users'},
                {data: 'status'},
                {data: 'actions'},
            ]
        });

        $('.work_request_filters').on('click', function () {

            $('.work_request_filters').each(function () {
                $(this).removeClass('btn-secondary').addClass('btn-default');
            });
            $(this).removeClass('btn-default').addClass('btn-secondary');

            work_request_global_filter.status = $(this).data('filter');

            $work_request_table.DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                searching: true,
                ordering: true,
                order: [0, 'desc'],
                info: true,
                autoWidth: false,
                responsive: true,
                ajax: {
                    url: "{{ route('get_work_requests_table') }}",
                    data: work_request_global_filter,
                },
                language: {
                    "url": "/admin/plugins/datatables-ru-lang/{{ auth()->user()->language }}.json",

                },
                columns: [
                    {data: 'id'},
                    {data: 'info'},
                    {data: 'users'},
                    {data: 'status'},
                    {data: 'actions'},
                ],
                preDrawCallback: function () {
                    $work_request_table.addClass('d-none');
                },
                drawCallback: function () {
                    $work_request_table.removeClass('d-none');
                }
            });
        });
        ///////////////////////////////////////////////////////////

        $('#invoices_table_modal').DataTable({
            "paging": false,
            "lengthChange": false,
            "searching": false,
            "ordering": true,
            "info": false,
            "autoWidth": false,
            "responsive": true,
            "language": {
                "url": "/admin/plugins/datatables-ru-lang/{{ auth()->user()->language }}.json"
            }

        });

        $('.data_tables').DataTable({
            "paging": false,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "language": {
                "url": "/admin/plugins/datatables-ru-lang/{{ auth()->user()->language }}.json"
            }

        });

        $('.datatable_with_paging').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "language": {
                "url": "/admin/plugins/datatables-ru-lang/{{ auth()->user()->language }}.json"
            }

        });

        $('#users_table').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "language": {
                "url": "/admin/plugins/datatables-ru-lang/{{ auth()->user()->language }}.json"
            },
        });

        $('#projects_table').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "columns": [
                {responsivePriority: 1},
                {responsivePriority: 2},
                {responsivePriority: 3},
                {responsivePriority: 4},
                {responsivePriority: 5},
                {responsivePriority: 6}
            ],
            "language": {
                "url": "/admin/plugins/datatables-ru-lang/{{ auth()->user()->language }}.json"
            }
        });

        $('.datatable_without_search').DataTable({
            "paging": false,
            "lengthChange": false,
            "searching": false,
            "ordering": true,
            "info": false,
            "autoWidth": false,
            "responsive": true,
            "language": {
                "url": "/admin/plugins/datatables-ru-lang/{{ auth()->user()->language }}.json"
            }
        });

        //// ---------------- Таблица Клиенты ------------------////
        $clients_table.DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ordering: true,
            order: [0, 'desc'],
            info: true,
            autoWidth: false,
            responsive: true,
            ajax: {
                url: "{{ route('get_clients_table') }}",
                data: {"filter": page_filter[1]},
            },
            language: {
                "url": "/admin/plugins/datatables-ru-lang/{{ auth()->user()->language }}.json"
            },
            columns: [
                {data: 'id'},
                {data: 'name'},
                {data: 'requisites'},
                {data: 'info'},
                {data: 'resources'},
                {data: 'actions'},
            ]
        });

        $('.clients_filters').on('click', function () {
            $('.clients_filters').each(function () {
                $(this).removeClass('btn-secondary').addClass('btn-default');
            });
            $(this).removeClass('btn-default').addClass('btn-secondary');

            $clients_table.DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                searching: true,
                ordering: true,
                order: [0, 'desc'],
                info: true,
                autoWidth: false,
                responsive: true,
                ajax: {
                    url: "{{ route('get_clients_table') }}",
                    data: {"country": $(this).data('filter'), "filter": page_filter[1]},
                },
                language: {
                    "url": "/admin/plugins/datatables-ru-lang/{{ auth()->user()->language }}.json"
                },
                columns: [
                    {data: 'id'},
                    {data: 'name'},
                    {data: 'requisites'},
                    {data: 'info'},
                    {data: 'resources'},
                    {data: 'actions'},
                ],
                preDrawCallback: function () {
                    $clients_table.addClass('d-none');
                },
                drawCallback: function () {
                    $clients_table.removeClass('d-none');
                },
            });
        });

        //// ---------------- Таблица Поставщики ------------------////
        $suppliers_table.DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ordering: true,
            order: [0, 'desc'],
            info: true,
            autoWidth: false,
            responsive: true,
            ajax: {
                url: "{{ route('get_suppliers_table') }}",
                data: {"filter": page_filter[1]},
            },
            language: {
                "url": "/admin/plugins/datatables-ru-lang/{{ auth()->user()->language }}.json"
            },
            columns: [
                {data: 'id'},
                {data: 'name'},
                {data: 'requisites'},
                {data: 'info'},
                {data: 'resources'},
                {data: 'actions'},
            ]
        });

        $('.suppliers_filters').on('click', function () {
            $('.suppliers_filters').each(function () {
                $(this).removeClass('btn-secondary').addClass('btn-default');
            });
            $(this).removeClass('btn-default').addClass('btn-secondary');
            supplier_filters.type = $(this).data('filter');
            filterSupplierTable();
        });

        $('.suppliers_filters_country').on('click', function () {
            $('.suppliers_filters_country').each(function () {
                $(this).removeClass('btn-secondary').addClass('btn-default');
            });
            $(this).removeClass('btn-default').addClass('btn-secondary');
            supplier_filters.country = $(this).data('filter');
            filterSupplierTable();
        });
        ///////////////////////////////////////////////////////////

        $('#containers_table').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "language": {
                "url": "/admin/plugins/datatables-ru-lang/{{ auth()->user()->language }}.json"
            }

        });

        //таблицы проекты главная страница
        $('.projects_homepage').each(function (i) {
            let filter_type = ($(this).data('type'));
            setTimeout(function () {
                console.log(filter_type);
                $('#projects_ajax_table_content_' + filter_type).DataTable({
                    processing: true,
                    serverSide: true,
                    searching: true,
                    ordering: true,
                    order: [0, 'desc'],
                    info: true,
                    autoWidth: false,
                    responsive: true,
                    ajax: {
                        url: "{{ route('get_projects_table') }}",
                        data: {"filter": filter_type}
                    },
                    language: {
                        "url": "/admin/plugins/datatables-ru-lang/{{ auth()->user()->language }}.json"
                    },
                    columns: [
                        {data: 'id'},
                        {data: 'name'},
                        {data: 'client_id'},
                        {data: 'freight_amount'},
                        {data: 'status'},
                        {data: 'created_at'},
                    ]
                });
            }, 500 * i);
        });

        //таблица проекты аналитика проектов
        $('.projects_analytics').each(function () {
            let filter_type = ($(this).data('type'));
            $('#projects_ajax_table_content_' + filter_type).DataTable({
                processing: true,
                serverSide: true,
                searching: true,
                ordering: true,
                order: [0, 'desc'],
                info: true,
                autoWidth: false,
                responsive: true,
                ajax: {
                    url: "{{ route('get_projects_table') }}",
                    data: {"filter": filter_type, "data_range": $('#data_range').val()}
                },
                language: {
                    "url": "/admin/plugins/datatables-ru-lang/{{ auth()->user()->language }}.json"
                },
                columns: [
                    {data: 'id'},
                    {data: 'name'},
                    {data: 'client_id'},
                    {data: 'freight_amount'},
                    {data: 'status'},
                    {data: 'created_at'},
                ]
            });
        });

        //таблица проекты отчеты
        $('.projects_report').each(function () {
            let filter_type = ($(this).data('type'));
            let user_id = ($(this).data('user_id'));
            let manager_id = ($(this).data('manager_id'));
            let data_range = ($(this).data('range'));
            $('#projects_ajax_table_content_' + filter_type).DataTable({
                processing: true,
                serverSide: true,
                searching: true,
                ordering: true,
                order: [0, 'desc'],
                info: true,
                autoWidth: false,
                responsive: true,
                ajax: {
                    url: "{{ route('get_projects_table_with_filter') }}",
                    data: {
                        "filter": filter_type,
                        "user_id": user_id,
                        "manager_id": manager_id,
                        "data_range": data_range
                    }
                },
                language: {
                    "url": "/admin/plugins/datatables-ru-lang/{{ auth()->user()->language }}.json"
                },
                columns: [
                    {data: 'id'},
                    {data: 'name'},
                    {data: 'client_id'},
                    {data: 'freight_amount'},
                    {data: 'status'},
                    {data: 'created_at'},
                ]
            });
        });

        //таблица группы контейнеров в проектах
        $('.container_groups_project').each(function () {
            let group_id = ($(this).data('group_id'));
            console.log(group_id);
            $('#containers_group_' + group_id).DataTable({
                processing: true,
                serverSide: true,
                searching: true,
                ordering: true,
                columnDefs: [{
                    orderable: false,
                    targets: "no-sort"
                }],
                order: [0, 'desc'],
                info: true,
                autoWidth: false,
                responsive: true,
                ajax: {
                    url: "{{ route('get_container_group_table') }}",
                    data: {"group_id": group_id}
                },
                language: {
                    "url": "/admin/plugins/datatables-ru-lang/{{ auth()->user()->language }}.json"
                },
                columns: [
                    {data: 'id'},
                    {data: 'name'},
                    {data: 'dates'},
                    {data: 'usage'},
                    {data: 'place'},
                    {data: 'return'},
                ],
                drawCallback: function () {
                    $('.xedit').editable({
                        mode: 'inline',
                        url: '{{ url("xeditable/update") }}',
                        title: '{{ 'general.update_' }}',
                        emptytext: '{{ 'general.empty' }}',
                        params: function (params) {
                            params.model = $(this).data('model');
                            return params;
                        },
                        success: function (response, newValue) {
                            $('#containers_group_' + group_id).DataTable().draw(false);
                            if (response.status === 'error') alert('Ошибка');
                        }
                    });
                },
                createdRow: function (row, data, dataIndex) {
                    if (data.class !== '') {
                        $(row).addClass(data.class);
                    }
                }
            });
        });

        //таблица проекты пользователя
        $('.projects_user').each(function (i) {
            let filter_type = $(this).data('filter_type');
            let filter_data = {};

            $.each($(this).data(), function (i, v) {
                filter_data[i] = v;
            });

            setTimeout(function () {
                console.log(filter_type);
                $('#projects_ajax_table_content_' + filter_type).DataTable({
                    processing: true,
                    serverSide: true,
                    searching: true,
                    ordering: true,
                    order: [0, 'desc'],
                    info: true,
                    autoWidth: false,
                    responsive: true,
                    ajax: {
                        url: "{{ route('get_projects_table') }}",
                        data: filter_data
                    },
                    language: {
                        "url": "/admin/plugins/datatables-ru-lang/{{ auth()->user()->language }}.json"
                    },
                    columns: [
                        {data: 'id'},
                        {data: 'name'},
                        {data: 'client_id'},
                        {data: 'freight_amount'},
                        {data: 'status'},
                        {data: 'created_at'},
                    ]
                });
            }, 500 * i);
        });

        //таблица счета пользователя
        $('.invoices_user').each(function (i) {
            let filter_type = 'user';
            let user_name = $(this).data('user_name');
            let data = {"filter": 'user=' + user_name};
            let route_link = "{{ route('get_invoices_with_filter') }}";
            let table_id = 'invoices_ajax_table_content_user';
            setTimeout(function () {
                console.log(filter_type);
                initInvoiceDatatables(data, route_link, filter_type, table_id);
            }, 500 * i);
        });

        /// просмотр истории
        $('#view_component_history').on('show.bs.modal', function (event) {
            let button = $(event.relatedTarget);
            audits_filter[button.data('component')] = button.data('id');
            console.log(audits_filter);
            $('#audits_table_component_history').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                searching: false,
                ordering: true,
                pageLength: 10,
                lengthChange: false,
                order: [0, 'desc'],
                info: true,
                ajax: {
                    url: "{{route('get_component_history_table')}}",
                    type: "GET",
                    data: audits_filter
                },
                language: {
                    "url": "/admin/plugins/datatables-ru-lang/{{ auth()->user()->language }}.json"
                },
                columns: [
                    {data: "created_at"},
                    {data: "old_values"},
                    {data: "new_values"}
                ],
                drawCallback: function () {
                    console.log('draw complete')
                },
            });
        });
    });
</script>
