<script>
    var APP_URL = document.location.origin;
    var user_id = {!! json_encode((array)auth()->user()->id) !!};

    var loading_spinner = '<div id="loading_spinner" class="text-center">' +
        '  <div class="spinner-border" role="status">' +
        '    <span class="sr-only">Loading...</span>' +
        '  </div>' +
        '</div>';

    var loading_spinner_small = `<div class="spinner-border spinner-border-sm" role="status">
                                              <span class="sr-only">Loading...</span>
                                            </div>`;

    //$.fn.dataTable.ext.errMode = 'throw';

    function NavbarStyle(){
        if($.cookie('navbar') !== ''){
            if($.cookie('navbar') === 'collapsed'){
                $.cookie('navbar', 'normal', { expires: 7 });
            }
            else {
                $.cookie('navbar', 'collapsed', { expires: 7 });
            }
        }
        else $.cookie('navbar', 'collapsed', { expires: 7 });
    }

    function init_comments_editable(){
        console.log('start');
        ///// editable popup
        $('.xedit_edit_comment').each(function() {
            var comment_id = $(this).data('pk');
            $(this).editable({
                mode: 'popup',
                url: '{{url("xeditable/update")}}',
                title: '{{ __('general.update_') }}',
                emptytext: '{{ __('general.empty') }}',
                params: function(params) {
                    params.model = $(this).data('model');
                    params.task_id = $(this).data('task_id');
                    return params;
                },
                success: function (response, newValue) {
                    $('#'+response.div_id).html(response.ajax);
                    init_comments_editable();
                }.bind(this)
            }).on('hidden', function(e, reason) {
                if (reason === 'save' || reason === "cancel") {
                    $('#comment_default_'+comment_id).removeClass('d-none');
                    $('#comment_editable_'+comment_id).addClass('d-none');
                    console.log(comment_id);
                }
            });
        });
    }

    $(function () {
        jQuery('.invoice_deadline').datetimepicker({
            timepicker: false,
            format:'d.m.Y',
            lang:'{{ auth()->user()->language }}',
            minDate:0,
            scrollMonth : false,
            scrollInput : false
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.select2').select2({
            "language": "ru",
        });

        ///// editable inline
        $('.xedit').editable({
            mode: 'inline',
            url: '{{url("xeditable/update")}}',
            title: '{{ __('general.update_') }}',
            emptytext: '{{ __('general.empty') }}',
            params: function(params) {
                params.model = $(this).data('model');
                return params;
            },
            success: function(response) {
                if(!response.success){
                    $(document).Toasts('create', {
                        autohide: true,
                        delay: 3000,
                        class: 'bg-danger',
                        title: 'Ошибка',
                        body: response.error
                    });
                }
            }
        });

        if(location.pathname.split('/')[1] === 'task'){
            init_comments_editable();
        }

        if(location.pathname.split('/')[1] === 'work_request'){
            init_comments_editable();
        }

        $('.invoice_select2_ajax').select2({
            placeholder: '{{ __('general.enter_invoice_number_or_counterparty_name') }}',
            ajax: {
                url: '/select2-autocomplete-ajax',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term,
                        page: 'task_invoice'
                    };
                },
                processResults: function (data) {
                    return {
                        results:  $.map(data, function (item) {
                            return {
                                text: item.text,
                                id: item.id
                            }
                        })
                    };
                },
                cache: true
            }
        });
    });

    // инициализация редактирования комментариев проекта
    $('#add_file').on('show.bs.modal', function (event) {
        init_comments_editable();
    });

    /// просмотр счета
    $('#view_invoice').on('show.bs.modal', function (event) {
        $('#invoice_info').html('<h5 class="text-center">{{ __('general.loading_please_wait') }}</h5>');
        var button = $(event.relatedTarget);
        var id = button.data('invoice-id');
        var type = button.data('type');
        var table_id = button.closest('table').attr('id');
        console.log(type);
        $.ajax({
            type: "GET",
            url: APP_URL + "/invoice/get_invoice_by_id/" + id,
            success: function (response) {
                $('#invoice_info').html(response);
                $('.ajax_remove').attr('data-type',type);

                if ($(".submit_from_invoice_modal")[0]) {
                    var actions = $('.submit_from_invoice_modal').data('action');
                    Object.assign(actions, {
                        "update_table": {
                            "table_id": table_id,
                            "type": type,
                            "object": "invoice",
                            "id": id
                        }
                    });
                    $('.submit_from_invoice_modal').data('action', actions);
                }

                $('#create_invoice_draft_emit').attr('data-type', type);
                $('#create_invoice_draft_emit').attr('data-table_id', table_id);
                $('#edit_invoice_draft_emit').attr('data-type', type);
                $('#edit_invoice_draft_emit').attr('data-table_id', table_id);
                $('#create_invoice_draft_emit').attr('data-type', type);
                $('#create_invoice_draft_emit').attr('data-table_id', table_id);

                $(".digits_only").inputmask('9{1,}');
                $(".rate_input").inputmask(
                    "decimal", {
                        radixPoint: '.',
                        rightAlign: false
                    }
                );
                $('#invoices_table_modal').DataTable({
                    "paging": false,
                    "lengthChange": false,
                    "searching": false,
                    "ordering": true,
                    "info": false,
                    "autoWidth": false,
                    "responsive": true,
                    "language": {
                        "url": "/admin/plugins/datatables-ru-lang/ru.json"
                    }
                });
                jQuery('.invoice_deadline').datetimepicker({
                    timepicker: false,
                        format:'d.m.Y',
                    lang:'{{ auth()->user()->language }}',
                    minDate:0,
                    scrollMonth : false,
                    scrollInput : false
                });
            },
            error: function (XMLHttprequest, textStatus, errorThrown) {
                console.log(textStatus);
            }
        });
    });

    ///редактирование счета
    $('#edit_invoice_modal').on('show.bs.modal', function (event) {
        $('#edit_invoice_info').html('<h5 class="text-center">{{ __('general.loading_please_wait') }}</h5>');
        var button = $(event.relatedTarget);
        var id = button.data('invoice-id');
        var table_id = button.closest('table').attr('id');
        $(this).find('.modal-title').text('{{ __('invoice.edit_invoice') }} №'+id);

        $.ajax({
            type: "GET",
            url: APP_URL + "/invoice/edit_invoice_by_id/" + id,
            success: function (response) {
                $('#edit_invoice_info').html(response);
                $(".digits_only").inputmask('9{1,}');
                $(".rate_input").inputmask(
                    "decimal", {
                        radixPoint: '.',
                        rightAlign: false
                    }
                );
                $(".date_input").inputmask('99.99.9999');
                $('.select2').select2();
                var actions = $("#update_invoice_form").data('action');
                var type = button.data('type');
                Object.assign(actions, {
                    "update_table": {
                        "table_id": table_id,
                        "type": type,
                        "object": "invoice",
                        "id": id
                    }
                });
                $("#update_invoice_form").data('action', actions);

            },
            error: function (XMLHttprequest, textStatus, errorThrown) {
                console.log(textStatus);
            }
        });
    });

    /// просмотр изменений по счету
    $('#view_invoice_changes').on('show.bs.modal', function (event) {
        $('#invoice_changes').html('<h5 class="text-center">{{ __('general.loading_please_wait') }}</h5>');
        var button = $(event.relatedTarget);
        var id = button.data('invoice-id');

        $.ajax({
            type: "GET",
            url: APP_URL + "/invoice/get_invoice_changes/" + id,
            success: function (response) {
                $('#invoice_changes').html(response);
                $('.invoices_table_modal').DataTable({
                    "paging": false,
                    "lengthChange": false,
                    "searching": false,
                    "ordering": true,
                    "info": false,
                    "autoWidth": false,
                    "responsive": true,
                    "language": {
                        "url": "/admin/plugins/datatables-ru-lang/ru.json"
                    }
                });
            },
            error: function (XMLHttprequest, textStatus, errorThrown) {
                console.log(textStatus);
            }
        });
    });

    $('#save_project_plan').on('click', function () {
        choosed_items = new Array();

        $('#choosed_items li').each(function () {
            choosed_items.push($(this).html());
        });
        project_id = $('#project_id').val();
        $.ajax({
            type: "POST",
            url: APP_URL + "/project/save_plan",
            data: {
                action: 'create_plan',
                items: choosed_items,
                project_id: project_id
            },
            success: function () {
                window.location.replace(APP_URL + "/project/"+project_id);
            },
            error: function (XMLHttprequest, textStatus, errorThrown) {
                console.log(textStatus);
            }
        });
    });

    $('.change_user_permission').on('click', function () {
        var user_id = $(this).data('user-id');
        var permission_name = $(this).data('permission-name');
        var permission_ru_name = $(this).data('permission-ru-name');
        var action = '';

        if ($(this).prop('checked') === true) {
            action = 'add_permission';
        } else {
            action = 'remove_permission';
        }

        $.ajax({
            type: "POST",
            url: APP_URL + "/user/update_permissions",
            data: {
                action: action,
                user_id: user_id,
                permission_name: permission_name,
                permission_ru_name: permission_ru_name
            },
            success: function (response) {
                $(document).Toasts('create', {
                    class: response['type'],
                    title: response['user'],
                    body: response['message'],
                    autohide: true
                })
            },
            error: function (XMLHttprequest, textStatus, errorThrown) {
                console.log(textStatus);
            }
        });
    });

    $('#user_edit_change_password').change(function () {
        if ($(this).val() === 'change_password') {
            $('#user_edit_password_field').css('display', '');
        } else {
            $('#user_edit_password_field').css('display', 'none');
        }
    });

    $('#finance_direction').change(function () {
        if ($(this).val() === 'Клиенту') {
            $('#client_group').removeClass('d-none');
            $('#client_select').prop('required', true);
            $('#supplier_group').addClass('d-none');
            $('#supplier_select').prop('required', false);
            $('#supplier_group_agree_without_invoice').addClass('d-none');
        }
        else if ($(this).val() === 'Поставщику') {
            $('#supplier_group').removeClass('d-none');
            $('#supplier_select').prop('required', true);
            $('#client_group').addClass('d-none');
            $('#client_select').prop('required', false);
            $('#supplier_group_agree_without_invoice').removeClass('d-none');
        }
        else {
            $('#supplier_group').addClass('d-none');
            $('#supplier_select').prop('required', false);
            $('#client_group').addClass('d-none');
            $('#client_select').prop('required', false);
            $('#supplier_group_agree_without_invoice').addClass('d-none');
        }
    });

    $(document).on('change', '.finance_direction', function() {
        if ($(this).val() === 'Клиенту') {
            $('.client_group').removeClass('d-none');
            $('.client_select').prop('required', true);
            $('.supplier_group').addClass('d-none');
            $('.supplier_select').prop('required', false);
            $('.supplier_group_agree_without_invoice').addClass('d-none');
        }
        else if ($(this).val() === 'Поставщику') {
            $('.supplier_group').removeClass('d-none');
            $('.supplier_select').prop('required', true);
            $('.client_group').addClass('d-none');
            $('.client_select').prop('required', false);
            $('.supplier_group_agree_without_invoice').removeClass('d-none');
        }
        else {
            $('.supplier_group').addClass('d-none');
            $('.supplier_select').prop('required', false);
            $('.client_group').addClass('d-none');
            $('.client_select').prop('required', false);
            $('.supplier_group_agree_without_invoice').addClass('d-none');
        }
    });

    $('#supplier_select').change(function () {
        if($(this).val() === '311'){
            $('#add_invoice_additional_info').prop('required', true);
            $('#add_invoice_additional_info').attr("placeholder", "{{ __('invoice.add_phone_or_card_number') }}");
        }
        else {
            $('#add_invoice_additional_info').prop('required', false);
            $('#add_invoice_additional_info').attr("placeholder", "{{ __('general.additional_info') }}");
        }
    });
    $('#client_select').change(function () {
        console.log($(this).val());
        if($(this).val() === '52'){
            $('#add_invoice_additional_info').prop('required', true);
            $('#add_invoice_additional_info').attr("placeholder", "{{ __('invoice.add_phone_or_card_number') }}");
        }
        else {
            $('#add_invoice_additional_info').prop('required', false);
            $('#add_invoice_additional_info').attr("placeholder", "{{ __('general.additional_info') }}");
        }
    });

    $('#container_group_add_type').change(function () {
        if ($(this).val() === 'new_group') {
            $('#container_new_group').css('display', '');
            $('.chosen_group_to_add').prop('required', false);
            $('#add_to_group').css('display', 'none');
            $('.group_name').prop('required', true);
        }
        if ($(this).val() === 'add_to_group') {
            $('#container_new_group').css('display', 'none');
            $('.chosen_group_to_add').prop('required', true);
            $('#add_to_group').css('display', '');
            $('.group_name').prop('required', false);
        }
    });

    $('#container_group_add_type_list').change(function () {
        if ($(this).val() === 'new_group') {
            $('#container_new_group_list').removeClass('d-none');
            $('.chosen_group_to_add').prop('required', false);
            $('#add_to_group_list').addClass('d-none');
            $('.group_name').prop('required', true);
        }
        if ($(this).val() === 'add_to_group') {
            $('#container_new_group_list').addClass('d-none');
            $('.chosen_group_to_add').prop('required', true);
            $('#add_to_group_list').removeClass('d-none');
            $('.group_name').prop('required', false);
        }
    });

    jQuery('.add_contract_item').click(function(){
        jQuery('.contract-items').after(
            '<div class="contract-items_2">' +
            '<div class="card card-body">'+
            '<div class="form-group">'+
            '<label for="contract">{{ __('contract.contract_number') }}</label>' +
            '<input type="text" class="form-control" name="contract[name][]" placeholder="{{ __('contract.contract_number') }}" required>'+
            '</div>'+
            '<div class="form-group">'+
            '<label for="contract">{{ __('contract.date_of_sign') }}</label>'+
            '<input type="text" class="form-control date_input" name="contract[date_start][]" placeholder="{{ __('contract.date_of_sign') }}" required>'+
            '</div>'+
            '<div class="form-group">'+
            '<label for="contract">{{ __('contract.valid_before') }}</label>'+
            '<input type="text" class="form-control date_input" name="contract[date_period][]" placeholder="{{ __('contract.valid_before') }}" required>'+
            '</div>'+
            '<div class="form-group">'+
            '<label for="additional_info">{{ __('contract.contract_type') }}</label>'+
            '<input type="text" class="form-control" name="contract[additional_info][]" placeholder="{{ __('contract.contract_type') }}" required>'+
            '</div>'+
            '<div class="form-group">'+
            '<label for="contract">{{ __('contract.contract_file') }}</label>'+
            '<input type="file" class="form-control-file" name="contract[file][]" required>'+
            '</div>'+
            '<div class="form-group">'+
            '<span class="btn btn-danger remove_contract_item">{{ __('contract.remove_contract') }}</span>'+
            '</div>'+
            '</div>'+
            '</div>'
        );
        $(".date_input").inputmask('99.99.9999');
    });

    jQuery(document).on('click', '.remove_contract_item', function(){
        jQuery( this ).closest( '.contract-items_2' ).remove();
    });

    $('.carousel').carousel({
        interval: 10000
    });

    ///// блок расходы при создании проекта

    $(document).ready(function(){
        let expense_types = ['Контейнер', 'Авто', 'Платформа', 'ТЭО'];

        expense_types = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.whitespace,
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            local: expense_types
        });

        $('.twitter-typeahead').typeahead({
            hint: true,
            highlight: true,
            minLength: 1
        },{
            name: 'expense_types',
            source: expense_types
        });
    });

    let expense_i = 1;
    max_expense_i = Number($('#max_expense_i').val());
    if(max_expense_i >= expense_i) expense_i = max_expense_i+1;

    // function expenseClients() {
    //
    //     $('#expense_client')
    //         .empty()
    //         .append('<option value="all">Все</option>')
    //     ;
    //     var clients = $('#additional_client').select2('data');
    //     if(clients){
    //         clients.push(
    //             {
    //                 id: $('#main_client').val(),
    //                 text: $('#main_client option:selected').text()
    //             }
    //         );
    //     }
    //     else clients = {
    //         id: $('#main_client').val(),
    //         text: $('#main_client option:selected').text()
    //     };
    //
    //     var arrayLength = clients.length;
    //
    //     for (var i = 0; i < arrayLength; i++) {
    //
    //         $('#expense_client').append($('<option>', {
    //             value: clients[i].id,
    //             text: clients[i].text
    //         }));
    //
    //     }
    //
    // }

    $('#add_expense').click(function(){
        expense_client = $('#expense_client option:selected').text();
        expense_client_id = $('#expense_client').val();

        if(expense_client_id !== 'all'){
            expense_type = 'client_'+expense_client_id+'_'+expense_i;
            expense_name = $("#expense_type" ).val()+' '+$('#expense_description').val()+' - '+expense_client;
        }
        else {
            expense_type = 'exp_'+expense_i;
            expense_name = $("#expense_type" ).val()+' '+$('#expense_description').val();
        }

        let usd_rate = $('#usd_rate').val();
        let cny_rate = $('#cny_rate').val();
        let usd_divided = $('#usd_divided').val();
        let cny_divided = $('#cny_divided').val();
        let usd_ratio = $('#usd_ratio').val();
        let cny_ratio = $('#cny_ratio').val();

        $('.expense-blocks').append(
            '<div class="col-md-4" id="'+expense_type+'">'+
            '<input type="hidden" name="expenses_array['+expense_i+'][type]" value="'+expense_type+'">'+
            '<input type="hidden" name="expenses_array['+expense_i+']['+expense_type+'_name]" value="'+expense_name+'">'+
            '<div class="card">'+
            '<div class="card-header">'+
            '<h3 class="card-title">'+expense_name+'</h3>'+
            '<div class="card-tools">'+
            '<button type="button" class="btn btn-tool remove_expense_block" data-block-delete="'+expense_type+'">'+
            '<i class="fas fa-trash"></i>'+
            '</button>'+
            '</div>'+
            '</div>'+
            '<div class="card-body">'+
            '<div class="form-group">'+
            '<label>{{ __('general.currency') }}</label>'+
            '<select class="form-control choose_currency" name="expenses_array['+expense_i+']['+expense_type+'_currency]" data-type="'+expense_type+'" data-placeholder="{{ __('general.currency') }}" style="width: 100%;" required>'+
            '<option value="RUB" data-currency-rate="1">Рубль</option>'+
            '<option value="USD" data-currency-rate="'+usd_rate+'" data-divided="'+usd_divided+'" data-ratio="'+usd_ratio+'">{{ __('general.usd') }}, {{ __('general.cb_rate') }} '+usd_rate+'</option>'+
            '<option value="CNY" data-currency-rate="'+cny_rate+'" data-divided="'+cny_divided+'" data-ratio="'+cny_ratio+'">{{ __('general.cny') }}, {{ __('general.cb_rate') }} '+cny_rate+'</option>'+
            '</select>'+
            '</div>'+
            '<div class="form-group d-none" id="'+expense_type+'_rate_div">'+
            '<label>{{ __('general.cb_rate_corrected') }}</label>'+
            '<input class="form-control rate" type="text" name="expenses_array['+expense_i+']['+expense_type+'_rate]" id="'+expense_type+'_rate" placeholder="{{ __('general.cb_rate_corrected') }}" value="1">'+
            '</div>'+
            '<div class="form-group">'+
            '<label>{{ __('project.1pc_price') }}</label>'+
            '<input class="form-control digits_only price_1pc" type="text" id="'+expense_type+'_price_1pc"            name="expenses_array['+expense_i+']['+expense_type+'_price_1pc]" placeholder="{{ __('project.1pc_price') }}" value="0">'+
            '</div>'+
            '<div class="form-group">'+
            '<label>{{ __('general.quantity') }}</label>'+
            '<input class="form-control digits_only amount" id="'+expense_type+'_amount" data-type="'+expense_type+'" type="text" name="expenses_array['+expense_i+']['+expense_type+'_amount]" placeholder="{{ __('general.quantity') }}" value="1">'+
            '</div>'+
            '<div class="form-group">'+
            '<label>{{ __('general.price_in_rubles') }}</label>'+
            '<input class="form-control digits_only expenses" id="'+expense_type+'_total_price_in_rub" type="text" name="expenses_array['+expense_i+']['+expense_type+'_total_price_in_rub]" placeholder="{{ __('general.price_in_rubles') }}" value="0">'+
            '</div>'+
            '</div>'+
            '</div>'+
            '</div>'
        );
        $(".digits_only").inputmask('9{1,}');
        expense_i++;
    });

    jQuery(document).on('click', '.remove_expense_block', function () {
        block_remove = $(this).data('block-delete');
        amount_this_block = Number($('#'+block_remove+'_total_price_in_rub').val());
        console.log(amount_this_block);
        costs = Number($('#project_planned_costs').val());
        profit = Number($('#project_planned_profit').val());
        $('#project_planned_costs').val(costs-amount_this_block);
        $('#project_planned_profit').val(profit+amount_this_block);
        $('#'+block_remove).remove();
    });

    jQuery(document).on('change', '.choose_currency', function () {
        expense_type = $(this).data('type');
        selected = $(this).find(':selected');
        rate = selected.data('currency-rate');
        divided = selected.data('divided');
        ratio = selected.data('ratio');

        console.log(expense_type);

        if (selected.val() !== 'RUB'){
            $('#'+expense_type+'_rate_div').removeClass('d-none');
            $('#'+expense_type+'_rate').val(divided.toFixed(2));
            $('#ratio').html('(минус '+ratio+')');
        }
        else {
            $('#'+expense_type+'_rate_div').addClass('d-none');
            $('#'+expense_type+'_rate').val(rate);
            $('#ratio').html('');
        }

    });

    jQuery(document).on('keyup', '.amount, .price_1pc', function () {
        expense_type = $(this).data('type');
        quantity = $('#'+expense_type+'_amount').val();
        price = $('#'+expense_type+'_price_1pc').val();
        rate = $('#'+expense_type+'_rate').val();

        $('#'+expense_type+'_total_price_in_rub').val((quantity*price*rate).toFixed(2));
        $('#'+expense_type+'_price_in_currency').val((quantity*price).toFixed(2));

        costs = 0;

        $('.expenses').each(function () {
            costs += Number($(this).val());
        });

        $('#project_planned_costs').val(costs);

        revenue = $('#project_total_price_in_rub').val();
        $('#project_planned_revenue').val(revenue);
        $('#project_planned_profit').val(revenue-costs);

    });

    /////контейнерные проекты блок расходы
    $(document).on('click','#add_expense_cp',function(){

        $('#cp_expenses_save').removeClass('d-none');

        expense_type = 'exp_'+expense_i;

        let usd_rate = $('#usd_rate').val();
        let cny_rate = $('#cny_rate').val();
        let usd_divided = $('#usd_divided').val();
        let cny_divided = $('#cny_divided').val();
        let usd_ratio = $('#usd_ratio').val();
        let cny_ratio = $('#cny_ratio').val();

        $('.expense-blocks').append(
            '<div class="col-md-6" id="'+expense_type+'">'+
            '<input type="hidden" name="expenses_array['+expense_i+'][type]" value="'+expense_type+'">'+
            '<div class="card">'+
            '<div class="card-body">'+
            '<div class="form-group">'+
            '<label>{{ __('container.expense_type') }}</label>'+
            '<input class="form-control to_uppercase" type="text" name="expenses_array['+expense_i+']['+expense_type+'_name]" placeholder="{{ __('container.expense_type') }}">'+
            '</div>'+
            '<div class="form-group">'+
            '<label>{{ __('general.currency') }}</label>'+
            '<select class="form-control choose_currency_cp" name="expenses_array['+expense_i+']['+expense_type+'_currency]" data-type="'+expense_type+'" data-placeholder="Выбери валюту" style="width: 100%;" required>'+
            '<option value="RUB" data-currency-rate="1">Рубль</option>'+
            '<option value="USD" data-currency-rate="'+usd_rate+'" data-divided="'+usd_divided+'" data-ratio="'+usd_ratio+'">{{ __('general.usd') }}, {{ __('general.cb_rate') }} '+usd_rate+'</option>'+
            '<option value="CNY" data-currency-rate="'+cny_rate+'" data-divided="'+cny_divided+'" data-ratio="'+cny_ratio+'">{{ __('general.cny') }}, {{ __('general.cb_rate') }} '+cny_rate+'</option>'+
            '</select>'+
            '</div>'+
            '<div class="form-group d-none" id="'+expense_type+'_rate_div">'+
            '<label>{{ __('general.cb_rate_corrected') }}</label>'+
            '<input class="form-control rate" type="text" name="expenses_array['+expense_i+']['+expense_type+'_rate]" id="'+expense_type+'_rate" placeholder="{{ __('general.cb_rate_corrected') }}" value="1">'+
            '</div>'+
            '<div class="form-group">'+
            '<label>{{ __('general.price_in_currency') }}</label>'+
            '<input class="form-control digits_only price_in_currency_cp" type="text" data-type="'+expense_type+'" id="'+expense_type+'_price_in_currency_cp"            name="expenses_array['+expense_i+']['+expense_type+'_price_in_currency]" placeholder="{{ __('general.price_in_currency') }}" value="0">'+
            '</div>'+
            '<input type="hidden" id="'+expense_type+'_amount" data-type="'+expense_type+'" value="1">'+
            '<div class="form-group">'+
            '<label>{{ __('general.price_in_rubles') }}</label>'+
            '<input class="form-control digits_only expenses" id="'+expense_type+'_total_price_in_rub" type="text" name="expenses_array['+expense_i+']['+expense_type+'_total_price_in_rub]" placeholder="{{ __('general.price_in_rubles') }}" value="0">'+
            '</div>'+
            '<button type="button" class="btn btn-sm btn-danger remove_expense_block_cp float-right" data-block-delete="'+expense_type+'">'+
            '<i class="fas fa-trash"></i> {{ __('general.remove') }}'+
            '</button>'+
            '</div>'+
            '</div>'+
            '</div>'
        );
        $(".digits_only").inputmask('9{1,}');
        expense_i++;
    });

    jQuery(document).on('click', '.remove_expense_block_cp', function () {
        $('#'+$(this).data('block-delete')).remove();
    });

    jQuery(document).on('change', '.choose_currency_cp', function () {
        expense_type = $(this).data('type');
        selected = $(this).find(':selected');
        rate = selected.data('currency-rate');
        divided = selected.data('divided');
        ratio = selected.data('ratio');
        console.log(divided);

        if (selected.val() !== 'RUB'){
            $('#'+expense_type+'_rate_div').removeClass('d-none');
            $('#'+expense_type+'_rate').val(divided.toFixed(2));
            $('#ratio').html('({{ __('general.minus') }} '+ratio+')');
        }
        else {
            $('#'+expense_type+'_rate_div').addClass('d-none');
            $('#'+expense_type+'_rate').val(rate);
            $('#ratio').html('');
        }

    });

    jQuery(document).on('keyup', '.price_in_currency_cp', function () {
        expense_type = $(this).data('type');
        price = $('#'+expense_type+'_price_in_currency_cp').val();
        rate = $('#'+expense_type+'_rate').val();

        $('#'+expense_type+'_total_price_in_rub').val((price*rate).toFixed(2));

    });

    /////валюта при создании расходов / доходов
    jQuery(document).on('change', '#invoice_currency', function () {
        selected = $(this).find(':selected');
        rate = selected.data('currency-rate');
        divided = selected.data('divided');
        ratio = selected.data('ratio');

        if (selected.val() !== 'RUB'){
            $('#invoice_rate_div').removeClass('d-none');
            $('#invoice_amount_in_currency_div').removeClass('d-none');
            $('#invoice_rate').val(divided);
            $('#ratio').html(ratio);
        }
        else {
            $('#invoice_rate_div').addClass('d-none');
            $('#invoice_amount_in_currency_div').addClass('d-none');
            $('#invoice_rate').val(rate);
            $('#ratio').html('');
        }

    });

    jQuery(document).on('keyup', '#invoice_amount_in_currency', function () {
        price = $('#invoice_amount_in_currency').val();
        rate = $('#invoice_rate').val();

        $('#invoice_total_price_in_rub').val((price*rate).toFixed(2));

    });

    jQuery(document).on('keyup', '#this_invoice_payment_in_currency', function () {
        price = $('#this_invoice_payment_in_currency').val();
        rate = $('#invoice_rate').val();

        $('#this_invoice_payment_in_rubles').val((price*rate).toFixed(2));

    });

    ////Редактирование счета

    jQuery(document).on('keyup', '#edit_invoice_amount_out_date, #rate_out_date', function () {
        price = $('#edit_invoice_amount_out_date').val();
        rate = $('#rate_out_date').val();

        $('#edit_invoice_amount_out_date_in_rubles').val((price*rate).toFixed(2));

    });

    jQuery(document).on('keyup', '#edit_invoice_amount_actual, #rate_income_date', function () {
        price = $('#edit_invoice_amount_actual').val();
        rate = $('#rate_income_date').val();

        $('#edit_invoice_amount_actual_in_rubles').val((price*rate).toFixed(2));

    });

    jQuery(document).on('keyup', '#edit_invoice_amount_income_date, #rate_income_date', function () {
        price = $('#edit_invoice_amount_income_date').val();
        rate = $('#rate_income_date').val();

        $('#edit_invoice_amount_income_date_in_rubles').val((price*rate).toFixed(2));

    });

    jQuery(document).on('change', '#edit_invoice_currency', function () {
        if($(this).find('option:selected').val() !== 'RUB'){
            $('.currency-dnone').removeClass('d-none');
        }
        else {
            $('.currency-dnone').addClass('d-none');
        }
    });


    ////страна клиента

    jQuery(document).on('change', '#client_country', function () {
        if ($(this).val() != 'Россия'){
            $('.client_status_div').addClass('d-none');
        }
        else {
            $('.client_status_div').removeClass('d-none');
        }

    });

    //маски ввода

    $(document).ready(function(){
        $(".digits_only").inputmask('9{1,}');

        $(".date_input").inputmask('99.99.9999');

        $('.project-name').inputmask({regex: "([A-Za-zА-Яа-я0-9_-\\s]+)"});

        $('.to_uppercase').keyup(function(event) {
            var textBox = event.target;
            var start = textBox.selectionStart;
            var end = textBox.selectionEnd;
            textBox.value = textBox.value.charAt(0).toUpperCase() + textBox.value.slice(1);
            textBox.setSelectionRange(start, end);
        });

        $("#container_name").inputmask({
            mask: ['AAAA9999999' ],
            definitions: {
                "A": {
                    validator: "[A-Za-z]",
                    casing: "upper"
                }
            }
        });

        $(".rate_input").inputmask(
            "decimal", {
                radixPoint: '.',
                rightAlign: false
            }
        );

        if(document.querySelector('#list_of_all_items')){
            $(function () {
                Sortable.create(list_of_all_items, {
                    group: {
                        name: 'shared',
                        pull: 'clone',
                    },
                    animation: 100,
                    draggable: '.list-group-item',
                    handle: '.list-group-item',
                    sort: false,
                    filter: '.sortable-disabled'
                });

                Sortable.create(choosed_items, {
                    group: {
                        name: 'shared',
                    },
                    handle: '.list-group-item',
                    chosenClass: 'active'
                });

            });
        }

    });

    /////////////////////Задачи
    $.fn.modal.Constructor.prototype._enforceFocus = function() {};

    $(document).on('click', '.task_handler', function(e) {
        $('.dropdown-menu').removeClass('show');
        $(this).attr('disabled','disabled');
        task_id = $(this).data('task_id');
        action = $(this).data('action');
        to_users = $(this).data('to_users');
        send_to = $(this).data('send_to');
        reload = $(this).data('reload');
        redirect_to_task = $(this).data('redirect_to_task');
        task_comment = $('#comment').val();
        deadline = $('#task_deadline').val();

        $.ajax({
            type: "POST",
            url: APP_URL + "/task/handler",
            data: {
                action: action,
                task_id: task_id,
                to_users: to_users,
                send_to: send_to,
                comment: task_comment,
                deadline: deadline,
            },
            success: function(data) {
                $(document).Toasts('create', {
                    autohide: true,
                    delay: 3000,
                    class: data['bg-class'],
                    title: '{{ __('general.notification_from') }} ' + data['from'],
                    body: data['message']
                });
                console.log(reload)
                if(reload === true){
                    setTimeout(function(){
                        location.reload();
                    }, 1000);
                }
                if(redirect_to_task === true){
                    window.location.replace(APP_URL + "/task/" + task_id);
                }

                if(data.div_id !== 'undefined'){
                    $('#'+data.div_id).html(data.ajax);
                    jQuery('.task_deadline').datetimepicker({
                        format:'d.m.Y H:i',
                        lang: '{{ auth()->user()->language }}',
                        minDate:0,
                        scrollMonth : false,
                        scrollInput : false
                    });
                }
                else{
                    setTimeout(function(){
                        location.reload();
                    }, 1000);
                }
            },
            error: function (XMLHttprequest, textStatus, errorThrown) {
                console.log(textStatus);
            }
        });
    });

    $(document).on('click', '.task_handler_comments', function(e) {
        console.log('start');
        $(this).attr('disabled','disabled');
        task_id = $(this).data('task_id');
        action = $(this).data('action');
        to_users = $(this).data('to_users');
        send_to = $(this).data('send_to');
        answer_to = $(this).data('answer_to');
        task_comment = $('#comment').val();
        message_id = $(this).data('message_id');
        notify_users = $('#comment_notify_users').val();

        datasend =  {
            action: action,
            task_id: task_id,
            to_users: to_users,
            send_to: send_to,
            comment: task_comment,
            answer_to: answer_to,
            message_id: message_id,
            notify_users: notify_users
        };

        $.ajax({
            type: "POST",
            url: APP_URL + "/task/handler",
            data: datasend,
            success: function(data) {
                $(document).Toasts('create', {
                    autohide: true,
                    delay: 3000,
                    class: data['bg-class'],
                    title: '{{ __('general.notification_from') }} ' + data['from'],
                    body: data['message']
                });
                $('#'+data.div_id).html(data.ajax);
                if(data.div_id !== ''){
                    $('#task_handler_submit').removeAttr('disabled');
                    $('#comment').val('');
                }
                else{
                    setTimeout(function(){
                        location.reload();
                    }, 1000);
                }
            },
            error: function (XMLHttprequest, textStatus, errorThrown) {
                console.log(textStatus);
            }
        });
    });

    $(document).on("click", '.task_handler_answer', function () {

        let selected_users = $("#comment_notify_users").val();
        selected_users.push($(this).data('notify_user'));

        $("#comment_notify_users").val(selected_users).trigger("change");

        $('#comment').focus();
        $('#comment').val($(this).data('name')+', ');

        $('#task_handler_submit').attr('data-answer_to', $(this).data('answer_to'));
    });

    $('.create_task_modal_select2').select2({
        dropdownParent: $('#create_task_modal')
    });

    $('.create_task_to_users').on('change', function () {
        selected = $(this).find(':selected').data('create_task_send_to');
        console.log(selected);
        $('.create_task_send_to').val(selected);
    });

    document.addEventListener("livewire:load", () => {
        Livewire.hook('message.processed', (message, component) => {
            console.log('processed');
            $('.select2_livewire').select2();
            $('.invoice_select2_ajax').select2({
                placeholder: '{{ __('general.enter_invoice_number_or_counterparty_name') }}',
                ajax: {
                    url: '/select2-autocomplete-ajax',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term,
                            page: 'task_invoice'
                        };
                    },
                    processResults: function (data) {
                        console.log(data);
                        return {
                            results:  $.map(data, function (item) {
                                return {
                                    text: item.text,
                                    id: item.id
                                }
                            })
                        };
                    },
                    cache: true
                }
            });
        });

    });

    $('#create_task_modal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var model = button.data('model');
        var model_id = button.data('model-id');
        var text = button.data('text');
        var selected_user = button.data('user');
        var send_to = button.data('send_to');

        window.livewire.emit('set:model',model, model_id, text, selected_user, send_to);

        setTimeout(function(){
            jQuery.datetimepicker.setLocale('{{ auth()->user()->language }}');
            jQuery('.task_deadline').datetimepicker({
                format: 'd.m.Y H:i',
                lang: '{{ auth()->user()->language }}',
                minDate: 0,
                scrollMonth : false,
                scrollInput : false
            });
            console.log('datetimepicker');
        }, 1000);

    });

    /////////////////////Запросы
    $.fn.modal.Constructor.prototype._enforceFocus = function() {};

    $(document).on('click', '.work_request_handler', function(e) {
        $('.dropdown-menu').removeClass('show');
        $(this).attr('disabled','disabled');
        work_request_id = $(this).data('work_request_id');
        action = $(this).data('action');
        to_users = $(this).data('to_users');
        send_to = $(this).data('send_to');
        reload = $(this).data('reload');
        redirect_to_work_request = $(this).data('redirect_to_work_request');
        work_request_comment = $('#comment').val();
        deadline = $('#work_request_deadline').val();

        $.ajax({
            type: "POST",
            url: APP_URL + "/work_request/handler",
            data: {
                action: action,
                work_request_id: work_request_id,
                to_users: to_users,
                send_to: send_to,
                comment: work_request_comment,
                deadline: deadline,
            },
            success: function(data) {
                $(document).Toasts('create', {
                    autohide: true,
                    delay: 3000,
                    class: data['bg-class'],
                    title: '{{ __('general.notification_from') }} ' + data['from'],
                    body: data['message']
                });
                console.log(reload)
                if(reload === true){
                    setTimeout(function(){
                        location.reload();
                    }, 1000);
                }
                if(redirect_to_work_request === true){
                    window.location.replace(APP_URL + "/work_request/" + work_request_id);
                }

                if(data.div_id !== 'undefined'){
                    $('#'+data.div_id).html(data.ajax);
                    jQuery('.work_request_deadline').datetimepicker({
                        format:'d.m.Y H:i',
                        lang: '{{ auth()->user()->language }}',
                        minDate:0,
                        scrollMonth : false,
                        scrollInput : false
                    });
                }
                else{
                    setTimeout(function(){
                        location.reload();
                    }, 1000);
                }
            },
            error: function (XMLHttprequest, textStatus, errorThrown) {
                console.log(textStatus);
            }
        });
    });

    $(document).on('click', '.work_request_handler_comments', function(e) {
        $(this).attr('disabled','disabled');
        work_request_id = $(this).data('work_request_id');
        action = $(this).data('action');
        to_users = $(this).data('to_users');
        send_to = $(this).data('send_to');
        answer_to = $(this).data('answer_to');
        work_request_comment = $('#comment').val();
        message_id = $(this).data('message_id');
        notify_users = $('#comment_notify_users').val();

        datasend =  {
            action: action,
            work_request_id: work_request_id,
            to_users: to_users,
            send_to: send_to,
            comment: work_request_comment,
            answer_to: answer_to,
            message_id: message_id,
            notify_users: notify_users
        };

        $.ajax({
            type: "POST",
            url: APP_URL + "/work_request/handler",
            data: datasend,
            success: function(data) {
                $(document).Toasts('create', {
                    autohide: true,
                    delay: 3000,
                    class: data['bg-class'],
                    title: '{{ __('general.notification_from') }} ' + data['from'],
                    body: data['message']
                });
                $('#'+data.div_id).html(data.ajax);
                if(data.div_id !== ''){
                    $('#work_request_handler_submit').removeAttr('disabled');
                    $('#comment').val('');
                }
                else{
                    setTimeout(function(){
                        location.reload();
                    }, 1000);
                }
            },
            error: function (XMLHttprequest, textStatus, errorThrown) {
                console.log(textStatus);
            }
        });
    });

    $(document).on("click", '.work_request_handler_answer', function () {

        let selected_users = $("#comment_notify_users").val();

        selected_users.push($(this).data('notify_user'));

        $("#comment_notify_users").val(selected_users).trigger("change");

        $('#comment').focus();
        $('#comment').val($(this).data('name')+', ');

        $('#work_request_handler_submit').attr('data-answer_to', $(this).data('answer_to'));
    });

    $('.create_work_request_modal_select2').select2({
        dropdownParent: $('#create_work_request_modal')
    });

    $('#create_work_request_to_users').on('change', function () {
        selected = $(this).find(':selected').data('create_work_request_send_to');
        console.log(selected);
        $('#create_work_request_send_to').val(selected);
    });

    $('#create_work_request_modal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var model = button.data('model');
        var model_id = button.data('model-id');
        var text = button.data('text');
        var selected_user = button.data('user');
        var send_to = button.data('send_to');

        window.livewire.emit('set:model',model, model_id, text, selected_user, send_to);

        setTimeout(function(){
            jQuery.datetimepicker.setLocale('{{ auth()->user()->language }}');
            jQuery('.work_request_deadline').datetimepicker({
                format:'d.m.Y H:i',
                lang:'{{ auth()->user()->language }}',
                minDate:0,
                scrollMonth : false,
                scrollInput : false
            });
            console.log('datetimepicker');
        }, 1000);

    });

    ////комментарии к проекту
    $(document).on('click', '.project_handler_comments', function(e) {
        let action = $(this).data('action');
        console.log(action);
        if (action === 'answer'){
            $("#project_comment_answer_to").val($(this).data('answer_to'));

            let selected_users = $("#comment_notify_users").val();
            selected_users.push($(this).data('notify_user'));
            $("#comment_notify_users").val(selected_users).trigger("change");

            $('#comment').focus();
            $('#comment').val($(this).data('name')+', ');
        }
        if (action === 'delete_chat_record'){
            $.ajax({
                type: "POST",
                url: APP_URL + "/project_comment/remove",
                data: {'comment_id': $(this).data('comment_id')},
                beforeSend:function(){
                    return confirm("{{ __('general.are_you_sure') }}");
                },
                success: function(data) {
                    $(document).Toasts('create', {
                        autohide: true,
                        delay: 3000,
                        class: data['bg-class'],
                        title: '{{ __('general.notification_from') }} ' + data['from'],
                        body: data['message']
                    });
                    $('#project_additional_info').html(data.ajax);
                    setTimeout(function(){
                        init_comments_editable();
                    }, 1000);
                },
                error: function (XMLHttprequest, textStatus, errorThrown) {
                    console.log(textStatus);
                }
            });
        }
        if (action === 'change_comment'){
            let comment_id = $(this).data('comment_id');

            $('#comment_default_'+comment_id).addClass('d-none');
            $('#comment_editable_'+comment_id).removeClass('d-none');

            setTimeout(function(){
                $('#editable_comment_link_'+comment_id).click();
            }, 100);
        }
    });

    ///////создать инвойс

    $('#create_invoice_modal').on('show.bs.modal', function (event) {
        $(".date_input").inputmask('99.99.9999');
        $('#view_invoice').modal('hide');
        var button = $(event.relatedTarget);
        var id = button.data('invoice-id');
        window.livewire.emit('set:create_invoice_id', id)
        var type = button.data('type');
        var table_id = button.data('table_id');

        var actions = $('#create_invoice').data('action');
        console.log(actions);
        Object.assign(actions, {
            "update_table": {
                "table_id": table_id,
                "type": type,
                "object": "invoice",
                "id": id
            }
        });
        $('#create_invoice').data('action', actions);
    });

    /////черновик инвойса

    $('#create_draft_invoice_modal').on('show.bs.modal', function (event) {
        $(".date_input").inputmask('99.99.9999');
        $('#view_invoice').modal('hide');
        var button = $(event.relatedTarget);
        var id = button.data('invoice-id');

        window.livewire.emit('set:create_draft_invoice_id', id);

        var type = button.data('type');
        var table_id = button.data('table_id');

        var actions = $('#create_invoice_draft').data('action');
        console.log(actions);
        Object.assign(actions, {
            "update_table": {
                "table_id": table_id,
                "type": type,
                "object": "invoice",
                "id": id
            }
        });
        $('#create_invoice_draft').data('action', actions);

    });

    /////редактировать черновик инвойса

    $('#edit_draft_invoice_modal').on('show.bs.modal', function (event) {
        $(".date_input").inputmask('99.99.9999');
        $('#view_invoice').modal('hide');
        var button = $(event.relatedTarget);
        var id = button.data('invoice-id');
        window.livewire.emit('set:edit_draft_invoice_id', id)

        var type = button.data('type');
        var table_id = button.data('table_id');

        var actions = $('#edit_invoice_draft').data('action');
        console.log(actions);
        Object.assign(actions, {
            "update_table": {
                "table_id": table_id,
                "type": type,
                "object": "invoice",
                "id": id
            }
        });
        $('#edit_invoice_draft').data('action', actions);
    });

    ///// загрузка файлов в проекте
    $(document).ready(function(){

        if(document.querySelector('#upload_files_project')){
            $(function () {
                window.livewire.emit('set:project_id', $('#upload_files_project_id').val())
            });
        }

    });

    /////диапазон дат
    $(function() {
        $.datepicker.setDefaults($.datepicker.regional['ru']);
        moment().locale('ru');
        var start = moment().subtract(29, 'days');
        var end = moment();

        function cb(start, end) {
            $('#reportrange span').html(start.format('DD.MM.YYYY') + ' - ' + end.format('DD.MM.YYYY'));
            $('#datarange').val(start.format('DD.MM.YYYY') + ' - ' + end.format('DD.MM.YYYY'));
        }

        $('#reportrange').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
                '{{ __('general.today') }}': [moment(), moment()],
                '{{ __('general.yesterday') }}': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                '{{ __('general.7_days') }}': [moment().subtract(6, 'days'), moment().add(1, 'days')],
                '{{ __('general.30_days') }}': [moment().subtract(29, 'days'), moment().add(1, 'days')],
                '{{ __('general.this_month') }}': [moment().startOf('month'), moment().endOf('month')],
                '{{ __('general.last_month') }}': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            "locale": {
                "format": "DD.MM.YYYY",
                "separator": " - ",
                "applyLabel": "{{ __('general.apply') }}",
                "cancelLabel": "{{ __('general.cancel') }}",
                "fromLabel": "{{ __('general.fromLabel') }}",
                "toLabel": "{{ __('general.toLabel') }}",
                "customRangeLabel": "{{ __('general.choose_date_range') }}",
                "daysOfWeek": [
                    "{{ __('general.daysOfWeek_7') }}",
                    "{{ __('general.daysOfWeek_1') }}",
                    "{{ __('general.daysOfWeek_2') }}",
                    "{{ __('general.daysOfWeek_3') }}",
                    "{{ __('general.daysOfWeek_4') }}",
                    "{{ __('general.daysOfWeek_5') }}",
                    "{{ __('general.daysOfWeek_6') }}"
                ],
                "monthNames": [
                    "{{ __('general.month_1') }}",
                    "{{ __('general.month_2') }}",
                    "{{ __('general.month_3') }}",
                    "{{ __('general.month_4') }}",
                    "{{ __('general.month_5') }}",
                    "{{ __('general.month_6') }}",
                    "{{ __('general.month_7') }}",
                    "{{ __('general.month_8') }}",
                    "{{ __('general.month_9') }}",
                    "{{ __('general.month_10') }}",
                    "{{ __('general.month_11') }}",
                    "{{ __('general.month_12') }}"
                ],
                "firstDay": 1
            },
        }, cb);

        // cb(start, end);

        ///дедлайн для задач
        jQuery.datetimepicker.setLocale('{{ auth()->user()->language }}');
        jQuery('.task_deadline').datetimepicker({
            format:'d.m.Y H:i',
            lang:'{{ auth()->user()->language }}',
            minDate:0,
            scrollMonth : false,
            scrollInput : false
        });
        /////

    });


    $(document).on('click', '.dropdown-menu', function (e) {
        e.stopPropagation();
    });

    $(document).on('keyup keypress', 'form input[type="text"]', function(e) {
        if(e.keyCode == 13) {
            e.preventDefault();
            return false;
        }
    });


    /// продажа валюты

    $('#sell_currency').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        $('#sell_currency_invoice_id').val(button.data('invoice-id'));
        $('#sell_currency_amount').val(button.data('currency-amount'));
    });

    jQuery(document).on('keyup', '#sell_currency_rate_sale_date', function () {
        price = $('#sell_currency_amount').val();
        rate = $('#sell_currency_rate_sale_date').val();

        $('#sell_currency_amount_sale_date').val((price*rate).toFixed(2));

    });


    /// поиск
    jQuery(document).on('keyup', '#sidebar-search', function () {
        $('#sidebar-search-results').css('display', 'none');
        request = $('#sidebar-search').val();
        if(request.length > 1){
            $.ajax({
                type: "POST",
                url: APP_URL + "/search",
                data: {
                    search: request
                },
                success: function (response) {
                    $(`#sidebar-search-results`).html(response);
                    $('#sidebar-search-results').css('display', 'block');
                },
                error: function (XMLHttprequest, textStatus, errorThrown) {
                    console.log(textStatus);
                }
            });
        }
    });

    jQuery(document).on('blur', '#sidebar-search', function () {
        setTimeout(function(){
            $('#sidebar-search-results').css('display', 'none');
        }, 300);

    });

    function YandexOauth() {
        var popup = window.open('/yandex-oauth.php','','Toolbar=0,Location=0,Directories=0,Status=0,Menubar=0,Scrollbars=0,Resizable=0,Width=609,Height=831');

        var popupTick = setInterval(function() {
            if (popup.closed) {
                clearInterval(popupTick);
                location.reload();
            }
        }, 500);

    }

    function YandexExit() {
        var popup = window.open('/yandex-oauth.php','','Toolbar=0,Location=0,Directories=0,Status=0,Menubar=0,Scrollbars=0,Resizable=0,Width=609,Height=831');

        var popupTick = setInterval(function() {
            if (popup.closed) {
                clearInterval(popupTick);
                location.reload();
            }
        }, 500);

    }

    //выбор папки на яндекс диске при создании проекта

    function MakeFoldersList() {

        $('#folder_yandex_disk').empty().append('<option></option>');

        folders = [
            {
                folder: $('#logist_select option:selected').data('folder')
            },
            {
                folder: $('#manager_select option:selected').data('folder')
            }
        ];

        var arrayLength = folders.length;

        for (var i = 0; i < arrayLength; i++) {
            $('#folder_yandex_disk').append($('<option>', {
                value: folders[i].folder,
                text: folders[i].folder
            }));
        }

    }

    $(document).ready(function () {
        $("#create_project").submit(function () {
            $(".submit-button").attr("disabled", true);
            return true;
        });
    });

    ///СНП диапазон
    let snp_client_i = 1;
    jQuery('#snp_client_add').click(function(){
        jQuery('.snp-for-client-blocks').append(
            '<div class="col-md-4" id="snp_client_'+snp_client_i+'">'+
            '<div class="card">'+
            '<div class="card-header">'+
            '<h3 class="card-title">{{ __('container.snp_range_for_client') }}</h3>'+
            '<div class="card-tools">'+
            '<button type="button" class="btn btn-tool remove_expense_block" data-block-delete="snp_client_'+snp_client_i+'">'+
            '<i class="fas fa-trash"></i>'+
            '</button>'+
            '</div>'+
            '</div>'+
            '<div class="card-body">'+
            '<div class="form-group">'+
            '<label>{{ __('container.range_days') }}</label>'+
            '<input class="form-control" type="text" name="snp_client_array['+snp_client_i+'][range]" placeholder="{{ __('container.range_days') }}" value="1-5">'+
            '</div>'+
            '<div class="form-group">'+
            '<label>{{ __('container.rate') }}</label>'+
            '<input class="form-control digits_only" type="text" name="snp_client_array['+snp_client_i+'][price]" placeholder="{{ __('container.rate') }}">'+
            '</div>'+
            '</div>'+
            '</div>'+
            '</div>'
        );
        $(".digits_only").inputmask('9{1,}');
        snp_client_i++;
    });

    let snp_us_i = 1;
    jQuery('#snp_us_add').click(function(){
        jQuery('.snp-for-us-blocks').append(
            '<div class="col-md-4" id="snp_us_'+snp_us_i+'">'+
            '<div class="card">'+
            '<div class="card-header">'+
            '<h3 class="card-title">{{ __('container.snp_range_for_us') }}</h3>'+
            '<div class="card-tools">'+
            '<button type="button" class="btn btn-tool remove_expense_block" data-block-delete="snp_us_'+snp_us_i+'">'+
            '<i class="fas fa-trash"></i>'+
            '</button>'+
            '</div>'+
            '</div>'+
            '<div class="card-body">'+
            '<div class="form-group">'+
            '<label>{{ __('container.range_days') }}</label>'+
            '<input class="form-control" type="text" name="snp_us_array['+snp_us_i+'][range]" placeholder="{{ __('container.range_days') }}" value="1-5">'+
            '</div>'+
            '<div class="form-group">'+
            '<label>{{ __('container.rate') }}</label>'+
            '<input class="form-control digits_only" type="text" name="snp_us_array['+snp_us_i+'][price]" placeholder="{{ __('container.rate') }}">'+
            '</div>'+
            '</div>'+
            '</div>'+
            '</div>'
        );
        $(".digits_only").inputmask('9{1,}');
        snp_us_i++;
    });

    $('#snp_application_add').click(function(){
        let urlpath = location.pathname.split('/');
        if(urlpath[1] === 'application' && urlpath[3] === 'edit'){
            let last_element = $('.remove_expense_block').last().data('block-delete');
            var snp_application_i = 1;
            if (last_element !== undefined){
                snp_application_i = Number(last_element.replace("snp_application_", ""))+1;
            }
        }
        $('.snp-for-application-blocks').append(
            '<div class="col-md-4" id="snp_application_'+snp_application_i+'">'+
            '<div class="card">'+
            '<div class="card-header">'+
            '<h3 class="card-title">Диапазон СНП</h3>'+
            '<div class="card-tools">'+
            '<button type="button" class="btn btn-tool remove_expense_block" data-block-delete="snp_application_'+snp_application_i+'">'+
            '<i class="fas fa-trash"></i>'+
            '</button>'+
            '</div>'+
            '</div>'+
            '<div class="card-body">'+
            '<div class="form-group">'+
            '<label>{{ __('container.range_days') }}</label>'+
            '<input class="form-control snp_range" type="text" name="snp_application_array['+snp_application_i+'][range]" placeholder="{{ __('container.range_days') }}" value="1-5">'+
            '</div>'+
            '<div class="form-group">'+
            '<label>{{ __('container.rate') }}</label>'+
            '<input class="form-control digits_only" type="text" name="snp_application_array['+snp_application_i+'][price]" placeholder="{{ __('container.rate') }}">'+
            '</div>'+
            '</div>'+
            '</div>'+
            '</div>'
        );
        $(".digits_only").inputmask('9{1,}');
        $(".snp_range").inputmask({ regex: '^[0-9-]*$' });
        snp_application_i++;
    });

    jQuery(document).on('click', '.remove_snp_block', function () {
        block_remove = $(this).data('block-delete');
        $('#'+block_remove).remove();
    });

    function fillRateInRub(){
        let rate_in_usd = $('#rate_for_client_usd').val();
        let bank_rate = $('#rate_for_client_bank').val();

        $('#rate_for_client_rub').val((rate_in_usd*bank_rate).toFixed(2));
    }

    function fillSNPInRub(){
        let snp_amount_usd = $('#snp_amount_usd').val();
        let snp_bank = $('#snp_bank').val();

        $('#snp_rub').val((snp_amount_usd*snp_bank).toFixed(2));
    }

    function fillRepairInRub(){
        let repair_usd = $('#repair_usd').val();
        let repair_bank = $('#repair_bank').val();

        $('#repair_rub').val((repair_usd*repair_bank).toFixed(2));
    }

    function fillPaidInRub(){
        let paid_usd = $('#paid_usd').val();
        let paid_bank = $('#paid_bank').val();

        $('#paid_rub').val((paid_usd*paid_bank).toFixed(2));
    }

    $(document).ready(function() {
        if ($('#need_repair').val() == 'требуется') $('#repair_div').removeClass('d-none');
    });

    $(document).on('change', '#need_repair', function () {
        if ($(this).val() == 'требуется'){
            $('#repair_div').removeClass('d-none');
        }
        else $('#repair_div').addClass('d-none');

    });

    $(document).on('click', '.collapse-trigger', function () {
        $(this).closest('div').addClass('d-none');
        $('#'+$(this).data('div_id')).removeClass('d-none');
    });

    $(document).on('change', '#report_project_type', function () {
        if ($('#report_project_type option:selected').val() == 'date_range'){
            $('#report_project_date_range').removeClass('d-none');
        }
        else $('#report_project_date_range').addClass('d-none');

    });

    $(document).on('click', '.smooth-scroll', function (event) {
        event.preventDefault();

        $('html, body').animate({
            scrollTop: $($.attr(this, 'href')).offset().top
        }, 500);
    });

    $(function() {
        var slideToTop = $("<div />");
        slideToTop.html('<i class="fa fa-chevron-up"></i>');
        slideToTop.css({
            position: 'fixed',
            bottom: '40px',
            right: '0px',
            width: '40px',
            height: '40px',
            color: '#eee',
            'font-size': '',
            'line-height': '40px',
            'text-align': 'center',
            'background-color': '#222d32',
            cursor: 'pointer',
            'border-radius': '5px',
            'z-index': '99999',
            opacity: '.7',
            'display': 'none'
        });
        slideToTop.on('mouseenter', function () {
            $(this).css('opacity', '1');
        });
        slideToTop.on('mouseout', function () {
            $(this).css('opacity', '.7');
        });
        $('.wrapper').append(slideToTop);
        $(window).scroll(function () {
            if ($(window).scrollTop() >= 150) {
                if (!$(slideToTop).is(':visible')) {
                    $(slideToTop).fadeIn(500);
                }
            } else {
                $(slideToTop).fadeOut(500);
            }
        });
        $(slideToTop).click(function () {
            $('body,html').animate({
                scrollTop: 0
            }, 400);
            return false;
        });
        $(".sidebar-menu li:not(.treeview) a").click(function () {
            $('body,html').animate({
                scrollTop: 0
            }, 400);
            return false;
        });
    });

    $(document).on('click', '.copy-to-clipboard', function (event) {
        navigator.clipboard.writeText($(this).data('link'));
    });


    $('#application_direction').change(function () {
        if ($(this).val() === 'Клиент') {
            $('#client_group').removeClass('d-none');
            $('#client_select').prop('required', true);
            $('#supplier_group').addClass('d-none');
            $('#application_supplier_select').prop('required', false);
            // $('#application_supplier_select').val('');
            // $('#application_supplier_select').trigger('change');
            // $('#application_type').val('Клиент');
            // $('#application_type').trigger('change');
        }
        if ($(this).val() === 'Поставщик') {
            $('#supplier_group').removeClass('d-none');
            $('#supplier_select').prop('required', true);
            $('#client_group').addClass('d-none');
            $('#application_client_select').prop('required', false);
            // $('#application_client_select').val('');
            // $('#application_client_select').trigger('change');
            // $('#application_type').val('Поставщик');
            // $('#application_type').trigger('change');
        }
    });

    function application_load_contract(counterparty_id, counterparty_type){
        console.log('load');
        $.ajax({
            type: "GET",
            url: "{{ route('load_counterparty_contract') }}",
            data: {
                counterparty_id: counterparty_id,
                counterparty_type: counterparty_type
            },
            success: function (response) {
                $('#application_counterparty_contracts').html(response.view);
                $('.select2').select2({
                    "language": "ru",
                });
            },
            error: function (XMLHttprequest, textStatus, errorThrown) {
                console.log(textStatus);
            }
        });
    }

    $('.application_load_contract').change(function () {
        let counterparty_id = $(this).val();
        if(counterparty_id !== ''){
            let counterparty_type = $(this).data('counterparty_type');
            application_load_contract(counterparty_id, counterparty_type);
        }
    });

    $('#application_supplier_select').on('select2:select', function (e) {
        var data = e.params.data;
        console.log(data);
    });

    function process_filled_containers(){
        let containers_list = $('#application_containers').val();
        let containers = $('#containers_used').val();
        let application_type = $('#application_type option:selected').val();
        let application_id = urlpath[urlpath.length - 2];
        $.ajax({
            type: "GET",
            url: "{{ route('process_containers_list') }}",
            data: {
                containers_list: containers_list,
                containers: containers,
                application_type: application_type,
                application_id: application_id
            },
            success: function (response) {
                console.log(response);
                $('#dynamic_containers_div').html(response.view);
                $('.select2').select2({
                    "language": "ru",
                });
            },
            error: function (XMLHttprequest, textStatus, errorThrown) {
                console.log(textStatus);
            }
        });
    }

    $('#application_containers').change(function () {
        process_filled_containers();
    });

    $(document).on('click', '.reuse_container', function () {
        let container_id = $(this).data('container_id');
        let application_id = $(this).data('application_id');
        if (confirm("Вы уверены?")) {
            $.ajax({
                type: "POST",
                url: "{{ route('reuse_container_with_return_date') }}",
                data: {
                    container_id: container_id,
                    application_id: application_id
                },
                success: function (response) {
                    process_filled_containers();
                },
                error: function (XMLHttprequest, textStatus, errorThrown) {
                    console.log(textStatus);
                }
            });
        }
    });

    $('#confirm_containers_remove').click(function () {
        let application_id = $(this).data('application_id');
        let containers_removed = $('#containers_removed').val();
        let containers = $('#containers').val();
        containers.forEach((item) => {
            if(containers_removed.includes(item)){
                let index = containers_removed.indexOf(item);
                if (index !== -1) {
                    containers.splice(index, 1);
                    $("#containers option[value="+item+"]").remove();
                }
            }
        });
        console.log($("#containers").val());
        $('#containers_used').val(containers.join(', '));
        $.ajax({
            type: "POST",
            url: "{{ route('confirm_containers_remove') }}",
            data: {
                application_id: application_id,
                containers: containers
            },
            success: function (response) {
                $('#containers_removed').val([]);
                $(document).Toasts('create', {
                    autohide: true,
                    delay: 3000,
                    class: response['bg-class'],
                    title: 'Уведомление от ' + response['from'],
                    body: response['message']
                });
                $('#removed_containers_div').addClass('d-none');
            },
            error: function (XMLHttprequest, textStatus, errorThrown) {
                console.log(textStatus);
            }
        });
    });

    $('#send_from_country, #send_to_country, #place_of_delivery_country').change(function (event) {
        let target_div_id = event.target.id+'_div';
        console.log(target_div_id);
        let type = $(this).data('type');
        $.ajax({
            type: "GET",
            url: "{{ route('get_cities_list') }}",
            data: {
                country: $(this).val(),
                type: type
            },
            success: function (response) {
                console.log(target_div_id);
                $('#'+target_div_id).html(response.view);
                $('.select2').select2({
                    "language": "ru",
                });
                $('.to_uppercase').keyup(function(event) {
                    var textBox = event.target;
                    var start = textBox.selectionStart;
                    var end = textBox.selectionEnd;
                    textBox.value = textBox.value.charAt(0).toUpperCase() + textBox.value.slice(1);
                    textBox.setSelectionRange(start, end);
                });
            },
            error: function (XMLHttprequest, textStatus, errorThrown) {
                console.log(textStatus);
            }
        });
    });

    $(document).on('click', '.add_city_to_country', function () {
        let type = $(this).data('country_type');
        let city = $('#'+type+'_city_add_city').val();
        let country = $('#'+type+'_country').val();
        let target = '#'+type+'_city';
        $.ajax({
            type: "POST",
            url: "{{ route('add_city') }}",
            data: {
                country: country,
                city: city
            },
            success: function (response) {
                let data = {
                    id: city,
                    text: city
                };

                let newOption = new Option(data.text, data.id, true, true);
                $(target).append(newOption).trigger('change');

            },
            error: function (XMLHttprequest, textStatus, errorThrown) {
                console.log(textStatus);
            }
        });
    });

    $(document).on('click', '#cancel_containers_remove', function () {
        let application_id = $(this).data('application_id');

        $('#containers_removed').val('').change();
        $.ajax({
            type: "POST",
            url: "{{ route('cancel_containers_remove') }}",
            data: {
                application_id: application_id,
            },
            success: function (response) {
                $('#containers_removed').val('');
                $(document).Toasts('create', {
                    autohide: true,
                    delay: 3000,
                    class: response['bg-class'],
                    title: 'Уведомление от ' + response['from'],
                    body: response['message']
                });
                $('#removed_containers_div').addClass('d-none');
            },
            error: function (XMLHttprequest, textStatus, errorThrown) {
                console.log(textStatus);
            }
        });
    });

    let chosen_containers_for_edit = [];

    //редактировать список контейнеров
    $('#edit_containers_list').on('show.bs.modal', function () {
        $.ajax({
            type: "GET",
            url: "{{ route('check_containers_processing') }}",
            data: {
                chosen_containers_id: chosen_containers_id,
            },
            success: function (response) {
                console.log(response);
                chosen_containers_for_edit = response.chosen_containers_id;
                console.log(chosen_containers_for_edit);
                $('#containers_list').val(response.chosen_containers_list);
                $('#edit_containers_list_prefix').html(response.view);
                if(response.excluded_containers_exist){
                    $('#containers_list_collapse').collapse("show");
                }
                else if(response.chosen_containers_list === ''){
                    $('#containers_list_collapse').collapse("show");
                    $('#save_containers_list_edits').attr("disabled", "disabled");
                }
                else {
                    $('#containers_list_collapse').collapse("hide");
                    $('#save_containers_list_edits').removeAttr("disabled");
                }
                $('.select2-containers_list').select2({
                    "language": "ru",
                });
            },
            error: function (XMLHttprequest, textStatus, errorThrown) {
                console.log(textStatus);
            }
        });


        $('.date_input').datetimepicker({
            timepicker: false,
            format:'d.m.Y',
            lang: '{{ auth()->user()->language }}',
            scrollMonth : false,
            scrollInput : false
        });
        $(".rate_input").inputmask(
            "decimal", {
                radixPoint: '.',
                rightAlign: false
            }
        );
        $('.to_uppercase').keyup(function(event) {
            var textBox = event.target;
            var start = textBox.selectionStart;
            var end = textBox.selectionEnd;
            textBox.value = textBox.value.charAt(0).toUpperCase() + textBox.value.slice(1);
            textBox.setSelectionRange(start, end);
        });
        $(".digits_only").inputmask('9{1,}');
    });

    $('#edit_containers_list').on('hide.bs.modal', function () {
        if(chosen_containers_for_edit.length > 0){
            unmarkContainers();
        }
    });

    function unmarkContainers(){
        console.log('unmark starting');
        console.log(chosen_containers_for_edit);
        $.ajax({
            type: "POST",
            url: "{{ route('unmark_chosen_containers') }}",
            data: {
                list: chosen_containers_for_edit,
            },
            success: function (response){
                chosen_containers_for_edit = [];
                $('#containers_extended_ajax_table').DataTable().draw(false);
            },
            error: function (XMLHttprequest, textStatus, errorThrown) {
                console.log(textStatus);
            }
        });
    }

    window.addEventListener('beforeunload', function (e) {
        if(chosen_containers_for_edit.length > 0){
            e.preventDefault();
            e.returnValue = '';
            unmarkContainers();
        }
    });

    $(document).on('click', '#unmark_my_processing', function () {
        $.ajax({
            type: "POST",
            url: "{{ route('unblock_processing_by_me') }}",
            success: function () {
                $('#containers_extended_ajax_table').DataTable().draw(false);
                $('#unmark_my_processing').addClass('d-none');
            },
            error: function (XMLHttprequest, textStatus, errorThrown) {
                console.log(textStatus);
            }
        });
    });

    $(document).on('click', '#unmark_processing', function () {
        $.ajax({
            type: "POST",
            url: "{{ route('unblock_processing') }}",
            success: function () {
                $('#containers_extended_ajax_table').DataTable().draw(false);
                $('#unmark_processing').addClass('d-none');
            },
            error: function (XMLHttprequest, textStatus, errorThrown) {
                console.log(textStatus);
            }
        });
    });

    $(document).on('click', '#preview_invoices', function () {
        let application_id = $(this).data('application_id');
        console.log(application_id);
        $.ajax({
            type: "GET",
            url: "{{ route('get_invoices_preview_before_generate') }}",
            data: {
                application_id: application_id,
            },
            success: function (response) {
                $('#preview_application_invoices').modal('show');
                $('#invoices_for_application_div').html(response.view);
            },
            error: function (XMLHttprequest, textStatus, errorThrown) {
                console.log(textStatus);
            }
        });
    });

    $(document).on('click', '.download_application', function () {
        let application_id = $(this).data('application_id');
        let application_template = $(this).data('application_template');
        $.ajax({
            type: "GET",
            url: "{{ route('download_application_template') }}",
            data: {
                application_id: application_id,
                application_template: application_template
            },
            success: function (response) {
                $('body').append('<a style="display:none;" href="' + response + '" id="download" download></a>');
                $('#download')[0].click();
                $('#download').remove();
            },
            error: function (XMLHttprequest, textStatus, errorThrown) {
                console.log(textStatus);
            }
        });
    });

    function confirmSubmit(){
        let agree=confirm("Подтвердите перенос контейнеров в архив. Отменить данное действие будет невозможно!");
        if (agree)
            return true;
        else
            return false;
    }

    $(document).on('click', '.fixed_header_toggle', function () {
        if($(this).data('fixed_state') === 'unblocked'){
            $('#containers_extended_ajax_table').DataTable().fixedHeader.enable();
            $(this).data('fixed_state', 'blocked');
            $(this).html('<i class="fas fa-unlock"></i></button>');
        }
        else {
            $('#containers_extended_ajax_table').DataTable().fixedHeader.disable();
            $(this).data('fixed_state', 'unblocked');
            $(this).html('<i class="fas fa-lock"></i></button>');
        }
    });

    $(document).on('change', '#invoice_type', function () {
        if($(this).val() === 'Доход'){
            $('#expense_types_categories').addClass('d-none');
            $("#finance_direction").val("Клиенту").change();
        }

        else {
            $('#expense_types_categories').removeClass('d-none');
            $("#finance_direction").val("Поставщику").change();
        }
    });

    $(document).on('change', '#expense_category, #edit_expense_category', function () {
        $.ajax({
            type: "GET",
            url: "{{ route('load_expense_types_by_category') }}",
            data: {
                category: $(this).val(),
            },
            success: function (response) {
                $('.expense_type_div').html(response);
                $('.select2').select2({
                    "language": "ru",
                });
            },
            error: function (XMLHttprequest, textStatus, errorThrown) {
                console.log(textStatus);
            }
        });
    });

    $('#make_invoice').on('show.bs.modal', function (event) {
        $('.select2').select2({
            "language": "ru",
        });
    });

    $(document).on('change', '#application_type', function () {
        if($(this).val() === 'Подсыл'){
            $('#send_to_div').removeClass('d-none');
            $('#send_to_country').prop('required', true);
            $('#send_to_city').prop('required', true);
            $('#place_of_delivery_div').addClass('d-none');
            $('#place_of_delivery_country').prop('required', false);
            $('#place_of_delivery_city').prop('required', false);
        }
        if($(this).val() === 'Клиент'){
            $('#send_to_div').addClass('d-none');
            $('#send_to_country').prop('required', false);
            $('#send_to_city').prop('required', false);
            $('#place_of_delivery_div').removeClass('d-none');
            $('#place_of_delivery_country').prop('required', true);
            $('#place_of_delivery_city').prop('required', true);
            $('#application_direction').val('Клиент');
            $('#application_direction').trigger('change');
        }
        if($(this).val() === 'Поставщик'){
            $('#send_to_div').removeClass('d-none');
            $('#place_of_delivery_div').removeClass('d-none');
            $('#send_to_country').prop('required', true);
            $('#send_to_city').prop('required', true);
            $('#place_of_delivery_country').prop('required', true);
            $('#place_of_delivery_city').prop('required', true);
            $('#application_direction').val('Поставщик');
            $('#application_direction').trigger('change');
        }
        if($(this).val() === 'Покупка'){
            $('#application_direction').val('Поставщик');
            $('#application_direction').trigger('change');
        }
        if($(this).val() === 'Продажа'){
            $('#application_direction').val('Клиент');
            $('#application_direction').trigger('change');
        }
    });


    $(document).on('click', '#get_bank_account_balances', function () {
        $('#bank_account_balances').html(loading_spinner);
        $.ajax({
            type: "GET",
            url: "{{ route('get_bank_accounts_balance') }}",
            success: function (response) {
                $('#bank_account_balances').html(response);
            },
            error: function (XMLHttprequest, textStatus, errorThrown) {
                console.log(textStatus);
            }
        });
    });

    $('#preview_not_allowed_finish_reason').on('show.bs.modal', function (event) {
        $('#not_allowed_finish_reason_div').html(loading_spinner);
        $.ajax({
            type: "GET",
            url: "{{route('get_not_allowed_finish_reason')}}",
            data: {
                application_id: urlpath[2]
            },
            success: function (response) {
                $('#not_allowed_finish_reason_div').html(response);
            },
            error: function (XMLHttprequest, textStatus, errorThrown) {
                console.log(textStatus);
            }
        });
    });

</script>
