<?php

namespace Modules\Stream\Entities;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentTaggable\Models\Tag;

class UserTag extends Model
{

    public $table = 'user_tags';
    
    protected $fillable  = [
        'user_id', 'tag_id', 'score'
    ];

    public function tag()
    {   
        return $this->hasOne(Tag::class, 'tag_id', 'tag_id');
    }

}