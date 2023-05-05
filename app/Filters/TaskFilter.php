<?php

namespace App\Filters;

use Illuminate\Support\Facades\Auth;

class TaskFilter extends QueryFilter
{

    public function filter($value){

        if ($value == 'outcome'){
            return $this->builder->where('from_user_id', Auth::user()->id);
        }

        elseif($value == 'all') {
            return $this->builder->whereJsonContains('to_users', Auth::user()->id);
        }

        elseif ($value == 'done') {
            return $this->builder->where('accepted_user_id', Auth::user()->id)
                ->whereIn('status', ['Выполнена', 'Отправлена на проверку']);
        }

        elseif ($value == 'trash') {
            if(in_array(Auth::user()->getRoleNames()[0], ['super-admin','director']))
                return $this->builder->onlyTrashed();
        }

        elseif ($value == 'important'){
            return $this->builder->where('to_users', '['.Auth::user()->id.']');
        }

        else return null;

    }

    public function user_tasks($id){
        return $this->builder->whereJsonContains('to_users', (int)$id);
    }

    public function trash(){
        return $this->builder->onlyTrashed();
    }

}
