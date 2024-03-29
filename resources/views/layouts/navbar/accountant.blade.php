<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">

    @include('layouts.navbar.'.config('app.prefix_view').'logo')

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ $current_user_avatar }}" class="avatar elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="{{ route('my_profile') }}" class="d-block">{{ $current_user_name }}<br><small>{{ $current_user_position }}</small></a></a>
            </div>
        </div>

        <!-- SidebarSearch Form -->
        <div class="form-inline">
            <div class="input-group">
                <input class="form-control form-control-sidebar" type="search" id="sidebar-search" placeholder="Поиск" aria-label="Search">
            </div>
        </div>
        <div class="sidebar-search-results" id="sidebar-search-results"></div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
                     with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a href="{{ route('homeAdmin') }}" class="nav-link">
                        <i class="nav-icon fas fa-network-wired"></i>
                        <p>
                            {{ __('interface.home') }}
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-briefcase"></i>
                        <p>
                            {{ __('interface.tasks') }}
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a class="nav-link cursor-pointer" data-toggle="modal" data-target="#create_task_modal">
                                <p>{{ __('interface.create_task') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('income_tasks') }}" class="nav-link">
                                <p>{{ __('interface.all_tasks') }} <span class="badge badge-info right" id="current_user_income_tasks_count">0</span></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('outcome_tasks') }}" class="nav-link">
                                <p>{{ __('interface.outcome_tasks') }} <span class="badge badge-danger right" id="current_user_outcome_overdue_tasks_count">0</span></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('done_tasks') }}" class="nav-link">
                                <p>{{ __('interface.done_tasks') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('all_income_tasks') }}" class="nav-link">
                                <p>{{ __('interface.all_income_tasks') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('important_tasks') }}" class="nav-link">
                                <p>{{ __('interface.important_tasks') }}</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-exclamation-circle"></i>
                        <p>
                            {{ __('interface.work_requests') }}
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a class="nav-link cursor-pointer" data-toggle="modal" data-target="#create_work_request_modal">
                                <p>{{ __('interface.create_work_request') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('income_work_requests') }}" class="nav-link">
                                <p>{{ __('interface.income_work_requests') }} <span class="badge badge-info right" id="current_user_income_work_requests_count">0</span></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('outcome_work_requests') }}" class="nav-link">
                                <p>{{ __('interface.outcome_work_requests') }} <span class="badge badge-danger right" id="current_user_outcome_overdue_work_requests_count">0</span></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('done_work_requests') }}" class="nav-link">
                                <p>{{ __('interface.done_work_requests') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('all_income_work_requests') }}" class="nav-link">
                                <p>{{ __('interface.all_income_work_requests') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('important_work_requests') }}" class="nav-link">
                                <p>{{ __('interface.important_work_requests') }}</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-coins"></i>
                        <p>
                            {{ __('interface.projects') }}
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('project.index') }}" class="nav-link">
                                <p>{{ __('interface.all_projects') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('project.index').'?active' }}" class="nav-link">
                                <p>{{ __('interface.active_projects') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('project.index').'?done_unpaid' }}" class="nav-link">
                                <p>{{ __('interface.done_unpaid_projects') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('project.index').'?finished' }}" class="nav-link">
                                <p>{{ __('interface.finished_projects') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('project.index').'?archive' }}" class="nav-link">
                                <p>{{ __('interface.containers_archive') }}</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-address-card"></i>
                        <p>
                            {{ __('interface.clients') }}
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('client.create') }}" class="nav-link">
                                <p>{{ __('interface.create_client') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('client.index') }}" class="nav-link">
                                <p>{{ __('interface.all_clients') }}</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-train"></i>
                        <p>
                            {{ __('interface.suppliers') }}
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('supplier.create') }}" class="nav-link">
                                <p>{{ __('interface.create_supplier') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('supplier.index') }}" class="nav-link">
                                <p>{{ __('interface.all_suppliers') }}</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-file-invoice-dollar"></i>
                        <p>
                            {{ __('interface.invoices') }}
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('invoice.index') }}" class="nav-link">
                                <p>{{ __('interface.all_invoices') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('invoice.index').'?my' }}" class="nav-link">
                                <p>{{ __('interface.my_invoices') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('invoice.index').'?in' }}" class="nav-link">
                                <p>{{ __('interface.income_invoices') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('invoice.index').'?out' }}" class="nav-link">
                                <p>{{ __('interface.outcome_invoices') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('invoice.index').'?for_approval' }}" class="nav-link">
                                <p>{{ __('interface.invoices_for_approval') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('invoice.index').'?on_approval' }}" class="nav-link">
                                <p>{{ __('interface.invoices_on_approval') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('invoice.index').'?agreed' }}" class="nav-link">
                                <p>{{ __('interface.agreed_invoices') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('invoice.index').'?partially_paid' }}" class="nav-link">
                                <p>{{ __('interface.partially_paid_invoices') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('invoice.index').'?paid' }}" class="nav-link">
                                <p>{{ __('interface.paid_invoices') }}</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-copy"></i>
                        <p>
                            {{ __('interface.contracts') }}
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('client_contracts') }}" class="nav-link">
                                <p>{{ __('interface.contracts_with_client') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('supplier_contracts') }}" class="nav-link">
                                <p>{{ __('interface.contracts_with_supplier') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('contract.index') }}" class="nav-link">
                                <p>{{ __('interface.all_contracts') }}</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fa fa-credit-card"></i>
                        <p>
                            Данные из 1С
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('bank_accounts_balance') }}" class="nav-link">
                                <p>Баланс счетов</p>
                            </a>
                            <a href="{{ route('bank_accounts_payments') }}" class="nav-link">
                                <p>История платежей</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>
                            {{ __('interface.reports') }}
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('report/client-supplier_summary') }}" class="nav-link">
                                <p>{{ __('interface.client-supplier_summary') }}</p>
                            </a>
                        </li>
                    </ul>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('report_get_credit') }}" class="nav-link">
                                <p>{{ __('interface.report_get_credit') }}</p>
                            </a>
                        </li>
                    </ul>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('report_get_debit') }}" class="nav-link">
                                <p>{{ __('interface.report_get_debit') }}</p>
                            </a>
                        </li>
                    </ul>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('report_get_potential_losses') }}" class="nav-link">
                                <p>{{ __('interface.report_get_potential_losses') }}</p>
                            </a>
                        </li>
                    </ul>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('report_get_losses') }}" class="nav-link">
                                <p>{{ __('interface.report_get_losses') }}</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-bell"></i>
                        <p>
                            {{ __('interface.notifications') }}
                            <i class="right fas fa-angle-left"></i>
                            <span class="badge badge-info right notifications_count" id="current_user_notifications_count_menu">0</span>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a class="nav-link cursor-pointer" data-toggle="modal" data-target="#make_notification_modal">
                                <p>{{ __('interface.send_notification') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('notification.index') }}" class="nav-link">
                                <p>{{ __('interface.all_notifications') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('show_notifications_archive') }}" class="nav-link">
                                <p>{{ __('interface.archive') }}</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>
                            Настройки
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('my_profile') }}" class="nav-link">
                                <p>Мой профиль</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a onclick="window.open('/filemanager','','Toolbar=0,Location=0,Directories=0,Status=0,Menubar=0,Scrollbars=0,Resizable=0,Width=1400,Height=740');"
                       class="nav-link cursor-pointer">
                        <i class="nav-icon fas fa-folder-open"></i>
                        <p>
                            {{ __('interface.file_manager') }}
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="nav-link">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>
                            {{ __('interface.logout') }}
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
<?php
