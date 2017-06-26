<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    public $table = 'thread_participants';
    
    protected $fillable  = [
        'user_id', 'thread_id'
    ];
}