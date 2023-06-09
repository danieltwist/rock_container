<?php

namespace App\Models;

use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Container extends Model
{
    use HasFactory;

    protected $casts = [
        'supplier_snp_range' => 'array',
        'client_snp_range' => 'array',
    ];

    protected $guarded = [];

    public function project(){
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function supplier(){
        return $this->belongsTo(Supplier::class,'supplier_id');
    }

    public function problem(){
        return $this->belongsTo(ContainerProblem::class,'problem_id');
    }

    public function own(){
        return $this->hasOne(OwnContainer::class, 'container_id');
    }

    public function container_projects(){
        return $this->hasMany(ContainerProject::class, 'container_id');
    }

    public function scopeSvv($query){
        return $query->whereNotNull('svv');
    }

    public function scopeApplication($query, $application_id)
    {
        return $query->where('supplier_application_id', $application_id)
            ->orWhere('relocation_application_id', $application_id)
            ->orWhere('client_application_id', $application_id);
    }

    public function scopeFilter(Builder $builder, QueryFilter $filter){
        return $filter->apply($builder);
    }


}
