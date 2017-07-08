<?php

namespace App\Listeners;

use App\Repositories\UserRepository;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateUserStats
{
    /**
     * Handle the event.
     *
     * @param  UserFollowed  $event
     * @return void
     */
    public function handle($event)
    {
        if(starts_with($event->name, 'user')) {
            app(UserRepository::class)->updateStats($event->user);
        }

        elseif(starts_with($event->name, 'order')) {
            app(UserRepository::class)->updateStats($event->order->merchant);
        }

        elseif(starts_with($event->name, 'product')) {
            app(UserRepository::class)->updateStats($event->actor);
        }
    }
}
