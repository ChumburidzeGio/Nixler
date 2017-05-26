<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use App\Entities\User;

class MessagesChannel
{
    /**
     * Notification channel to send message inside platform.
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

        $messagable = array_get($params, 'to') ? : $notifiable->id;

        $sender = array_get($params, 'from');

        $message = array_get($params, 'message');

        $users = User::whereIn('id', [$messagable, $sender])->get();

        foreach ($users as $user) {

            if(is_int($sender) && $user->id == $sender){
                $sender = $user;
            }

            if(is_int($messagable) && $user->id == $messagable){
                $messagable = $user;
            }
        }

        $thread = $sender->findOrCreateThreadWith($messagable);

        $sender->messageIn($thread->id, $message);
    }
}
