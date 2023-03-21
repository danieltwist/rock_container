<?php

namespace App\Models;

use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $casts = [
        'losses_potential' => 'array',
        'payments_history' => 'array',
        'payment_order_file' => 'array',
        'invoice_file' => 'array',
        'upd_file' => 'array',
    ];

    protected $guarded = [];

    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }

    public function client(){
        return $this->belongsTo(Client::class);
    }

    public function block(){
        return $this->belongsTo(Block::class, 'block_id');
    }

    public function project(){
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function application(){
        return $this->belongsTo(Application::class, 'application_id');
    }

    public function actions(){
        return $this->hasMany(ActionRecording::class, 'model_id');
    }

    public function scopeOut($query)
    {
        $query->where('direction', 'Доход');
    }

    public function scopeIn($query)
    {
        $query->where('direction', 'Расход');
    }

    public function scopeFilter(Builder $builder, QueryFilter $filter){
        return $filter->apply($builder);
    }
}
