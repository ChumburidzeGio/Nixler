<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    public $table = 'collections';
    
    protected $fillable  = [
        'user_id', 'name', 'description', 'media_id'
    ];

    /**
     * Relationship with owner
     *
     * @return collection
     */
    public function owner()
    {   
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id', 'id');
    }

}