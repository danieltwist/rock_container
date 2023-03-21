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
        $supply = User::role('supply')->pluck('id')->toArray();

        $project = \App\Models\Project::find($project_id);

        if(!is_null($project)){
            $project_create_user = User::findOrFail($project->user_id);
            $project_create_user_role = $project_create_user->getRoleNames()[0];

            if(config('app.prefix_view') == 'rl_' || config('app.prefix_view') == 'ntc_'){
                if(!is_null($project)){
                    if (in_array($role, ['super-admin','director']) ||
                        in_array($user->id, [$project->user_id, $project->manager_id, $project->logist_id]) ||
                        $project->user_id == '2' ||
                        $user->id == '13' ||
                        $user->id == '14' ||
                        ($role == 'logist' && $project_create_user_role == 'logist')){

                        return true;

                    }

                    else return false;

                }

                else return false;
            }
            elseif (config('app.prefix_view') == 'rc_'){
                if(!is_null($project)){
                    if (in_array($role, ['super-admin','director']) ||
                        in_array($user->id, [$project->user_id, $project->manager_id, $project->logist_id]) ||
                        in_array($project->user_id, ['21', '40']) ||
                        ($role == 'supply' && $project_create_user_role == 'supply')){

                        return true;

                    }

                    else return false;

                }

                else return false;
            }
            elseif (config('app.prefix_view') == 'blc_'){
                if(!is_null($project)){
                    if (in_array($role, ['super-admin','director']) ||
                        in_array($user->id, [$project->user_id, $project->manager_id, $project->logist_id]) ||
                        in_array($role, ['logist', 'accountant'])){

                        return true;

                    }

                    else return false;

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

}
