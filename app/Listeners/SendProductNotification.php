<?php

namespace App\Listeners;

use App\Notifications\ProductDeleted;
use App\Notifications\ProductUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendProductNotification
{
    /**
     * Handle the event.
     *
     * @param  $event
     * @return void
     */
    public function handle($event)
    {
        if($event->name == 'product:publihed') {
            $event->product->notify(new ProductUpdated);
        } 

        elseif($event->name == 'product:deleted') {
            $event->product->notify(new ProductDeleted);
        }
    }
}
