<?php

namespace App\Listeners;

use App\Notifications\ProductDeleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DeleteProductRelationships
{
    /**
     * Handle the event.
     *
     * @param  $event
     * @return void
     */
    public function handle($event)
    {
        $event->product->comments()->delete();
        
        $event->product->media()->delete();

        $event->product->meta()->delete();

        $event->product->activities()->delete();

        $event->product->source()->delete();
    }
}
