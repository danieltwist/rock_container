<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContainerUsageStatistic extends Model
{
    use HasFactory;

    protected $casts = [
        'fields' => 'array',
    ];

    protected $guarded = [];

    public function project(){
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function application(){
        return $this->belongsTo(Project::class, 'application_id');
    }

    public function container(){
        return $this->belongsTo(Project::class, 'container_id');
    }

}
