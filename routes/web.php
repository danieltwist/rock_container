<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Auth::routes(['register' => false]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['role:manager|accountant|director|super-admin|special|logist|supply|equipment'])->prefix('/')->group(function (){

    Route::prefix('application')->middleware(['auth'])->group(function () {

        Route::get('/buy_sell/create', 'App\Http\Controllers\Application\ApplicationController@buySellCreate')
            ->name('buy_sell_create');
        Route::get('/buy_sell/{id}/edit', 'App\Http\Controllers\Application\ApplicationController@buySellEdit')
            ->name('buy_sell_edit');

        Route::get('/load_counterparty_contract', 'App\Http\Controllers\Application\ApplicationController@loadCounterpartyContract')
            ->name('load_counterparty_contract');

        Route::get('/get_cities_list', 'App\Http\Controllers\Application\ApplicationController@loadCities')
            ->name('get_cities_list');

        Route::post('/add_city', 'App\Http\Controllers\Application\ApplicationController@addCity')
            ->name('add_city');

        Route::get('/process_containers_list', 'App\Http\Controllers\Application\ApplicationController@processContainersList')
            ->name('process_containers_list');

        Route::get('/check_containers_processing', 'App\Http\Controllers\Container\ContainerController@checkContainersProcessing')
            ->name('check_containers_processing');

        Route::post('/unmark_chosen_containers', 'App\Http\Controllers\Container\ContainerController@unmarkContainersProcessing')
            ->name('unmark_chosen_containers');

        Route::post('/check_processing', 'App\Http\Controllers\Container\ContainerController@checkProcessing')
            ->name('check_processing');

        Route::post('/unblock_processing_by_me', 'App\Http\Controllers\Container\ContainerController@unblockProcessingByMe')
            ->name('unblock_processing_by_me');

        Route::get('/get_invoices_preview_before_generate', 'App\Http\Controllers\Application\ApplicationController@getInvoicesPreviewBeforeGenerate')
            ->name('get_invoices_preview_before_generate');

        Route::post('/add_invoices', 'App\Http\Controllers\Application\ApplicationController@addChosenInvoices')
            ->name('application_add_invoices');

        Route::post('/archive_containers_usage_info', 'App\Http\Controllers\Application\ApplicationController@archiveContainersUsageInfo')
            ->name('archive_containers_usage_info');

        Route::post('/get_archive_containers_table', 'App\Http\Controllers\Datatables\ContainerTablesController@applicationArchiveTableGet')
            ->name('get_archive_containers_table');

        Route::get('/get_applications_table', 'App\Http\Controllers\Datatables\ApplicationTablesController@getApplicationTable')
            ->name('get_applications_table');

        Route::post('application/cancel_containers_remove', 'App\Http\Controllers\Application\ApplicationController@cancelContainersRemove')
            ->name('cancel_containers_remove')
            ->middleware(['auth']);

        Route::get('/download_application_template', 'App\Http\Controllers\Application\ApplicationController@downloadApplicationTemplate')
            ->name('download_application_template');

    });


    Route::prefix('telegram')->middleware(['auth'])->group(function () {
        Route::post('/link_account', 'App\Http\Controllers\TelegramController@linkAccount')->name('link_telegram_account');
        Route::post('/unlink_account', 'App\Http\Controllers\TelegramController@unlinkAccount')->name('unlink_telegram_account');
        Route::get('/show_updates', 'App\Http\Controllers\TelegramController@showUpdates')->name('show_updates');
    });

    Route::get('/test_function', 'App\Http\Controllers\Adminpage\HomeController@test_function')->middleware(['auth']);
    Route::get('/get_user_counts', 'App\Http\Controllers\Adminpage\HomeController@getUserCounts')->middleware(['auth']);

    Route::get('/', [App\Http\Controllers\Adminpage\HomeController::class, 'index'])->name('homeAdmin')->middleware(['auth']);

    Route::get('project/remove_from_stat', 'App\Http\Controllers\Project\ProjectController@removeFromStatView')->name('remove_from_stat_view')->middleware(['auth']);
    Route::any('project/remove_from_stat/process', 'App\Http\Controllers\Project\ProjectController@removeFromStat')->name('remove_from_stat')->middleware(['auth']);

    Route::get('project/{id}/re-calculate', 'App\Http\Controllers\Project\ProjectController@update_project_finance')->name('recalculate_project_finance')->middleware(['auth']);

    Route::any('projects/export_with_filter_to_excel', '\App\Http\Controllers\ExportToExcel\ProjectExportController@exportProjectsListWithFilter')
        ->name('projects_export_with_filter_to_excel')->middleware(['auth']);

    Route::get('project/{id}/export', '\App\Http\Controllers\ExportToExcel\ProjectExportController@exportProject')->name('export_project')->middleware(['auth']);
    Route::any('projects_list_export', '\App\Http\Controllers\ExportToExcel\ProjectExportController@exportProjectsList')->name('export_projects_list')->middleware(['auth']);
    Route::any('projects_counterparty_export', '\App\Http\Controllers\ExportToExcel\ProjectExportController@exportCounterpartyProjectsList')
        ->name('projects_counterparty_export')
        ->middleware(['auth']);
    Route::post('project_comment/add', '\App\Http\Controllers\Project\ProjectCommentController@addComment')->name('project_add_comment')->middleware(['auth']);
    Route::post('project_comment/remove', '\App\Http\Controllers\Project\ProjectCommentController@removeComment')->name('project_comment_remove')->middleware(['auth']);
    Route::any('project/finish_project', 'App\Http\Controllers\Project\ProjectController@finish_project')->name('finish_project')->middleware(['auth']);
    Route::get('supplier/projects/{id}', 'App\Http\Controllers\Project\ProjectController@getProjectBySupplier')->name('supplier_projects')->middleware(['auth']);
    Route::post('project/save_plan', 'App\Http\Controllers\Project\ProjectController@save_plan')->middleware(['auth']);


    Route::get('project/sort_by_date', 'App\Http\Controllers\Project\ProjectSortByDateController@sortByDate')->name('projects_sort_by_date')->middleware(['auth']);
    Route::any('projects/statistic', 'App\Http\Controllers\Project\ProjectsStatisticController@getStatistic')->name('get_projects_statistic')->middleware(['auth']);

    Route::get('project/active', 'App\Http\Controllers\Project\ProjectController@getActiveProjects')->name('active_projects')->middleware(['auth']);
    Route::get('project/draft', 'App\Http\Controllers\Project\ProjectController@getDraftProjects')->name('draft_projects')->middleware(['auth']);
    Route::get('project/finished', 'App\Http\Controllers\Project\ProjectController@getFinishedProjects')->name('finished_projects')->middleware(['auth']);

    Route::get('project/getProject', 'App\Http\Controllers\Project\ProjectController@getProjectTable')->name('get_projects_table')->middleware(['auth']);
    Route::get('project/get_projects_table_with_filter', 'App\Http\Controllers\Datatables\ProjectTablesController@getProjectsWithFilter')->name('get_projects_table_with_filter')->middleware(['auth']);

    Route::resource('project', \App\Http\Controllers\Project\ProjectController::class)->middleware(['auth']);
    Route::get('project/{id}/create_plan', 'App\Http\Controllers\Project\ProjectController@create_plan')->name('project_create_plan')->middleware(['auth']);

    Route::any('block/make_active', '\App\Http\Controllers\Block\BlockController@makeBlockActive')->name('make_block_active')->middleware(['auth']);

    Route::get('client/get_clients_table', '\App\Http\Controllers\Datatables\CounterpartyTablesController@getClientTable')->name('get_clients_table')->middleware(['auth']);
    Route::get('supplier/get_suppliers_table', '\App\Http\Controllers\Datatables\CounterpartyTablesController@getSupplierTable')->name('get_suppliers_table')->middleware(['auth']);

    Route::post('client/import', '\App\Http\Controllers\Client\ClientExcelImport@uploadList')->name('upload_client')->middleware(['auth']);
    Route::post('supplier/import', '\App\Http\Controllers\Supplier\SupplierExcelImport@uploadList')->name('upload_supplier')->middleware(['auth']);

    Route::resource('client', \App\Http\Controllers\Client\ClientController::class)->middleware(['auth']);
    Route::resource('supplier', \App\Http\Controllers\Supplier\SupplierController::class)->middleware(['auth']);
    Route::resource('block', \App\Http\Controllers\Block\BlockController::class)->middleware(['auth']);

    Route::any('invoices/export_with_filter_to_excel', '\App\Http\Controllers\ExportToExcel\InvoiceExportController@exportInvoicesWithFilter')
        ->name('invoices_export_with_filter_to_excel')->middleware(['auth']);
    Route::any('invoices/export_project_report_invoices_to_excel', '\App\Http\Controllers\ExportToExcel\InvoiceExportController@exportProjectReportInvoices')
        ->name('export_project_report_invoices_to_excel')->middleware(['auth']);

    Route::get('invoice/get_agreed_invoices_amount', 'App\Http\Controllers\Invoice\InvoiceController@getAgreedInvoicesAmount')->name('get_agreed_invoices_amount')->middleware(['auth']);
    Route::get('invoice/getInvoice', 'App\Http\Controllers\Datatables\InvoiceTablesController@getInvoiceTable')->name('get_invoices_table')->middleware(['auth']);
    Route::get('invoice/get_invoice_for_project_analytics', 'App\Http\Controllers\Datatables\InvoiceTablesController@getInvoiceTableForProjectAnalytics')->name('get_invoice_for_project_analytics')->middleware(['auth']);
    Route::get('invoice/get_invoices_for_counterparty', 'App\Http\Controllers\Invoice\InvoiceController@get_invoices_for_counterparty')->name('get_invoices_for_counterparty')->middleware(['auth']);

    Route::post('invoice/agree', '\App\Http\Controllers\Invoice\InvoiceAgreeController@AgreeInvoice')->name('agree_invoice_rc')->middleware(['auth']);
    Route::post('invoice/potential_losess', '\App\Http\Controllers\Invoice\InvoiceController@potentialLossesUpdate')->name('potential_losess_update')->middleware(['auth']);

    Route::get('invoice/out', 'App\Http\Controllers\Invoice\InvoiceController@getOutInvoices')->name('out_invoices')->middleware(['auth']);
    Route::get('invoice/in', 'App\Http\Controllers\Invoice\InvoiceController@getInInvoices')->name('in_invoices')->middleware(['auth']);
    Route::get('invoice/agreed', 'App\Http\Controllers\Invoice\InvoiceController@getAgreedInvoices')->name('agreed_invoices')->middleware(['auth']);
    Route::get('invoice/for_approval', 'App\Http\Controllers\Invoice\InvoiceController@getInvoicesForApproval')->name('for_approval')->middleware(['auth']);
    Route::get('invoice/sort_by_date', 'App\Http\Controllers\Invoice\InvoiceController@sortByDate')->name('sort_by_date')->middleware(['auth']);
    Route::post('invoice/generate', '\App\Http\Controllers\Invoice\GenerateInvoice@makeInvoice')->name('generate_invoice')->middleware(['auth']);
    Route::post('invoice/make_draft', '\App\Http\Controllers\Invoice\MakeDraftInvoice@addDraft')->name('make_draft_invoice')->middleware(['auth']);
    Route::post('invoice/sell_currency', '\App\Http\Controllers\Invoice\SellInvoiceCurrency@sellCurrency')->name('sell_currency')->middleware(['auth']);
    Route::get('invoice/get_invoice_by_id/{id}', 'App\Http\Controllers\Invoice\InvoiceController@get_invoice_by_id')->middleware(['auth']);
    Route::get('invoice/edit_invoice_by_id/{id}', 'App\Http\Controllers\Invoice\InvoiceController@edit_invoice_by_id')->middleware(['auth']);
    Route::get('invoice/get_invoice_changes/{id}', 'App\Http\Controllers\Invoice\InvoiceController@get_invoice_changes')->middleware(['auth']);

    Route::resource('invoice', \App\Http\Controllers\Invoice\InvoiceController::class)->middleware(['auth']);

    Route::get('container_group/get_table', 'App\Http\Controllers\Datatables\ContainerTablesController@getContainerGroupTable')->name('get_container_group_table')->middleware(['auth']);
    Route::get('container/extended', '\App\Http\Controllers\Datatables\ContainerTablesController@extendedTable')->name('containers_extended')->middleware(['auth']);
    Route::get('container/extended/archive', '\App\Http\Controllers\Datatables\ContainerTablesController@archiveTable')->name('containers_extended_archive')->middleware(['auth']);
    Route::post('container/extended/get_table', '\App\Http\Controllers\Datatables\ContainerTablesController@extendedTableGet')->name('containers_extended_table')->middleware(['auth']);
    Route::get('container/extended/get_table_filters', '\App\Http\Controllers\Datatables\ContainerTablesController@extendedTableGetFilters')->name('containers_extended_table_get_filters')->middleware(['auth']);
    Route::get('container/extended/get_table_columns', '\App\Http\Controllers\Datatables\ContainerTablesController@extendedTableGetColumns')->name('containers_extended_table_get_columns')->middleware(['auth']);
    Route::get('container/extended/export_to_excel', '\App\Http\Controllers\ExportToExcel\ContainerExportController@exportExtendedTable')->name('containers_export_to_excel')->middleware(['auth']);

    Route::post('container/extended/edit', '\App\Http\Controllers\Container\ContainerController@update_list')->name('edit_containers_list')->middleware(['auth']);
    Route::post('container/extended/load_table_for_filter', '\App\Http\Controllers\Datatables\ContainerTablesController@loadTableForFilter')->name('load_table_for_filter')->middleware(['auth']);

    Route::get('container/getContainer', 'App\Http\Controllers\Container\ContainerController@getContainer')->name('get_containers_table')->middleware(['auth']);
    Route::get('container/{id}/return', 'App\Http\Controllers\Container\ContainerController@makeReturn')->name('return_container')->middleware(['auth']);
    Route::resource('container', \App\Http\Controllers\Container\ContainerController::class)->middleware(['auth']);
    Route::resource('container_problem', \App\Http\Controllers\Container\ContainerProblemController::class)->middleware(['auth']);
    Route::post('container_group/upload_list', '\App\Http\Controllers\Container\ContainerGroupController@uploadList')
        ->name('container_group_upload_list')->middleware(['auth']);

    Route::any('container_group/action', '\App\Http\Controllers\Container\ContainerGroupController@container_group_actions')
        ->name('container_group_actions')->middleware(['auth']);

    Route::any('containers/download', '\App\Http\Controllers\Container\ContainerProcessing@DownloadExcel')
        ->name('containers_download')->middleware(['auth']);
    Route::any('containers/preview_actions', '\App\Http\Controllers\Container\ContainerProcessing@uploadList')
        ->name('containers_preview_actions')->middleware(['auth']);
    Route::post('containers/save_actions', '\App\Http\Controllers\Container\ContainerProcessing@saveActions')
        ->name('containers_save_actions')->middleware(['auth']);
    Route::get('containers/processing', '\App\Http\Controllers\Container\ContainerProcessing@index')->name('containers_processing')->middleware(['auth']);

    Route::resource('project.container_group', \App\Http\Controllers\Container\ContainerGroupController::class)->middleware(['auth']);
    Route::resource('container_group_location', \App\Http\Controllers\Container\ContainerGroupLocationController::class)->middleware(['auth']);

    Route::resource('container_group', \App\Http\Controllers\Container\ContainerGroupController::class)->middleware(['auth']);

    Route::resource('block_items', \App\Http\Controllers\Block\BlockItemController::class)->middleware(['auth']);

    Route::resource('application', \App\Http\Controllers\Application\ApplicationController::class)->middleware(['auth']);

    Route::post('notification/make_read', '\App\Http\Controllers\Notification\NotificationController@makeRead')->middleware(['auth']);
    Route::get('notification/archive', '\App\Http\Controllers\Notification\NotificationController@showArchive')->name('show_notifications_archive')->middleware(['auth']);
    Route::post('notification/make_all_read', '\App\Http\Controllers\Notification\NotificationController@makeAllRead')->middleware(['auth']);
    Route::post('notification/add_all_read_to_archive', '\App\Http\Controllers\Notification\NotificationController@addAllReadToArchive')->name('add_all_read_to_archive_notifications')->middleware(['auth']);
    Route::get('notification/get_notifications', '\App\Http\Controllers\Notification\NotificationController@getNotifications')->middleware(['auth']);
    Route::resource('notification', \App\Http\Controllers\Notification\NotificationController::class)->middleware(['auth']);

    Route::resource('own_container', \App\Http\Controllers\Container\OwnContainerController::class)->middleware(['auth']);
    Route::resource('container_project', \App\Http\Controllers\Container\ContainerProjectController::class)->middleware(['auth']);



    Route::get('contract/client', 'App\Http\Controllers\Contract\ContractController@getClientContracts')->name('client_contracts')->middleware(['auth']);
    Route::get('contract/supplier', 'App\Http\Controllers\Contract\ContractController@getSupplierContracts')->name('supplier_contracts')->middleware(['auth']);
    Route::resource('contract', \App\Http\Controllers\Contract\ContractController::class)->middleware(['auth']);



    Route::get('task/{id}/upload_upd', '\App\Http\Controllers\WorkRequest\WorkRequestController@uploadUpd')->name('upload_upd')->middleware(['auth']);
    Route::get('task/all', '\App\Http\Controllers\Task\TaskController@allIncomeTask')->name('all_income_tasks')->middleware(['auth']);
    Route::get('task/income', '\App\Http\Controllers\Task\TaskController@incomeTask')->name('income_tasks')->middleware(['auth']);
    Route::get('task/outcome', '\App\Http\Controllers\Task\TaskController@outcomeTask')->name('outcome_tasks')->middleware(['auth']);
    Route::get('task/done', '\App\Http\Controllers\Task\TaskController@doneTask')->name('done_tasks')->middleware(['auth']);
    Route::get('task/important', '\App\Http\Controllers\Task\TaskController@allIncomeTask')->name('important_tasks')->middleware(['auth']);
    Route::get('task/create_task_modal', '\App\Http\Controllers\Task\TaskController@createTaskModal')->middleware(['auth']);
    Route::post('task/handler', '\App\Http\Controllers\Task\TaskController@handler')->middleware(['auth']);
    Route::post('upload_file_to_task', '\App\Http\Controllers\Task\TaskController@addFileToTask')->name('upload_file_to_task')->middleware(['auth']);
    Route::get('task/trash', '\App\Http\Controllers\Task\TaskController@trashTask')->name('trash_tasks')->middleware(['auth']);
    Route::any('task/get_tasks_table', 'App\Http\Controllers\Task\TaskController@getTaskTable')->name('get_tasks_table')->middleware(['auth']);
    Route::resource('task', \App\Http\Controllers\Task\TaskController::class)->middleware(['auth']);


    Route::get('work_request/{id}/upload_upd', '\App\Http\Controllers\WorkRequest\WorkRequestController@uploadUpd')->name('work_request_upload_upd')->middleware(['auth']);
    Route::get('work_request/all', '\App\Http\Controllers\WorkRequest\WorkRequestController@allIncomeWorkRequest')->name('all_income_work_requests')->middleware(['auth']);
    Route::get('work_request/income', '\App\Http\Controllers\WorkRequest\WorkRequestController@incomeWorkRequest')->name('income_work_requests')->middleware(['auth']);
    Route::get('work_request/outcome', '\App\Http\Controllers\WorkRequest\WorkRequestController@outcomeWorkRequest')->name('outcome_work_requests')->middleware(['auth']);
    Route::get('work_request/done', '\App\Http\Controllers\WorkRequest\WorkRequestController@doneWorkRequest')->name('done_work_requests')->middleware(['auth']);
    Route::get('work_request/important', '\App\Http\Controllers\WorkRequest\WorkRequestController@allIncomeWorkRequest')->name('important_work_requests')->middleware(['auth']);
    Route::get('work_request/create_task_modal', '\App\Http\Controllers\WorkRequest\WorkRequestController@createWorkRequestModal')->middleware(['auth']);
    Route::post('work_request/handler', '\App\Http\Controllers\WorkRequest\WorkRequestController@handler')->middleware(['auth']);
    Route::post('upload_file_to_work_request', '\App\Http\Controllers\WorkRequest\WorkRequestController@addFileToWorkRequest')->name('upload_file_to_work_requests')->middleware(['auth']);
    Route::any('work_request/get_work_requests_table', 'App\Http\Controllers\WorkRequest\WorkRequestController@getWorkRequestTable')->name('get_work_requests_table')->middleware(['auth']);
    Route::resource('work_request', \App\Http\Controllers\WorkRequest\WorkRequestController::class)->middleware(['auth']);



    Route::get('user/my-profile', 'App\Http\Controllers\User\UserController@my_profile')->name('my_profile')->middleware(['auth']);
    Route::post('user/upload-avatar', 'App\Http\Controllers\User\UserController@upload_avatar')->name('upload_avatar')->middleware(['auth']);
    Route::post('user/change_language', 'App\Http\Controllers\User\UserController@change_language')->name('change_language')->middleware(['auth']);

    Route::post('user/update-profile', 'App\Http\Controllers\User\UserController@update_profile')->name('update_profile')->middleware(['auth']);

    Route::post('xeditable/update','App\Http\Controllers\XEditable@update' )->name('x-editable')->middleware(['auth']);
    Route::post('search','App\Http\Controllers\SearchController@search' )->name('search')->middleware(['auth']);
    Route::any('yandexdisk','App\Http\Controllers\YandexDiskController@check' )->name('yandexdisk')->middleware(['auth']);
    Route::resource('countries',App\Http\Controllers\Setting\CountryController::class)->middleware(['auth']);

    Route::any('select2-autocomplete-ajax','App\Http\Controllers\Select2AutocompleteController@dataAjax' )->middleware(['auth']);

    //////////////////ajax routes invoices
    Route::get('invoice/remove_upd_file/{id}/{key}', '\App\Http\Controllers\Invoice\InvoiceController@removeUpdAjax')->middleware(['auth']);
    Route::get('invoice/remove_invoice_file/{id}/{key}', '\App\Http\Controllers\Invoice\InvoiceController@removeInvoiceAjax')->middleware(['auth']);
    Route::get('invoice/remove_payment_order_file/{id}/{key}', '\App\Http\Controllers\Invoice\InvoiceController@removePaymentOrderAjax')->middleware(['auth']);

    Route::any('invoice/upload_invoice_file/{id}', '\App\Http\Controllers\Invoice\InvoiceController@uploadInvoiceAjax')->name('upload_invoice_file_ajax')->middleware(['auth']);

    Route::get('invoice/load_table_row/{id}', '\App\Http\Controllers\Invoice\InvoiceController@loadTableRow')->middleware(['auth']);
    Route::get('invoice/load_modal_table_row/{id}', '\App\Http\Controllers\Invoice\InvoiceController@loadModalTableRow')->middleware(['auth']);

    Route::post('invoice/delete_row/{id}', '\App\Http\Controllers\Invoice\InvoiceController@deleteRow')->middleware(['auth']);
    Route::get('invoices/get_invoices_project', '\App\Http\Controllers\Datatables\InvoiceTablesController@get_invoices_with_filter')->name('get_invoices_project')->middleware(['auth']);

    //////////////////ajax remove
    Route::post('project/delete_row/{id}', '\App\Http\Controllers\Project\ProjectController@deleteRow')->middleware(['auth']);
    Route::post('task/delete_row/{id}', '\App\Http\Controllers\Task\TaskController@deleteRow')->middleware(['auth']);
    Route::post('work_request/delete_row/{id}', '\App\Http\Controllers\WorkRequest\WorkRequestController@deleteRow')->middleware(['auth']);
    Route::post('application/delete_row/{id}', '\App\Http\Controllers\Application\ApplicationController@deleteRow')->middleware(['auth']);
    Route::post('client/delete_row/{id}', '\App\Http\Controllers\Client\ClientController@deleteRow')->middleware(['auth']);
    Route::post('supplier/delete_row/{id}', '\App\Http\Controllers\Supplier\SupplierController@deleteRow')->middleware(['auth']);

    //////////////////ajax routes containers
    Route::get('container_group/load_table_row/{id}', '\App\Http\Controllers\Container\ContainerGroupController@loadTableRow')->middleware(['auth']);

    /////////////////reports

    Route::get('report/client-supplier_summary', '\App\Http\Controllers\Report\ReportController@clientSupplierSummaryIndex')
        ->name('report/client-supplier_summary')
        ->middleware(['auth']);
    Route::any('report/client-supplier_summary/load', '\App\Http\Controllers\Report\ReportController@clientSupplierSummaryLoad')
        ->name('report_client_supplier_summary_load')
        ->middleware(['auth']);
    Route::any('report/client-supplier_summary/export', '\App\Http\Controllers\Report\ReportController@clientSupplierSummaryLoadExportToExcel')
        ->name('export_report_client_supplier_balance')
        ->middleware(['auth']);
    Route::get('report/credit', 'App\Http\Controllers\Report\ReportController@getCredit')->name('report_get_credit')->middleware(['auth']);
    Route::get('report/debit', 'App\Http\Controllers\Report\ReportController@getDebit')->name('report_get_debit')->middleware(['auth']);
    Route::get('report/losses', 'App\Http\Controllers\Report\ReportController@getLosses')->name('report_get_losses')->middleware(['auth']);
    Route::get('report/potential_losses', 'App\Http\Controllers\Report\ReportController@getPotentialLosses')->name('report_get_potential_losses')->middleware(['auth']);

    Route::get('report/project', 'App\Http\Controllers\Report\ReportController@ReportProject')->name('report_project_choose_type')->middleware(['auth']);

    Route::any('report/project/result', 'App\Http\Controllers\Report\ReportController@getReportProject')->name('get_report_project')->middleware(['auth']);

    Route::get('report/user_invoices', function (){
        return view('report.user_invoices_choose',[
            'users' => \App\Models\User::all()
        ]);
    })->name('report_user_invoices_choose_type')->middleware(['auth']);

    Route::get('report/user_invoices', 'App\Http\Controllers\Report\ReportController@ReportUserInvoices')->name('report_user_invoices_choose_type')->middleware(['auth']);

    Route::any('report/user_invoices/result', 'App\Http\Controllers\Report\ReportController@getReportUserInvoices')->name('get_report_user_invoices')->middleware(['auth']);

    //////////////////ajax routes containers
    Route::post('container/delete_row/{id}', '\App\Http\Controllers\Container\ContainerController@deleteRow')->middleware(['auth']);


    Route::get('invoices/get_invoices_with_filter', 'App\Http\Controllers\Datatables\InvoiceTablesController@getInvoicesWithFilter')->name('get_invoices_with_filter')->middleware(['auth']);

    Route::any('invoices/download/losses_table', '\App\Http\Controllers\ExportToExcel\InvoiceExportController@exportLosses')
        ->name('losses_table_invoices_download')->middleware(['auth']);


});


Route::middleware(['role:director|super-admin'])->prefix('/')->group(function (){
    Route::get('users', 'App\Http\Controllers\User\UserController@all')->name('all_users')->middleware(['auth']);
    Route::get('user/statistic', 'App\Http\Controllers\User\UserController@allUsersStatistic')->name('all_users_statistic')->middleware(['auth']);
    Route::get('user/create', 'App\Http\Controllers\User\UserController@create_user')->name('create_user')->middleware(['auth']);
    Route::post('user/{id}/delete', 'App\Http\Controllers\User\UserController@delete_user')->name('delete_user')->middleware(['auth']);
    Route::get('user/{id}/statistic', 'App\Http\Controllers\User\UserController@getUserStatistic')->name('get_user_statistic')->middleware(['auth']);
    Route::post('user/store', 'App\Http\Controllers\User\UserController@store_new_user')->name('store_new_user')->middleware(['auth']);
    Route::get('user/edit/{id}', 'App\Http\Controllers\User\UserController@edit_user')->name('edit_user')->middleware(['auth']);
    Route::post('user/update/{id}', 'App\Http\Controllers\User\UserController@update_user')->name('update_user')->middleware(['auth']);
    Route::post('user/update_permissions', 'App\Http\Controllers\User\UserController@update_user_permissions')->name('update_user_permissions')->middleware(['auth']);
    Route::post('project/set_status_in_work', 'App\Http\Controllers\Project\ProjectController@setStatusInWork')->name('set_status_in_work')->middleware(['auth']);

    Route::post('application/confirm_containers_remove', 'App\Http\Controllers\Application\ApplicationController@confirmContainersRemove')
        ->name('confirm_containers_remove')
        ->middleware(['auth']);

    Route::post('application/unblock_processing', 'App\Http\Controllers\Container\ContainerController@unblockProcessing')
        ->name('unblock_processing')
        ->middleware(['auth']);

    Route::get('expense_type/load_expense_types_by_category', 'App\Http\Controllers\ExpenseTypeController@loadExpenseTypesByCategory')
        ->name('load_expense_types_by_category')
        ->middleware(['auth']);

    Route::resource('expense_type', \App\Http\Controllers\ExpenseTypeController::class)->middleware(['auth']);

    Route::get('/history', 'App\Http\Controllers\Audit\AuditController@index')->middleware(['auth'])->name('history');
    Route::get('/history/get_audits_table', 'App\Http\Controllers\Datatables\AuditTablesController@getAuditTable')->middleware(['auth'])->name('get_audits_table');
    Route::get('/history/get_component_history_table', 'App\Http\Controllers\Datatables\AuditTablesController@getComponentHistoryTable')->middleware(['auth'])->name('get_component_history_table');

    Route::prefix('settings')->middleware(['auth'])->group(function () {
        Route::get('/currency_ratio','App\Http\Controllers\Setting\CurrencyRatioController@index')
            ->name('currency_ratio_settings');
        Route::post('/update_currency_rates','App\Http\Controllers\Setting\CurrencyRatioController@updateRates')
            ->name('update_currency_rates');
        Route::get('/agree_invoices_settings','App\Http\Controllers\Setting\AgreeInvoicesController@agreeInvoicesSettings')
            ->name('agree_invoices_settings');
        Route::post('/update_agree_invoices_settings','App\Http\Controllers\Setting\AgreeInvoicesController@updateAgreeInvoicesSettings')
            ->name('update_agree_invoices_settings');
        Route::get('/currency_ratio','App\Http\Controllers\Setting\CurrencyRatioController@index')->name('currency_ratio_settings');
        Route::post('/update_currency_rates','App\Http\Controllers\Setting\CurrencyRatioController@updateRates')->name('update_currency_rates');
    });




    ///////////////////ajax restore
    Route::post('project/restore_row/{id}', '\App\Http\Controllers\Project\ProjectController@restoreRow')->middleware(['auth']);
    Route::post('invoice/restore_row/{id}', '\App\Http\Controllers\Invoice\InvoiceController@restoreRow')->middleware(['auth']);
    Route::post('task/restore_row/{id}', '\App\Http\Controllers\Task\TaskController@restoreRow')->middleware(['auth']);
    Route::post('work_request/restore_row/{id}', '\App\Http\Controllers\WorkRequest\WorkRequestController@restoreRow')->middleware(['auth']);
    Route::post('application/restore_row/{id}', '\App\Http\Controllers\Application\ApplicationController@restoreRow')->middleware(['auth']);
    Route::post('client/restore_row/{id}', '\App\Http\Controllers\Client\ClientController@restoreRow')->middleware(['auth']);
    Route::post('supplier/restore_row/{id}', '\App\Http\Controllers\Supplier\SupplierController@restoreRow')->middleware(['auth']);

});
