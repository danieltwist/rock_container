<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $guarded = [];

    use HasFactory;

    public function contracts(){
        return $this->hasMany(Contract::class,'supplier_id');
    }

    public function getShortAttribute()
    {
        return $this->attributes['short_name'];
    }
}
