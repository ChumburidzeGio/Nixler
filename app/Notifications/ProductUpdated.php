<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Channels\AlgoliaChannel;
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
        $owner = $notifiable->owner()->first();
        
        $city = $owner->city;

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
            'county' => $owner->country,
            'created_at' => intval($notifiable->created_at->format('U')),
            '_geoloc' => [
                'lat' => $city->lat,
                'lng' => $city->lng
            ],
        ];
    }

    /**
     * @return array
     */
    public function toRecomm($notifiable, $service)
    {
        $data = $this->toArray($notifiable);

        $data = array_merge($data, array_get($data, '_geoloc'));

        unset($data['_geoloc']);
        unset($data['county']);
        unset($data['created_at']);

        return $service->addObject($data, $notifiable->id);
    }
}
