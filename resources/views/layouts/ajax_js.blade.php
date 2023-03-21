<script>

    function reloadRow(table_id, object, id){
        console.log(table_id, object, id);
        let table = $('#'+table_id).DataTable();
        let filteredData = table.rows().indexes().filter(function(value, index) {
                console.log(table.row(value).data()[0]);
                return table.row(value).data()[0] == id;
            });
        $.ajax({
            type: "GET",
            url: APP_URL + "/" + object + "/load_table_row/" + id,
            success: function (data) {
                if(filteredData[0]){
                    console.log('filteredData');
                    table.row(filteredData).data(data);
                }
                else {
                    console.log('byID');
                    table.row({0:id}).data(data);
                }
            },
            error: function (XMLHttprequest, textStatus, errorThrown) {
                console.log(textStatus);
            }
        });
    }

    function reloadModalTable(id) {
        $.ajax({
            type: "GET",
            url: APP_URL + "/invoice/load_modal_table_row/" + id,
            success: function (data) {
                $('#invoice_table_modal_ajax').html(data.modal_table);
            },
            error: function (XMLHttprequest, textStatus, errorThrown) {
                console.log(textStatus);
            }
        });
    }

    function removeRow(table_id, object, id){
        let table = $('#'+table_id).DataTable();
        let filteredData = table.rows().indexes().filter(function(value, index) {
                return table.row(value).data()[0] === id;
            });

        table.rows(filteredData).remove().draw(false);
    }

    ////ajax таблицы удалить строку
    $(document).on("click", ".ajax-delete-row", function() {
        let action = $(this).data('action');
        let type = $(this).data('type');
        let object = $(this).data('object');
        let object_id = $(this).data('object-id');
        let table_id = $(this).closest('table').attr('id');
        console.log(table_id);
        $.ajax({
            type: "POST",
            url: APP_URL + "/" + object + "/" + action + "/" + object_id,
            beforeSend:function(){
                return confirm("{{ __('general.are_you_sure') }}");
            },
            success: function (data) {
                $(document).Toasts('create', {
                    autohide: true,
                    delay: 3000,
                    class: data['bg-class'],
                    title: 'Уведомление от ' + data['from'],
                    body: data['message']
                });
                if(type === 'ajax'){
                    $('#'+table_id).DataTable().draw(false);
                }
                else {
                    removeRow(table_id, object ,object_id)
                }

            },
            error: function (XMLHttprequest, textStatus, errorThrown) {
                console.log(textStatus);
            }
        });
    });

    ///// удалить элемент из счета
    $(document).on("click", ".ajax_remove", function() {
        let action = $(this).data('action');
        let div = $(this).closest('div[id]').attr('id');
        let invoice_id = $(this).data('invoice-id');
        let key_id = $(this).data('array_key');

        let send_url = APP_URL + "/invoice/" + action + "/" + invoice_id + "/" + key_id

        $.ajax({
            type: "GET",
            url: send_url,
            beforeSend:function(){
                if(confirm("{{ __('general.are_you_sure') }}")){
                    $('#'+div).html(loading_spinner);
                }
                else return false;
            },
            success: function (data) {
                setTimeout(function(){
                    $('#'+div).html(data.view);
                    $(document).Toasts('create', {
                        autohide: true,
                        delay: 3000,
                        class: data['bg-class'],
                        title: 'Уведомление от ' + data['from'],
                        body: data['message']
                    });
                }, 300);

            },
            error: function (XMLHttprequest, textStatus, errorThrown) {
                console.log(textStatus);
            }
        });
    });

    ///загрузить файл счета
    $(document).on("change", ".ajax_upload_invoice_file", function() {
        let invoice_id = $(this).data('invoice-id');
        let table_id = $(this).closest('table').attr('id');
        let file_data = $(this).prop("files")[0];
        let form_data = new FormData();
        form_data.append("file", file_data);
        form_data.append("id", invoice_id);

        $.ajax({
            url: APP_URL + "/invoice/upload_invoice_file/" + invoice_id,
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            dataType:'json',
            type: 'post',
            success: function(data) {
                $('#'+table_id).DataTable().draw(false);
                $(document).Toasts('create', {
                    autohide: true,
                    delay: 3000,
                    class: data['bg-class'],
                    title: '{{ __('general.notification_from') }} ' + data['from'],
                    body: data['message']
                });
            }
        });
    });

    ///ajax действия
    $(document).on('click', 'form button[type="submit"]', function(e) {
        console.log('form send button click');
        let form = $(this).parents('form');
        let button = $(this);
        let button_html = button.html();
        if (!button.hasClass('download_file_directly')){
            let form_id = 'send_this_form_now';
            form.attr('id', form_id);
            let url = form.attr('action');
            let actions = $(this).data('action');
            let send_method = $(this).data('send_method');

            if(send_method === undefined){
                send_method = 'POST';
            }

            let form_data = new FormData(form[0]);
            if(actions !== undefined){
                e.preventDefault();
                if(form.valid()) {
                    button.html(
                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Загрузка...'
                    );
                    button.attr('disabled', true);
                    $.ajax({
                        type: send_method,
                        url: url,
                        processData: false,
                        contentType: false,
                        data: form_data,
                        success: function (data) {
                            button.find('span').remove();
                            button.html('{{ __('general.successfully_done') }}');
                            setTimeout(function() {
                                button.html(button_html);
                                button.removeAttr('disabled');
                            }, 300);
                            for (const [key, value] of Object.entries(actions)) {
                                setTimeout(function() {
                                    if (key === 'hide_modal') {
                                        if(value['id'] === 'view_invoice'){
                                            if(($('#view_invoice').data('bs.modal') || {})._isShown) reloadModalTable(url.replace(/[^0-9]/g,""))
                                        }
                                        $('#'+value['id']).modal('hide');
                                    }
                                }, 500);
                                if(key === 'update_table'){
                                    if(value['type'] === 'static'){
                                        reloadRow(value['table_id'], value['object'], value['id']);
                                    }
                                    else $('#'+value['table_id']).DataTable().draw(false);
                                }
                                if(key === 'update_second_table'){
                                    $('#'+value['table_id']).DataTable().draw(false);
                                }
                                if(key === 'update_div'){
                                    $('#'+value['div_id']).html(data.ajax);
                                    if(value['div_id'] === 'project_additional_info'){
                                        $('#project_comment_button').html(data.button);
                                        setTimeout(function(){
                                            init_comments_editable();
                                        }, 1000);
                                    }
                                }
                                if(key === 'select2_init'){
                                    $('.select2').select2({
                                        "language": "ru",
                                    });
                                }
                                if(key === 'datetimepicker_init'){
                                    $('.invoice_deadline').datetimepicker({
                                        timepicker: false,
                                        format:'Y-m-d',
                                        lang:'{{ auth()->user()->language }}',
                                        minDate:0
                                    });
                                }
                                if(key === 'update_container_project_top_panel'){
                                    $('#top_panel').html(data.top_panel);
                                }
                                if(key === 'update_div_chat'){
                                    $('#'+value['div_id']).html(data.chat);
                                    $('#custom-tabs-three-home-tab').trigger('click');
                                }
                                if(key === 'add_table_row'){
                                    $('#'+value['table_id']).DataTable().row.add(data.table_row).draw(false);
                                }
                                if(key === 'reset_form'){
                                    form.trigger("reset");
                                }
                                if(key === 'download_file'){
                                    let anchor = document.createElement('a');
                                    anchor.setAttribute("href", data.url);
                                    anchor.click();
                                }
                                if(key === 'update_div_container_group_table'){
                                    setTimeout(function() {
                                        $('#project_containers_group_table_'+data.group_id).html(data.table);
                                        $('#containers_group_'+data.group_id).DataTable({
                                            "paging": true,
                                            "lengthChange": true,
                                            "searching": true,
                                            "ordering": true,
                                            "info": true,
                                            "autoWidth": false,
                                            "responsive": true,
                                            "language": {
                                                "url": "/admin/plugins/datatables-ru-lang/russian.json"
                                            }
                                        });
                                    }, 1000);
                                }
                                if(key === 'update_div_container_group_locations'){
                                    $('#project_group_locations_'+data.group_id).html(data.locations);
                                }
                            }
                            setTimeout(function() {
                                $(document).Toasts('create', {
                                    autohide: true,
                                    delay: 3000,
                                    class: data['bg-class'],
                                    title: 'Уведомление от ' + data['from'],
                                    body: data['message']
                                });
                            }, 600);

                        },
                        error: function (XMLHttprequest, textStatus, errorThrown) {
                            alert('{{ __('general.error_update_page') }}')
                            console.log(errorThrown);
                        }
                    });
                }

            }
            else {
                console.log('static_form_send');
                form.submit(function () {
                    if(form.valid()) {
                        button.html(
                            `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> {{ __('general.loading...') }}`
                        );
                        button.attr('disabled', true);

                        setTimeout(function() {
                            button.find("span").remove();
                            button.html(button_html);
                            button.removeAttr('disabled');
                        }, 25000);


                    }
                });
            }
        }
        else {
            console.log('download excel file');
            form.submit(function () {
                if(form.valid()) {
                    button.html(
                        `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> {{ __('general.request_send_wait') }}`
                    );
                    button.attr('disabled', true);

                    setTimeout(function() {
                        button.find("span").remove();
                        button.html(button_html);
                        button.removeAttr('disabled');
                    }, 1000);


                }
            });
        }
    });

    /// Проверка проекта на дубликат
    $(document).on('change', '#project_name', function(e){
        $.ajax({
            type: "GET",
            url: APP_URL + "/project/check_name_free",
            data: {"name": $(this).val()},
            success: function (data) {
                if(data !== '1') alert('{{ __('general.project_with_such_name_already_exist') }}');
            },
            error: function (XMLHttprequest, textStatus, errorThrown) {
                console.log(textStatus);
            }
        });
    });

    ///Перейти и отметить уведомление прочитанным
    $(document).on("click", ".notification-go-and-make-read", function() {
        let notification_id = $(this).data('notification_id');
        let div = $(this).closest('div.dropdown-item');
        let $notifications_count = $('.notifications_count');
        let count = $notifications_count.first().text();
        count = parseInt(count) - 1;
        $notifications_count.text(count);
        div.hide('slow');
        $.ajax({
            type: "POST",
            url: APP_URL + "/notification/make_read",
            data: {"id": notification_id},
            error: function (XMLHttprequest, textStatus, errorThrown) {
                console.log(textStatus);
            }
        });
    });

    ///Отметить уведомление прочитанным
    $(document).on("click", ".notification-make-read", function() {
        let notification_id = $(this).data('notification_id');
        let div = $(this).closest('div.dropdown-item');
        let $notifications_count = $('.notifications_count');
        let count = $notifications_count.first().text();
        count = parseInt(count) - 1;
        $notifications_count.text(count);
        div.hide('slow');
        $(this).remove();
        $.ajax({
            type: "POST",
            url: APP_URL + "/notification/make_read",
            data: {"id": notification_id},
            success: function (data) {
                console.log('success');
            },
            error: function (XMLHttprequest, textStatus, errorThrown) {
                console.log(textStatus);
            }
        });
    });

    ///Отметить все уведомление прочитанным
    $(document).on("click", ".notification-make-all-read", function() {
        $('#user_notifications').html('<div class="dropdown-item text-sm-center">У вас нет непрочитанных уведомлений</div>');
        $('.notifications_count').text(0);
        $.ajax({
            type: "POST",
            url: APP_URL + "/notification/make_all_read",
            success: function () {
                console.log('all_read');
            },
            error: function (XMLHttprequest, textStatus, errorThrown) {
                console.log(textStatus);
            }
        });
    });

    /// Сообщение пользователю в чате
    $(document).on('keypress', '#comment', function(e) {
        // if($('#comment').val() === ''){
            $('#task_handler_submit').removeAttr('data-nofity_user');
            if(e.key === '@') {
                $('#users_send_to').addClass('show');
            }
            else {
                $('#users_send_to').removeClass('show');
            }
        // }
    });

    $(document).click(function(e) {
        let clicked = $(e.currentTarget);
        if (clicked.closest('.dropdown').length === 0) {
            $('#users_send_to').removeClass('show');
            $('#users_add_to_task').removeClass('show');
        }
    });

    $(document).on("click", ".chat-notify-user", function() {
        $('#users_send_to').removeClass('show');
        let $comment = $('#comment');
        let current_text = $comment.val();
        console.log(current_text.toString().slice(-3));
        if(current_text === '@'){
            $comment.val($(this).text()+', ');
        }
        else if(current_text.toString().slice(-3) === ', @'){
            $comment.val(current_text.substring(0, current_text.length - 1) + $(this).text()+ ', ');
        }
        else $comment.val(current_text.substring(0, current_text.length - 1) + ', ' +$(this).text());

        $comment.focus();

        let selected_users = $("#comment_notify_users").val();
        selected_users.push($(this).data('notify_user'));

        $("#comment_notify_users").val(selected_users).trigger("change");

    });

    $(document).on("click", ".change_language", function() {
        let language = $(this).data('language');
        console.log(language);
        $.ajax({
            type: "POST",
            url: "{{ route('change_language') }}",
            data: {"language": language},
            success: function () {
                location.reload();
            },
            error: function (XMLHttprequest, textStatus, errorThrown) {
                console.log(textStatus);
            }
        });

    });

    $(function () {
        $.ajax({
            type: "GET",
            url: APP_URL + "/get_user_counts",
            dataType: "json",
            success: function (data) {
                Object.keys(data).forEach(function(key) {
                    $('#'+key).html(data[key]);
                })
            },
            error: function (XMLHttprequest, textStatus, errorThrown) {
                console.log(textStatus);
            }
        });
    });


</script>
