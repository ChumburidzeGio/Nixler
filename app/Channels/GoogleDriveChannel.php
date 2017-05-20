<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use App\Services\AlgoliaService;
use Storage;

class GoogleDriveChannel
{
    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     */
    public function send($notifiable, Notification $notification)
    {
        $params = $notification->toGoogleDrive($notifiable);

        Storage::disk('google')->put(
            array_get($params, 'name'),
            file_get_contents(array_get($params, 'path'))
        );
    }
}