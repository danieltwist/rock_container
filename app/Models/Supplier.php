<?php

namespace App\Models;

use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Supplier extends Model
{
    protected $guarded = [];

    use HasFactory;
    use SoftDeletes;

    public function resolveRouteBinding($value, $field = null)
    {
        if(in_array(Auth::user()->getRoleNames()[0], ['super-admin','director']))
            return $this->withTrashed()->where($field ?? $this->getRouteKeyName(), $value)->first();
        else
            return $this->where($field ?? $this->getRouteKeyName(), $value)->first();
    }

    public function contracts(){
        return $this->hasMany(Contract::class,'supplier_id');
    }

    public function getShortAttribute()
    {
        return $this->attributes['short_name'];
    }

    public function scopeFilter(Builder $builder, QueryFilter $filter){
        return $filter->apply($builder);
    }
}
