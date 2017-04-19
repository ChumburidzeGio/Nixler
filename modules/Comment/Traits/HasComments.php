<?php

namespace Modules\Comment\Traits;

use Modules\Comment\Entities\Comment;

trait HasComments {
    
    /**
     * Show comments for model
     */
    public function comments()
    {
        return $this->hasMany(Comment::class, 'target_id', 'id');
    }
    
    /**
     * Show comments for model
     */
    public function comment($text, $rate = null, $actor = null)
    {
        if(is_null($actor)){
        	$actor = auth()->user();
        }
        return $this->comments()->create([
            'user_id' => $actor->id,
            'target_type' => $this->getTable(),
            'text' => $text,
            'rate' => $rate
        ]);
    }
    
}