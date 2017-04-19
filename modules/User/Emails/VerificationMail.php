<?php

namespace Modules\User\Emails;

use Illuminate\Mail\Mailable;
use Illuminate\Contracts\Queue\ShouldQueue;

class VerificationMail extends Mailable
{

    public $code;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($code)
    {
        $this->code = $code;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('user::emails.verification');
    }
}
