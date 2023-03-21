<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectComment extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'notify_user' => 'array'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function project(){
        return $this->belongsTo(Project::class);
    }

    public function answered_comment(){
        return $this->belongsTo(ProjectComment::class,'answer_to','id');
    }
}
