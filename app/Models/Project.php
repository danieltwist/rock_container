<?php

namespace App\Models;

use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Project extends Model
{
    use HasFactory;

    protected $casts = [
        'access_to_project' => 'array',
    ];

    protected $guarded = [];

    public function client(){
        return $this->belongsTo(Client::class);
    }

    public function additional_client(){
        foreach (unserialize($this->additional_clients) as $client_id){
            $client_object = Client::find($client_id);
            $client_names [] = $client_object->name;
        }
        return $client_names;
    }

    public function all_clients(){
        $client [] = "$this->client_id";
        $additional_clients = unserialize($this->additional_clients);
        if($additional_clients)
            return array_merge($client, $additional_clients);
        else return $client;
    }

    public function active_block(){
        return $this->belongsTo(Block::class,'active_block_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function manager(){
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function logist(){
        return $this->belongsTo(User::class, 'logist_id');
    }

    public function invoices(){
        return $this->hasMany(Invoice::class,'project_id');
    }

    public function applications(){
        return $this->hasMany(Application::class,'project_id');
    }

    public function used_containers(){
        return $this->hasMany(ContainerUsageStatistic::class,'project_id');
    }

    public function containers(){
        return $this->hasMany(Container::class);
    }

    public function containers_used(){
        return $this->hasMany(ContainerUsageStatistic::class);
    }

    public function comments(){
        return $this->hasMany(ProjectComment::class)->orderBy('created_at', 'desc');
    }

    public function expense(){
        return $this->hasOne(ProjectExpense::class);
    }

    public function scopeFilter(Builder $builder, QueryFilter $filter){
        return $filter->apply($builder);
    }

}
