<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Emails\VerificationMail;
use Illuminate\Notifications\Messages\NexmoMessage;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use App\Services\SystemService;

class SendVerificationCode extends Notification
{
    use Queueable;

    protected $code;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($code)
    {
        $this->code = $code;
    }


    /**
     * Get the notification channels.
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return method_exists($notifiable, 'getTable') ? ['nexmo'] : ['slack'];
    }


    /**
     * Get the Nexmo / SMS representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return NexmoMessage
     */
    public function toNexmo($notifiable)
    {
        return (new NexmoMessage)
                    ->content('Verification code: '.$this->code);
    }
    
    /**
     * Get the Slack representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return SlackMessage
     */
    public function toSlack(): SlackMessage
    {
        return (new SlackMessage)->content('Verification code for '.auth()->user()->name.': '.$this->code);
    }

}