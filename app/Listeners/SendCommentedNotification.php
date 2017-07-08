<?php

namespace App\Listeners;

use App\Notifications\CommentedOnProduct;
use App\Events\ProductCommented;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendLikedNotification
{
    /**
     * Handle the event.
     *
     * @param  ProductCommented  $event
     * @return void
     */
    public function handle(ProductCommented $event)
    {
        if($event->product->owner_id == $event->actor->id) {
            return false;
        }
        
        $event->product->owner->notify(
            new CommentedOnProduct($event->actor, $event->product)
        );
    }
}
