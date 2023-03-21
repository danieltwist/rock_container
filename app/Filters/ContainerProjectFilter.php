<?php

namespace App\Filters;

class ContainerProjectFilter extends QueryFilter
{
    public function need_to_process(){
        return $this->builder->whereIn('status', ['Добавлен вручную', 'Добавлен автоматически']);
    }

}
