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
        $repository = app(UserRepository::class);
        
        if(starts_with($event->name, 'user')) {
            $repository->updateStats($event->user);
        }

        elseif(starts_with($event->name, 'order')) {
            $repository->updateStats($event->order->merchant);
        }

        elseif(starts_with($event->name, 'product')) {
            $repository->updateStats($event->actor);
        }
    }
}
