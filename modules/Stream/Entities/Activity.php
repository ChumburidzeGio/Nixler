<?php

namespace Modules\Stream\Entities;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentTaggable\Models\Tag;
use Modules\User\Entities\User;

class Activity extends Model
{

    public $table = 'activities';
    
    protected $fillable  = [
        'actor', 'verb', 'object', 'object_type', 'new'
    ];

    
    /**
     *  Relationships
     */
    public function mactor()
    {   
        return $this->hasOne(User::class,'id', 'actor');
    }

    
    /**
     *  Relationships
     */
    public function mobject()
    {
        return $this->hasOne($this->attributes['object_type'], 'id', 'object');
    }


}