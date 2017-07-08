<?php

namespace App\Listeners;

use App\Notifications\SomeoneFollowedYou;
use App\Events\UserFollowed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendFollowedNotification
{
    /**
     * Handle the event.
     *
     * @param  UserFollowed  $event
     * @return void
     */
    public function handle(UserFollowed $event)
    {
        $event->user->notify(new SomeoneFollowedYou($event->actor));
    }
}
