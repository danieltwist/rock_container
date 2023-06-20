$(document).ready(function () {
    $(".nav-treeview .nav-link, .nav-link").each(function () {
        var location2 = window.location.protocol + '//' + window.location.host + window.location.pathname + window.location.search;

        var link = this.href;

        if(link === location2){
            $(this).addClass('active');
            $(this).parent().parent().parent().addClass('menu-is-opening menu-open');

        }
    });

    jQuery(document).on('click', '.delete-btn', function () {
        var res = confirm('Подтвердите удаление. После этого восстановить данный элемент будет невозможно!');
        if(!res){
            return false;
        }
    });

    jQuery(document).on('click', '.invoice-confirm-btn', function () {
        var res = confirm('Проверьте правильность введенных данных и подтвердите.');
        if(!res){
            return false;
        }
    });

    jQuery(document).on('click', '.confirm-btn', function () {
        var res = confirm('Вы уверены?');
        if(!res){
            return false;
        }
    });

    jQuery(document).on('click', '.agree-btn', function () {
        var res = confirm('Подтвердить выполнение?');
        if(!res){
            return false;
        }
    });


    $(".alert").fadeTo(10000, 500).slideUp(500, function(){
        $(".alert").slideUp(500);
    });

});
