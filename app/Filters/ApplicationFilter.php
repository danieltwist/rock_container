<?php

namespace App\Filters;

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

}
