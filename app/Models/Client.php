<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $guarded = [];

    use HasFactory;

    public function contracts(){
        return $this->hasMany(Contract::class,'client_id');
    }

    public function projects(){
        return $this->hasMany(Project::class);
    }

    public function getShortAttribute()
    {
        return $this->attributes['short_name'];
    }

}
