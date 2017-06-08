<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Channels\MessagesChannel;

class SomeoneFollowedYou extends Notification
{
    use Queueable;

    protected $actor;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($actor)
    {
        $this->actor = $actor;
    }


    /**
     * Get the notification channels.
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return [MessagesChannel::class];
    }


    /**
     * Get the Internal Messenger representation of the notification.
     *
     * @param  mixed  $notifiable
     */
    public function toMessages($notifiable)
    {
        return [
            'from' => 1,
            'to' => $notifiable->id,
            'message' => __("[:name](:url) started following you.", ['name' => $this->actor->name, 'url' => $this->actor->link()])
        ];

    }
}