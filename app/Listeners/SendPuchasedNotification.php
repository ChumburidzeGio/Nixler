<?php

namespace App\Listeners;

use App\Notifications\ThankYouOnOrder;
use App\Events\OrderCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendPuchasedNotification
{
    /**
     * Handle the event.
     *
     * @param  OrderCreated  $event
     * @return void
     */
    public function handle(OrderCreated $event)
    {
        $event->actor->notify(new ThankYouOnOrder($event->order));
    }
}
