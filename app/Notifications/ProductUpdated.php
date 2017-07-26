<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Channels\RecommChannel;
use App\Entities\ProductVariant;
use App\Entities\ProductTag;

class ProductUpdated extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

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
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'price' => floatval($notifiable->price),
            'title' => $notifiable->title,
            'user_id' => $notifiable->owner_id,
            'category_id' => $notifiable->category_id,
            'in_stock' => intval($notifiable->in_stock),
            'currency' => $notifiable->currency,
            'description' => $notifiable->description,
            'variants' => ProductVariant::where('product_id', $notifiable->id)->get()->pluck('name')->toArray(),
            'tags' => ProductTag::where('product_id', $notifiable->id)->get()->pluck('name')->toArray(),
            'likes_count' => $notifiable->likes_count,
            'owner' => $owner->name,
        ];
    }
    
    /**
     * Get the Recommend representation of the notification.
     *
     * @param  mixed  $notifiable
     */
    public function toRecomm($notifiable, $service)
    {
        $data = $this->toArray($notifiable);

        return $service->addObject($data, $notifiable->id);
    }
}
