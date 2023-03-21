<script>
    ///////////Уведомления
    var pusher = new Pusher('8b6768a9846865623ba8', {
        cluster: 'eu'
    });

    channel = pusher.subscribe('notifications-channel');
    channel.bind('notify-user-' + user_id, function (data) {
        if(data['link'] !== ''){
            button_href = APP_URL + '/' + data['link'];
            notification_message = data['text'] +'<br><br><a href="'+ button_href +'" class="btn btn-sm btn-default">Перейти</a>';
        }
        else {
            notification_message = data['text'] +'<br><br><a href="'+ APP_URL + '/notification/" class="btn btn-sm btn-default">Перейти в уведомления</a>';
        }
        var count = $('.notifications_count').first().text();
        count = parseInt(count) + 1;
        $('.notifications_count').text(count);

        $.ajax({
            type: "GET",
            url: APP_URL + "/notification/get_notifications",
            success: function (data) {
                $('#user_notifications').html(data);
            },
            error: function (XMLHttprequest, textStatus, errorThrown) {
                console.log(textStatus);
            }
        });

        document.getElementById('notification_sound').play();
        $(document).Toasts('create', {
            class: data['class'],
            title: 'Уведомление от ' + data['from'],
            body: notification_message
        });
    });

    /// Уведомления задачи

    channel = pusher.subscribe('task-notifications-channel');
    channel.bind('notify-user-' + user_id, function (data) {
        document.getElementById('notification_sound').play();
        $(document).Toasts('create', {
            class: data['bg_class'],
            title: 'Уведомление от ' + data['from'],
            body: data['message'].substring(0,155)+'<br><br><a href="'+APP_URL + '/task/'+ data['object_id'] +'/" class="btn btn-sm btn-default">Перейти к задаче</a>'
        });
    });

</script>
