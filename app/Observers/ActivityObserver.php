<?php

namespace App\Observers;

use App\Entities\Activity;
use App\Services\RecommService;

class ActivityObserver
{
    /**
     * Listen to the Activity created event.
     *
     * @param  Activity  $activity
     * @return void
     */
    public function created(Activity $activity)
    {
        (new RecommService)->push($activity->actor, $activity->object, $activity->verb);
    }

    /**
     * Listen to the Activity deleting event.
     *
     * @param  Activity  $activity
     * @return void
     */
    public function deleting(Activity $activity)
    {
        (new RecommService)->remove($activity->actor, $activity->object, $activity->verb);
    }
}