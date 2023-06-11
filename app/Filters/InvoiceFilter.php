<?php

namespace App\Filters;

use Illuminate\Support\Facades\Auth;

class InvoiceFilter extends QueryFilter
{
    public function project($value){
        return $this->builder->where('project_id', $value);
    }

    public function application($value){
        return $this->builder->where('application_id', $value);
    }

    public function direction($value){
        return $this->builder->where('direction', $value);
    }

    public function sub_status($value){
        if($value != 'Все'){
            if($value == 'Без дополнительного статуса') {
                return $this->builder->whereNull('sub_status');
            }
            else return $this->builder->where('sub_status', $value);
        }
    }

    public function status($value){
        return $this->builder->where('status', $value);
    }

    public function client($value){
        return $this->builder->where('client_id', $value);
    }

    public function supplier($value){
        return $this->builder->where('supplier_id', $value);
    }

    public function in(){
    	return $this->builder->where('direction', 'Расход');
    }

    public function out(){
    	return $this->builder->where('direction', 'Доход');
    }

    public function for_approval(){
    	return $this->builder->where('status','Счет на согласовании');
    }

    public function agreed(){
    	return $this->builder->where('status', 'Счет согласован на оплату')->orWhere('status', 'Согласована частичная оплата');
    }

    public function paid(){
    	return $this->builder->where('status', 'Оплачен');
    }

    public function partially_paid(){
    	return $this->builder->Where('status', 'Частично оплачен');
    }

    public function my(){
        return $this->builder->where(function ($query) {
            $query->where('user_id', auth()->user()->id)->orWhere('user_add', auth()->user()->name);
        });
    }

    public function trash(){
        if(in_array(Auth::user()->getRoleNames()[0], ['super-admin','director'])){
            return $this->builder->onlyTrashed();
        }
    }

    public function on_approval(){
    	return $this->builder->whereNotIn('status', ['Оплачен','Частично оплачен','Счет согласован на оплату','Ожидается оплата'])
    	->where(function ($query) {
    		  $query->where('agree_1', 'like', '%Счет согласован на оплату%')
    				->orWhere('agree_2', 'like', '%Счет согласован на оплату%')
    				->orWhere('agree_1', 'like', '%Согласована частичная оплата%')
    				->orWhere('agree_2', 'like', '%Согласована частичная оплата%');
    	});
    }

    public function waiting_invoices(){
    	return $this->builder->where('status', 'Создан черновик инвойса');
    }

    public function user($value){
    	$user = \App\Models\User::where('name', $value)->first();
    	return $this->builder->where('user_add', $user->name);
    }

    public function filter($value){
        if($value == 'in'){
            return $this->builder->where('direction', 'Расход');
        }

        elseif($value == 'out'){
            return $this->builder->where('direction', 'Доход');
        }

        elseif($value == 'for_approval'){
            return $this->builder->where('status','Счет на согласовании');
        }

        elseif($value == 'agreed'){
            return $this->builder->where('status', 'Счет согласован на оплату')->orWhere('status', 'Согласована частичная оплата');
        }

        elseif($value == 'paid'){
            return $this->builder->where('status', 'Оплачен');
        }

        elseif($value == 'partially_paid'){
            return $this->builder->Where('status', 'Частично оплачен');
        }

        elseif($value == 'my'){
            return $this->builder->where(function ($query) {
                $query->where('user_id', auth()->user()->id)->orWhere('user_add', auth()->user()->name);
            });
        }

        elseif($value == 'trash'){
            if(in_array(Auth::user()->getRoleNames()[0], ['super-admin','director'])){
                return $this->builder->onlyTrashed();
            }
        }

        elseif($value == 'on_approval'){
            return $this->builder->whereNotIn('status', ['Оплачен','Частично оплачен','Счет согласован на оплату','Ожидается оплата'])
            ->where(function ($query) {
                  $query->where('agree_1', 'like', '%Счет согласован на оплату%')
                        ->orWhere('agree_2', 'like', '%Счет согласован на оплату%')
                        ->orWhere('agree_1', 'like', '%Согласована частичная оплата%')
                        ->orWhere('agree_2', 'like', '%Согласована частичная оплата%');
            });
        }

        elseif($value == 'waiting_invoices'){
            return $this->builder->where('status', 'Создан черновик инвойса');
        }

        elseif(strpos($value, 'client') !== false){
            $client = explode('=', $value);
            return $this->builder->where('client_id', $client[1]);
        }

        elseif(strpos($value, 'supplier') !== false){
            $supplier = explode('=', $value);
            return $this->builder->where('supplier_id', $supplier[1]);
        }

        elseif(strpos($value, 'user') !== false){
            $user = \App\Models\User::where('name', explode('=', $value)[1])->first();
            return $this->builder->where('user_add', $user->name);
        }

        elseif($value == 'credit'){
            $projects = \App\Models\Project::whereIn('status',['В работе', 'Завершен'])
                        ->where('paid', 'Не оплачен')
                        ->pluck('id')
                        ->toArray();
            return $this->builder->where('direction','Расход')->whereIn('project_id', $projects)->where('status','<>','Оплачен');
        }

        elseif($value == 'debit') {
            $projects = \App\Models\Project::whereIn('status', ['В работе', 'Завершен'])
                //->whereDate('planned_payment_date', '<=', date('Y-m-d'))
                ->where('paid', 'Не оплачен')
                ->pluck('id')
                ->toArray();

            return $this->builder->where('direction', 'Доход')->where('status', '<>', 'Оплачен')->whereIn('project_id', $projects);
        }

        elseif($value == 'potential_losses'){
            $projects = \App\Models\Project::where('status','Завершен')
                ->where('paid', 'Оплачен')
                ->pluck('id')
                ->toArray();

            return $this->builder->where('direction','Расход')->whereNotNull('losses_amount')->whereNull('losses_confirmed')->whereIn('project_id', $projects);

        }

        elseif($value == 'losses'){
            $projects = \App\Models\Project::where('status','Завершен')
                ->where('paid', 'Оплачен')
                ->pluck('id')
                ->toArray();

            return $this->builder->where('direction','Расход')->whereNotNull('losses_confirmed')->whereIn('project_id', $projects);

        }

        elseif($value == 'my'){
            return $this->builder->where('user_add', auth()->user()->name);
        }

        else return null;
    }

    public function second_filter($value){

        if($value == 'my'){
            return $this->builder->where(function ($query) {
                $query->where('user_id', auth()->user()->id)->orWhere('user_add', auth()->user()->name);
            });
        }

        else return null;
    }

}
