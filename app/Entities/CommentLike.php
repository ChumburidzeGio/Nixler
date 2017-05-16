<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class CommentLike extends Model
{
    public $table = 'comment_likes';
    
    protected $fillable  = [
        'user_id', 'comment_id'
    ];
}