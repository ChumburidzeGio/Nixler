<?php

namespace Modules\Stream\Traits;

use Modules\Stream\Entities\Feed;
use Modules\Product\Entities\Product;

trait HasStream {
    
    
    /**
     * Publish object in feeds who follow
     *
     * @return void
     */
    public function pushInStreams($object_id, $source)
    {
        $activities = [];
        $user_ids = $this->followers()->pluck('users.id');
        $user_ids[] = $this->id;

        foreach ($user_ids as $user_id) {
            $this->streamPushActivity($user_id, $object_id, $source);
        }

        return $activities;
    }
    
    /**
     * Publish object in feeds who follow
     *
     * @return void
     */
    public function pushInStream($object_ids, $source)
    {
        $user_id = $this->id;
        
        foreach ($object_ids as $object_id) {
            $this->streamPushActivity($user_id, $object_id, $source);
        }

    }
    
    /**
     * Publish object in feeds who follow
     *
     * @return void
     */
    public function streamPushActivity($user_id, $object_id, $source)
    {
        $activity = Feed::where('user_id', $user_id)->where('object_id', $object_id)->first();

        if(!$activity){
            $activity = Feed::create(compact('user_id', 'object_id', 'source'));
        }

        return $activity;
    }
    
    
    /**
     * Get stream by someone of model by type
     *
     * @return collection
     */
    public function stream()
    {   
        return $this->belongsToMany(Product::class, 'feeds', 'user_id', 'object_id');
    }
    
    
    /**
     * Unpublish object from feeds
     *
     * @return void
     */
    public function streamsRemove($object_id)
    {
        return Feed::where('object_id', $object_id)->delete();
    }
    
    
    /**
     * Unpublish object from feeds
     *
     * @return void
     */
    public function streamRemoveBySource($source)
    {
        return Feed::where('source', $source)->where('user_id', $this->id)->delete();
    }

}