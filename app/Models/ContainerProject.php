<?php

namespace App\Models;

use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContainerProject extends Model
{
    use HasFactory;

    protected $casts = [
        'expenses' => 'array'
    ];

    protected $guarded = [];

    public function project(){
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function client(){
        return $this->belongsTo(Client::class,'client_id');
    }

    public function container(){
        return $this->belongsTo(Container::class,'container_id');
    }

    public function scopeFilter(Builder $builder, QueryFilter $filter){
        return $filter->apply($builder);
    }
}
