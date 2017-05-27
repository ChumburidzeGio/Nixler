<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
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

        if(!$params) {
            return false;
        }
        
        Storage::disk('google')->put(
            array_get($params, 'name'),
            file_get_contents(array_get($params, 'path'))
        );
    }
}