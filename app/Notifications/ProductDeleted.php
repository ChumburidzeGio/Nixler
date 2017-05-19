<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Channels\AlgoliaChannel;
use App\Channels\RecommChannel;

class ProductDeleted extends Notification
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [AlgoliaChannel::class, RecommChannel::class];
    }

    /**
     * @return array
     */
    public function toAlgolia($product, $service)
    {
        return $service->deleteObject('products', $notifiable->id);
    }

    /**
     * @return array
     */
    public function toRecomm($product, $service)
    {
        return $service->removeProduct($product->id);
    }
}
