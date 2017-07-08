<?php

namespace App\Listeners;

use App\Notifications\LikedProduct;
use App\Events\ProductLiked;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendLikedNotification
{
    /**
     * Handle the event.
     *
     * @param  ProductLiked  $event
     * @return void
     */
    public function handle(ProductLiked $event)
    {
        if($event->product->owner_id == $event->actor->id) {
            return false;
        }
        
        $event->product->owner->notify(
            new LikedProduct($event->actor, $event->product)
        );
    }
}
