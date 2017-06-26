<?php

namespace App\Emails;

use Illuminate\Mail\Mailable;
use Illuminate\Contracts\Queue\ShouldQueue;

class SoldNotificationEmail extends Mailable
{
    /**
     * @var string
     */
    public $url;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('orders.emails.sold');
    }
}
