<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\Mediable;

class Comment extends Model
{
    use Mediable;
    
    public $table = 'comments';
    
    protected $fillable  = [
        'user_id',  'target_id', 'text', 'target_type', 'media_id'
    ];

    protected $with = ['author'];

    /**
     * Show comments for model
     */
    public function author()
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id', 'id');
    }

}