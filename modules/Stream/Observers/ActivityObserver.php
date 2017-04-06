<?php

namespace Modules\Stream\Observers;

use Modules\Stream\Entities\Activity;
use Modules\Stream\Services\RecommService;

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
        (new RecommService)->push($activity->actor, $activity->object, $activity->verb, $activity->created_at->format('c'));
    }

    /**
     * Listen to the Activity deleting event.
     *
     * @param  Activity  $activity
     * @return void
     */
    public function deleting(Activity $activity)
    {
        (new RecommService)->remove($activity->actor, $activity->object, $activity->verb, $activity->created_at->format('c'));
    }
}