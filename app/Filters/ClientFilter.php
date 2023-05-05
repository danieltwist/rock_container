<?php

namespace App\Filters;

use Illuminate\Support\Facades\Auth;

class ClientFilter extends QueryFilter
{
    public function filter($value){
        if($value == 'trash' && in_array(Auth::user()->getRoleNames()[0], ['super-admin','director'])){
            return $this->builder->onlyTrashed();
        }
    }
}
