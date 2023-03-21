<?php

namespace App\Models;

use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

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
