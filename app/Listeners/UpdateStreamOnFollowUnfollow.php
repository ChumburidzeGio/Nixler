<?php

namespace App\Listeners;

use App\Repositories\UserRepository;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateStreamOnFollowUnfollow
{
    /**
     * Handle the event.
     *
     * @param  $event
     * @return void
     */
    public function handle($event)
    {
        $repository = app(UserRepository::class);

        if($event->name == 'user:followed') {
            $repository->streamPushBySource($event->user, $event->actor);
        }
        
        elseif($event->name == 'user:unfollowed') {
            $repository->streamRemoveBySource($event->user, $event->actor);
        }
    }
}
