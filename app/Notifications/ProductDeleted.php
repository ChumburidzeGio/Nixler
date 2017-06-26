<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
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
        return [RecommChannel::class];
    }
    
    /**
     * Get the Recommend representation of the notification.
     *
     * @param  mixed  $notifiable
     */
    public function toRecomm($product, $service)
    {
        return $service->removeProduct($product->id);
    }
}
