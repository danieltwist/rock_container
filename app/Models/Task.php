<?php

namespace App\Models;

use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Task extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function resolveRouteBinding($value, $field = null)
    {
        if(in_array(Auth::user()->getRoleNames()[0], ['super-admin','director']))
            return $this->withTrashed()->where($field ?? $this->getRouteKeyName(), $value)->first();
        else
            return $this->where($field ?? $this->getRouteKeyName(), $value)->first();
    }

    protected $casts = [
        'to_users' => 'array',
        'responsible_user' => 'array',
        'additional_users' => 'array'
    ];
    protected $dates = ['deadline', 'done'];
    protected $guarded = [];

    public function from(){
        return $this->belongsTo(User::class, 'from_user_id', 'id');
    }

    public function to(){
        return $this->belongsTo(User::class, 'to_users', 'id');
    }

    public function accepted_user(){
        return $this->belongsTo(User::class, 'accepted_user_id', 'id');
    }

    public function project(){
        return $this->hasOne(Project::class,'id','model_id');
    }

    public function invoice(){
        return $this->hasOne(Invoice::class,'id','model_id');
    }

    public function for_project(){
        return $this->hasOne(Project::class,'id','project_id');
    }

    public function client(){
        return $this->hasOne(Client::class,'id','model_id');
    }

    public function supplier(){
        return $this->hasOne(Supplier::class,'id','model_id');
    }

    public function contract(){
        return $this->hasOne(Contract::class,'id','model_id');
    }

    public function container(){
        return $this->hasOne(Container::class,'id','model_id');
    }

    public function application(){
        return $this->hasOne(Application::class,'id','model_id');
    }

    public function block(){
        return $this->hasOne(Block::class,'id','model_id');
    }

    public function scopeFilter(Builder $builder, QueryFilter $filter){
        return $filter->apply($builder);
    }
}
