<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccountBalance extends Model
{
    use HasFactory;

    protected $casts = [
        'info' => 'array',
    ];

}
