<?php

namespace App\Filters;

use Illuminate\Support\Facades\Auth;

class ApplicationFilter extends QueryFilter
{
    public function type($value){
        if ($value!= 'Все') return $this->builder->where('type', $value);
    }

    public function supplier($value){
        return $this->builder->where('supplier_id', $value);
    }

    public function client($value){
        return $this->builder->where('client_id', $value);
    }

    public function filter($value){
        if($value == 'trash' && in_array(Auth::user()->getRoleNames()[0], ['super-admin','director'])){
            return $this->builder->onlyTrashed();
        }
        elseif($value == 'active'){
            return $this->builder->where('status', 'В работе');
        }
        elseif($value == 'done'){
            return $this->builder->where('status', 'Завершена');
        }
    }

}
