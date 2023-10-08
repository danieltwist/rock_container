<?php

use App\Models\User;

if(!function_exists('can_edit_this_project')){

    function can_edit_this_project($project_id){

        $user = auth()->user();
        $role = $user->getRoleNames()[0];

        $project = \App\Models\Project::find($project_id);
        if(!is_null($project)){
            if (in_array($role, ['super-admin','director']) || in_array($user->id, [$project->user_id, $project->manager_id, $project->logist_id]) || $user->id == '13'){

                return true;

            }

            else return false;
        }

        else return false;
    }

    function can_edit_this_project_price($project_id){

        $user = auth()->user();
        $role = $user->getRoleNames()[0];

        $project = \App\Models\Project::find($project_id);
        if(!is_null($project)){

            if (in_array($role, ['super-admin','director'])){
                return true;

            }
            else return false;

        }
        else return false;
    }

    function canWorkWithProject($project_id){

        $user = auth()->user();
        $role = $user->getRoleNames()[0];

        $project = \App\Models\Project::find($project_id);

        if(!is_null($project)){
            if(config('app.prefix_view') == 'rl_' || config('app.prefix_view') == 'ntc_'){
                if (in_array($role, ['super-admin','director']) ||
                    $user->can('view and access all projects') ||
                    in_array($user->id, [$project->user_id, $project->manager_id, $project->logist_id]) ||
                    $project->management_expenses == 'on')
                {
                    return true;
                }
                else return false;
            }
            elseif (config('app.prefix_view') == 'rc_'){
                is_null($project->access_to_project) ? $access_to_projects = $project->access_to_project = [] : $access_to_projects = $project->access_to_project;
                if (in_array($role, ['super-admin','director']) ||
                    $user->can('view and access all projects') ||
                    in_array($user->id, [$project->user_id, $project->manager_id, $project->logist_id]) ||
                    in_array($user->id, $access_to_projects) ||
                    $project->management_expenses == 'on')
                {
                    return true;
                }
                else return false;
            }
            elseif (config('app.prefix_view') == 'blc_'){
                if (in_array($role, ['super-admin','director']) ||
                    $user->can('view and access all projects') ||
                    in_array($user->id, [$project->user_id, $project->manager_id, $project->logist_id]) ||
                    $project->management_expenses == 'on')
                {
                    return true;

                }

                else return false;
            }
        }
        else return false;

    }

    function canViewProject($project_id){

        $user = auth()->user();
        $role = $user->getRoleNames()[0];

        $project = \App\Models\Project::find($project_id);

        if(!is_null($project)){
            if(config('app.prefix_view') == 'rl_' || config('app.prefix_view') == 'ntc_'){
                if (in_array($role, ['super-admin','director','accountant']) ||
                    $user->can('view and access all projects') ||
                    in_array($user->id, [$project->user_id, $project->manager_id, $project->logist_id]) ||
                    $project->management_expenses == 'on')
                {
                    return true;
                }
                else return false;
            }
            elseif (config('app.prefix_view') == 'rc_'){
                is_null($project->access_to_project) ? $access_to_projects = $project->access_to_project = [] : $access_to_projects = $project->access_to_project;
                if (in_array($role, ['super-admin','director','accountant']) ||
                    $user->can('view and access all projects') ||
                    in_array($user->id, [$project->user_id, $project->manager_id, $project->logist_id]) ||
                    in_array($user->id, $access_to_projects) ||
                    $project->management_expenses == 'on')
                {
                    return true;
                }
                else return false;
            }
            elseif (config('app.prefix_view') == 'blc_'){
                if (in_array($role, ['super-admin','director','accountant']) ||
                    $user->can('view and access all projects') ||
                    in_array($user->id, [$project->user_id, $project->manager_id, $project->logist_id]) ||
                    $project->management_expenses == 'on')
                {
                    return true;

                }

                else return false;
            }
        }
        else return false;
    }

    function containerHasProject($container_id, $project_id){

        $container_project = \App\Models\ContainerProject::where('container_id', $container_id)->where('project_id', $project_id)->count();

        if ($container_project > 0) return true; else return false;

    }

    function checkUploadedFileInvoice($invoice_id){
        $invoice = \App\Models\Invoice::find($invoice_id);

        if(!is_null($invoice)){
            $upd = @unserialize($invoice->upd);
            if($upd !== false){
                $can_upd = true;
            }
            else $can_upd = false;

            $file = @unserialize($invoice->file);
            if($file !== false){
                $can_file = true;
            }
            else $can_file = false;

            $payment_order = @unserialize($invoice->payment_order);
            if($payment_order !== false){
                $can_payment_order = true;
            }
            else $can_payment_order = false;

            $agree1 = @unserialize($invoice->agree_1);
            if($agree1 !== false){
                $can_agree1 = true;
            }
            else $can_agree1 = false;

            $agree2 = @unserialize($invoice->agree_2);
            if($agree2 !== false){
                $can_agree2 = true;
            }
            else $can_agree2 = false;

            return [
                'upd' => $can_upd,
                'file' => $can_file,
                'payment_order' => $can_payment_order,
                'agree1' => $can_agree1,
                'agree2' => $can_agree2
            ];
        }

        else return [
            'upd' => false,
            'file' => false,
            'payment_order' => false,
            'agree1' => false,
            'agree2' => false
        ];

    }

    function userInfo($id){
        $user = \App\Models\User::find($id);
        if(!is_null($user)){
            return $user;
        }
        else return false;
    }

    function checkSupplierExist($client_id){
        $client = \App\Models\Client::find($client_id);
        $supplier = \App\Models\Supplier::where('name', $client->name)->first();

        if(!empty($supplier)){
            return $supplier;
        }
        else return false;

    }

    function checkClientExist($supplier_id){
        $supplier = \App\Models\Supplier::find($supplier_id);
        $client = \App\Models\Client::where('name', $supplier->name)->first();

        if(!empty($client)){
            return $client;
        }
        else return false;

    }

    function companyType($id){
        $invoice = \App\Models\Invoice::find($id);
        $invoice->client_id == '' ? $company = 'supplier' : $company = 'client';

        return $company;
    }

    function renameBeforeUpload($file){

        $filename = preg_replace( "/[^(\w)|(\x7F-\xFF)|(\s)|(\-)]/", '', pathinfo($file)['filename']);

        return $filename.'.'.pathinfo($file)['extension'];

    }

    function getFolderUploadInvoice($invoice, $type){

        $project = \App\Models\Project::find($invoice->project_id);

        $project->active == '1' ? $active = 'Активные проекты' : $active = 'Завершенные проекты';

        if ($invoice->supplier_id != '') {
            $company = preg_replace("/[^(\w)|(\x7F-\xFF)|(\s)|(\-)]/", '', $invoice->supplier->name);
            $root_folder = 'public/Проекты/' . $active . '/' . $invoice->project->name;
            $counterparty_type = 'Поставщики';
        }
        else {
            $company = preg_replace("/[^(\w)|(\x7F-\xFF)|(\s)|(\-)]/", '', $invoice->client->name);
            $root_folder = 'public/Проекты/' . $active . '/' . $invoice->project->name;
            $counterparty_type = 'Клиенты';
        }

        switch ($type){
            case 'invoice':
                $folder = $root_folder.'/Счета/'.$counterparty_type.'/'. $company.'/';
                break;
            case 'upd':
                $folder = $root_folder.'/УПД/'.$counterparty_type.'/'. $company.'/';
                break;
            case 'payment_order':
                $folder = $root_folder.'/ПП/'.$counterparty_type.'/'. $company.'/';
                break;
            default:
                $folder = $root_folder;
        }

        return $folder;

    }

    function active_link(){

        $role = auth()->user()->getRoleNames()[0];

        if (in_array($role, ['super-admin','director'])) {
            return false;
        }

        else return true;
    }

    function getNotificationChannel($user_id){
        $user = User::find($user_id);

        return $user->notification_channel;
    }

    function getUserTelegramChatId($user_id){
        $user = User::find($user_id);

        return $user->telegram_chat_id;
    }

    function agreeInfo($invoice_id){
        $invoice = \App\Models\Invoice::withTrashed()->find($invoice_id);
        $agree_info = '';

        if($invoice->agree_1 != ''){
            $agree1 = @unserialize($invoice->agree_1);
            if($agree1 !== false){
                $agree1 = unserialize($invoice->agree_1);
                $status = invoiceStatusSwitch($agree1['status']);
                if(isset($agree1['user_id'])){
                    $agree_info = User::withTrashed()->find($agree1['user_id'])->name.': '. $status. ' '.\Carbon\Carbon::parse($agree1['date'])->format('d.m.Y H:i:s').'<br>';
                }
                else {
                    $agree_info = $status. ' '.\Carbon\Carbon::parse($agree1['date'])->format('d.m.Y H:i:s').'<br>';
                }
            }
            else {
                $agree_info = $invoice->agree_1.'<br>';
            }
        }
        if($invoice->agree_2 != '') {
            $agree2 = @unserialize($invoice->agree_2);
            if ($agree2 !== false) {
                $agree2 = unserialize($invoice->agree_2);
                $status = invoiceStatusSwitch($agree2['status']);
                if (isset($agree2['user_id'])) {
                    $agree_info .= User::withTrashed()->find($agree2['user_id'])->name . ': ' . $status . ' ' . \Carbon\Carbon::parse($agree2['date'])->format('d.m.Y H:i:s') . '<br>';
                } else {
                    $agree_info .= $status . ' ' . \Carbon\Carbon::parse($agree2['date'])->format('d.m.Y H:i:s') . '<br>';
                }
            } else {
                $agree_info .= $invoice->agree_2 . '<br>';
            }
        }

        return $agree_info;
    }

    function invoiceStatusSwitch($status){
        switch($status){
            case('Счет на согласовании'):
                $status = __('invoice.status_agree');
                break;
            case('Создан черновик инвойса'):
                $status = __('invoice.status_draft_invoice');
                break;
            case('Ожидается счет от поставщика'):
                $status = __('invoice.status_waiting_for_invoice');
                break;
            case('Ожидается создание инвойса'):
                $status = __('invoice.status_waiting_for_create_invoice');
                break;
            case('Ожидается оплата'):
                $status = __('invoice.status_waiting_for_payment');
                break;
            case('Ожидается загрузка счета'):
                $status = __('invoice.status_waiting_upload_invoice');
                break;
            case('Счет согласован на оплату'):
                $status = __('invoice.status_agreed');
                break;
            case('Согласована частичная оплата'):
                $status = __('invoice.status_part_agreed');
                break;
            case('Частично оплачен'):
                $status = __('invoice.status_part_paid');
                break;
            case('Оплачен'):
                $status = __('invoice.status_paid');
                break;
            case('Срочно'):
                $status = __('invoice.sub_status_urgent');
                break;
            case('Взаимозачет'):
                $status = __('invoice.sub_status_compensation');
                break;
            case('Отложен'):
                $status = __('invoice.sub_status_postponed');
                break;
            case('Без дополнительного статуса'):
                $status = __('invoice.sub_status_without');
                break;
        }
        return $status;
    }

    function getApplicationName($id){
        $application = \App\Models\Application::withTrashed()->find($id);
        return optional($application)->name;
    }

}
