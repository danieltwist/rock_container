<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.ru_name') }} - @yield('title')</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/admin/plugins/fontawesome-free/css/all.min.css">
    <!-- Select 2 -->
    <link rel="stylesheet" href="/admin/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <!-- daterange picker -->
    <link rel="stylesheet" href="/admin/plugins/daterangepicker/daterangepicker.css">
    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="/admin/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="/admin/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="/admin/plugins/datatables-scroller/css/scroller.bootstrap4.min.css">
    <link rel="stylesheet" href="/admin/plugins/datatables-fixedheader/css/fixedHeader.bootstrap4.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="/admin/dist/css/adminlte.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="/admin/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="/admin/plugins/daterangepicker/daterangepicker.css">
    <!-- Toastr -->
    <link rel="stylesheet" href="/admin/plugins/toastr/toastr.min.css">
    <!-- X-Editable -->
    <link href="/admin/plugins/x-editable/css/X-editable-bs4-new.css" rel="stylesheet"/>
    <!-- datetime-picker -->
    <link rel="stylesheet" href="/admin/plugins/datetimepicker/jquery.datetimepicker.min.css">

    <style>
        /* Important part */
        .modal-dialog{
            overflow-y: initial !important
        }
        .modal-body-invoice{
            max-height: calc(100vh - 180px);
            overflow-y: auto;
        }
    </style>

    @livewireStyles
</head>
<body class="hold-transition sidebar-mini layout-fixed
@php
    if(isset($_COOKIE["navbar"])){
        if($_COOKIE["navbar"] == 'collapsed') echo 'sidebar-collapse';
    }
@endphp
    ">
<audio id="notification_sound" src="{{url('/notification_sound.mp3')}}" preload="auto"></audio>

<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" onclick="NavbarStyle();" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a class="nav-link cursor-pointer" data-toggle="modal" data-target="#create_task_modal">{{ __('general.task') }}</a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a class="nav-link" href="{{ route('application.create') }}">Заявка</a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a onclick="window.open('/filemanager','','Toolbar=0,Location=0,Directories=0,Status=0,Menubar=0,Scrollbars=0,Resizable=0,Width=1400,Height=740');"
                   class="nav-link cursor-pointer">{{ __('general.files') }}</a>
            </li>
            @if(!isset($_COOKIE["yaToken"]))
                <li class="nav-item d-none d-sm-inline-block">
                    <a onclick="YandexOauth();" class="nav-link cursor-pointer">{{ __('interface.sing_in_yandex') }}</a>
                </li>
            @else
                <li class="nav-item d-none d-sm-inline-block">
                    <a onclick="YandexExit();" class="nav-link cursor-pointer">{{ __('interface.sing_out_yandex') }}</a>
                </li>
            @endif
            <li class="nav-item d-none d-sm-inline-block">
                <a href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="nav-link">{{ __('interface.exit') }}</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <!-- Messages Dropdown Menu -->
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-bell">
                        <span class="badge badge-warning navbar-badge notifications_count" id="current_user_notifications_count">0</span>
                    </i>
                </a>
                <div class="dropdown-menu min-width-400 dropdown-menu-lg dropdown-menu-right" id="user_notifications"></div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="fas fa-globe"></i>
                </a>
                <div class="dropdown-menu min-width-200 dropdown-menu-right">
                    <a href="#" class="dropdown-item change_language" data-language="ru">
                        @if(auth()->user()->language == 'ru')
                            <i class="far fa-check-circle"></i>
                        @else
                            <i class="far fa-circle"></i>
                        @endif
                            {{ __('user.language_ru') }}
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item change_language" data-language="cn">
                        @if(auth()->user()->language == 'cn')
                            <i class="far fa-check-circle"></i>
                        @else
                            <i class="far fa-circle"></i>
                        @endif
                            {{ __('user.language_cn') }}
                    </a>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                    <i class="fas fa-expand-arrows-alt"></i>
                </a>
            </li>
        </ul>
    </nav>
    <!-- /.navbar -->
    @if (in_array($role,['super-admin','director']))
        @include('layouts.navbar.director')
    @elseif($role == 'manager')
        @include('layouts.navbar.manager')
    @elseif($role == 'accountant')
        @include('layouts.navbar.accountant')
    @elseif($role == 'logist')
        @include('layouts.navbar.logist')
    @else
        @include('layouts.navbar.special')
    @endif
<!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        @yield('content')
    </div>
    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
    @include('task.task__modal')
    @include('work_request.work_request__modal')
    @include('invoice.modal.make_invoice')
    @include('invoice.modal.make_invoice_draft')
    @include('invoice.modal.edit_invoice_draft')
    @include('invoice.modal.sell_currency')
    @include('invoice.modal.view_invoice_changes')
    @include('notification.modal.make_notification_modal')
    @include('invoice.modal.edit_invoice')
    @include('container.modal.edit_containers')
</div>
<!-- ./wrapper -->
@livewireScripts
<!-- jQuery -->
<script src="/admin/plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="/admin/plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
    $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="/admin/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- Select 2 -->
<script src="/admin/plugins/select2/js/select2.full.min.js"></script>
<script src="/admin/plugins/select2/js/i18n/ru.js" type="text/javascript"></script>
<!-- DataTables  & Plugins -->
<script src="/admin/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="/admin/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="/admin/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="/admin/plugins/jszip/jszip.min.js"></script>
<script src="/admin/plugins/pdfmake/pdfmake.min.js"></script>
<script src="/admin/plugins/pdfmake/vfs_fonts.js"></script>
<script src="/admin/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="/admin/plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="/admin/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<script src="/admin/plugins/datatables-scroller/js/scroller.bootstrap4.min.js"></script>
<script src="/admin/plugins/datatables-fixedheader/js/dataTables.fixedHeader.js"></script>
<script src="/admin/plugins/datatables-fixedheader/js/fixedHeader.bootstrap4.js"></script>

<!-- InputMask -->
<script src="/admin/plugins/moment/moment.min.js"></script>
<script src="/admin/plugins/inputmask/jquery.inputmask.min.js"></script>

<!-- date-range-picker -->
<script src="/admin/plugins/daterangepicker/daterangepicker.js"></script>
<script src="/admin/plugins/daterangepicker/datepicker-ru.js"></script>

<!-- datetime-picker -->
<script src="/admin/plugins/datetimepicker/jquery.datetimepicker.full.min.js"></script>

<!-- overlayScrollbars -->
<script src="/admin/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="/admin/dist/js/adminlte.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="/admin/dist/js/demo.js"></script>
<!-- Toastr -->
<script src="/admin/plugins/toastr/toastr.min.js"></script>
<!-- Sortable js -->
<script type="text/javascript" src="/packages/sortablejs/Sortable.js"></script>
<!-- X-Editable -->
<script src="/admin/plugins/x-editable/js/X-editable-bs4-new.js"></script>

<!-- Rocklogistic js -->
<script src="/admin/rock.js"></script>
<script src="/admin/js-cookie.js"></script>
<!-- laravel js -->
<script src="/js/app.js"></script>
<!-- typeahead -->
<script src="/admin/plugins/twitter-typeahead/typeahead.bundle.js"></script>

<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.13.1/jquery.validate.js"></script>


@include('layouts.js')
@include('layouts.ajax_js')
@include('layouts.datatables')
@include('layouts.'.config('app.prefix_view').'pusher')

</body>
</html>
