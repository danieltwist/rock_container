<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelegramUpdate extends Model
{
    use HasFactory;

    protected $casts = [
        'object' => 'array',
        'info' => 'array'
    ];

    protected $guarded = [];

    public function answer(){
        return $this->belongsTo(TelegramUpdate::class, 'answer_id');
    }
}
