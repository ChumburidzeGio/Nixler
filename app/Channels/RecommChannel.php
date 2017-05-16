<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use App\Services\RecommService;

class RecommChannel
{
    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     */
    public function send($notifiable, Notification $notification)
    {
        $service = app(RecommService::class);

        $notification->toRecomm($notifiable, $service);
    }
}