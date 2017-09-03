<?php

namespace App\Services;

use App\Notifications\ExceptionThrown;
use Bugsnag\BugsnagLaravel\Facades\Bugsnag;
use Exception, Debugbar;

class SystemService
{
    public function notify($notifcation) 
    {
        app(\App\Notifications\Notifiables\Notifiable::class)->notify($notifcation);
    }

    public function reportException($ex) 
    {
        Bugsnag::notifyException($ex);
    }
}