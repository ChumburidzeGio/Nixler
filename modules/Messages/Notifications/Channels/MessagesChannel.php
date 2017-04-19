<?php

namespace NotificationChannels\Trello;

use Illuminate\Support\Arr;
use Illuminate\Notifications\Notification;

class MessagesChannel
{
    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     *
     * @throws \NotificationChannels\Trello\Exceptions\InvalidConfiguration
     * @throws \NotificationChannels\Trello\Exceptions\CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification)
    {
        $params = $notification->toMessages($notifiable);

        $sender = array_get($params, 'from');

        $message = array_get($params, 'message');

        $thread = $sender->findOrCreateThreadWith($notifiable);

        $sender->messageIn($thread->id, $message);
    }
}