<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function from_user(){
        return $this->hasOne(User::class, 'name', 'from');
    }

    public function to(){
        return $this->belongsTo(User::class, 'to_id', 'id');
    }

    public function project(){
        return $this->hasOne(Project::class,'id','project_id');
    }
}
