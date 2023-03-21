<?php

namespace App\Filters;

class ContainerFilter extends QueryFilter
{
    public function using_now(){
        return $this->builder->whereNotNull('project_id');
    }

    public function with_problem(){
        return $this->builder->whereNotNull('problem_id');
    }

    public function svv(){
        return $this->builder->whereNotNull('svv');
    }

    public function filter($value){

        if ($value == 'with_problem'){
            return $this->builder->whereNotNull('problem_id');
        }

        elseif($value == 'using_now') {
            return $this->builder->whereNotNull('project_id');
        }

        elseif($value == 'svv') {
            return $this->builder->whereNotNull('svv');
        }

        elseif ($value == 'own') {
            return $this->builder->where('type', 'В собственности');
        }

        elseif ($value == 'rent') {
            return $this->builder->where('type', 'Аренда');
        }

        elseif ($value == 'archive') {
            return $this->builder->whereNotNull('archive');
        }

        elseif ($value == 'free') {
            return $this->builder->whereNull('project_id')->whereNull('archive');
        }

        else return null;

    }

}
