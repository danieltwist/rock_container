<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    use HasFactory;

    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }

    public function contract(){
        return $this->belongsTo(Contract::class);
    }

    public function invoices(){
        return $this->hasMany(Invoice::class, 'block_id');
    }

    public function applications(){
        return $this->hasMany(Application::class, 'block_id');
    }

    public function project(){
        return $this->belongsTo(Project::class,'project_id');
    }
}
