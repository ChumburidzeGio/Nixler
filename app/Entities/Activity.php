<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Entities\User;
use App\Services\RecommService;

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
    
    /**
     *  Relationships
     */
    public function push()
    {
        $activity = $this;

        (new RecommService)->push($activity->actor, $activity->object, $activity->verb, $activity->created_at->format('c'));

    }
    
    /**
     *  Relationships
     */
    public function remove()
    {
        $activity = $this;

        (new RecommService)->remove($activity->actor, $activity->object, $activity->verb, $activity->created_at->format('c'));
        
    }
}