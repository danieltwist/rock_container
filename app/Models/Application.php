<?php

namespace App\Models;

use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Application extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    public function resolveRouteBinding($value, $field = null)
    {
        if(in_array(Auth::user()->getRoleNames()[0], ['super-admin','director']))
            return $this->withTrashed()->where($field ?? $this->getRouteKeyName(), $value)->first();
        else
            return $this->where($field ?? $this->getRouteKeyName(), $value)->first();
    }

    protected $casts = [
        'send_from_city' => 'array',
        'send_to_city' => 'array',
        'snp_range' => 'array',
        'containers' => 'array',
        'containers_removed' => 'array',
        'contract_info' => 'array',
        'place_of_delivery_city' => 'array',
        'invoices_generate' => 'array'
    ];

    protected $dates = ['finished_at'];

    protected $guarded = [];

    public function contract(){
        return $this->belongsTo(Contract::class);
    }

    public function client(){
        return $this->belongsTo(Client::class);
    }

    public function project(){
        return $this->belongsTo(Project::class);
    }

    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }

    public function block(){
        return $this->belongsTo(Block::class);
    }

    public function invoices(){
        return $this->hasMany(Invoice::class);
    }

    public function scopeFilter(Builder $builder, QueryFilter $filter){
        return $filter->apply($builder);
    }

}
