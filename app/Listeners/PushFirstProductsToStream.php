<?php

namespace App\Listeners;

use App\Repositories\UserRepository;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class PushFirstProductsToStream
{
    /**
     * Handle the event.
     *
     * @param  UserFollowed  $event
     * @return void
     */
    public function handle($event)
    {
        $productIds = array_pluck(capsule('stream')->perPage(100)->relevant()->get()->items(), 'id');

        $event->user->pushInStream($productIds, 'pop');
    }
}
