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
        if($event->name == 'user:followed') {
            
            app(UserRepository::class)->streamPushBySource($event->user, $event->actor);

        }
        
        elseif($event->name == 'user:unfollowed') {

            app(UserRepository::class)->streamRemoveBySource($event->user, $event->actor);

        }
    }
}
