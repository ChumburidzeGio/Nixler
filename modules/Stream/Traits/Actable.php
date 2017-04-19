<?php

namespace Modules\Stream\Traits;

use Modules\Stream\Entities\Activity;

trait Actable {
    
    
    /**
     * Get all activities for model
     *
     * @return void
     */
    public function activities()
    {
    	return $this->hasMany(Activity::class, 'object');
    }
    
    /**
     * Get all activities for model by verb
     *
     * @return void
     */
    public function getActivities($verb)
    {
    	return $this->activities()->where('verb', $verb);
    }
    
    /**
     * Get all actors for model by verb
     *
     * @return void
     */
    public function getActivityActors($verb)
    {
    	$users_model = config('auth.providers.users.model');
    	$activities_table = (new Activity())->getTable();

    	return $this->belongsToMany($users_model, $activities_table, 'object', 'actor')->where('verb', $verb);
    }
    
    /**
     * Track activity on model
     *
     * @return void
     */
    public function trackActivity($verb)
    {
    	return Activity::create([
    		'actor' => auth()->id(),
            'verb' => $verb,
            'object' => $this->id,
            'object_type' => get_class($this)
    	]);
    }
    
    /**
     * Track activity on model
     *
     * @return void
     */
    public function toggleActivity($verb)
    {
    	$activity = Activity::firstOrCreate([
    		'actor' => auth()->id(),
            'verb' => $verb,
            'object' => $this->id,
            'object_type' => get_class($this)
    	]);

    	return $activity->wasRecentlyCreated ? true : !$activity->delete();
    }


}