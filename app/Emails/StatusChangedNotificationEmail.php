<?php

namespace App\Emails;

use Illuminate\Mail\Mailable;
use Illuminate\Contracts\Queue\ShouldQueue;

class StatusChangedNotificationEmail extends Mailable
{
    /**
     * @var string
     */
    public $url;

    /**
     * @var string
     */
    public $status;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($url, $status)
    {
        $this->url = $url;

        $this->status = $status;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('orders.emails.status_changed');
    }
}
