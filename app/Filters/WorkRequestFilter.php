<?php

namespace App\Filters;

use Illuminate\Support\Facades\Auth;

class WorkRequestFilter extends QueryFilter
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
                ->whereIn('status', ['Выполнен', 'Отправлен на проверку']);
        }

        if ($value == 'important'){
            return $this->builder->where('to_users', '['.Auth::user()->id.']');
        }

        else return null;

    }

    public function user_work_requests($id){
        return $this->builder->whereJsonContains('to_users', (int)$id);
    }
}
