<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use App\Services\AlgoliaService;

class AlgoliaChannel
{
    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     */
    public function send($notifiable, Notification $notification)
    {
        $service = app(AlgoliaService::class);

        $notification->toAlgolia($notifiable, $service);
    }
}