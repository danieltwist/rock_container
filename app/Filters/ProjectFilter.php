<?php

namespace App\Filters;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ProjectFilter extends QueryFilter
{
    public function client_id($id){
        return $this->builder->where('client_id', $id);
    }

    public function user_id($id){
        if($id != 'Все') return $this->builder->where('user_id', $id);
    }

    public function manager_id($id){
        if($id != 'Все') return $this->builder->where('manager_id', $id);
    }

    public function logist_id($id){
        if($id != 'Все') return $this->builder->where('logist_id', $id);
    }

    public function status($value){
        return $this->builder->where('status', $value);
    }

    public function filter($value){
        if($value == 'active'){
            return $this->builder->where('active', '1')->where('status', '<>', 'Черновик');
        }

        elseif($value == 'finished'){
            return $this->builder->where('active', '0')->where('status', '<>', 'Черновик')->where('paid', 'Оплачен')->where('archive', '<>', 1);
        }

        elseif($value == 'archive'){
            return $this->builder->where('archive', 1);
        }

        elseif($value == 'finished_paid_date'){
            return $this->builder->where('active', '0')->where('status', '<>', 'Черновик')->where('paid', 'Оплачен');
        }

        elseif($value == 'draft'){
            return $this->builder->where('status', 'Черновик');
        }

        elseif($value == 'done_unpaid'){
            return $this->builder->where('status', 'Завершен')->where('paid', 'Не оплачен');
        }

        elseif($value == 'finished_this_month'){
            return $this->builder->whereMonth('finished_at', Carbon::now()->month);
        }

        elseif(strpos($value, 'user_id') !== false){
            $user = explode('=', $value);
            return $this->builder->where('user_id', $user[1]);
        }

        elseif(strpos($value, 'manager_id') !== false){
            $user = explode('=', $value);
            return $this->builder->where('manager_id', $user[1]);
        }

        elseif(strpos($value, 'client_id') !== false){
            $client = explode('=', $value);
            return $this->builder->where('client_id', $client[1]);
        }

        elseif($value == 'my_projects'){
            $user = auth()->user();
            return $this->builder->where('user_id', $user->id)->orWhere('manager_id', $user->id)->orWhere('logist_id', $user->id);
        }

        elseif($value == 'trash'){
            if(in_array(Auth::user()->getRoleNames()[0], ['super-admin','director'])){
                return $this->builder->onlyTrashed();
            }
        }

        else return null;
    }

}
