<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $dates = ['updated_at', 'date_period', 'date_start'];


    public function client(){
        return $this->belongsTo(Client::class);
    }

    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }
}
