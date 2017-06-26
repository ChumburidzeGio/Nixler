<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    public $table = 'collections';
    
    protected $fillable  = [
        'user_id'
    ];

}