<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Emails\VerificationMail;
use Illuminate\Notifications\Messages\NexmoMessage;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;

class SendVerificationCode extends Notification
{
    use Queueable;

    protected $code;

    protected $via;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($code, $via)
    {
        $this->code = $code;

        $this->via = ($via == 'sms') ? ['nexmo'] : ['mail'];
    }


    /**
     * Get the notification channels.
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        if (app()->environment('local')) {
            return [TelegramChannel::class];
        }
       
        return $this->via;
    }


    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {

        return (new VerificationMail($this->code))->to($notifiable->address);
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

    public function toTelegram($notifiable)
    {
        return TelegramMessage::create()
            ->to('-213889926')
            ->content('Verification code: '.$this->code);
    }

}