<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Feed extends Model
{
    public $table = 'feeds';
    
    protected $fillable  = [
        'user_id', 'object_id', 'source'
    ];
}