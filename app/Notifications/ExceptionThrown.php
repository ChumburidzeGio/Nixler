<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Support\Facades\Request;
use Exception;

class ExceptionThrown extends Notification
{
    use Queueable;

    protected $exception;

    public function __construct(Exception $exception) {
        $this->exception = $exception;
    }

    public function via()
    {
        return ['slack'];
    }

    public function toSlack(): SlackMessage
    {
        $e = $this->exception;

        return (new SlackMessage)->error()->attachment(function ($attachment) use ($e) {

            $title = $this->getTitle($e);

            $trace = $e->getTraceAsString();

            while ($e->getPrevious()) {

                $errorCount = isset($errorCount) ? $errorCount++ : 2;

                $e = $e->getPrevious();

                $title .= "\n{$errorCount}. {$this->getTitle($e)}";

                $trace = $e->getTraceAsString();

                $title = (!starts_with($title, '1.')) ? $title = "1. {$title}" : $title;
            }

            $attachment->title($title)->content($trace)->fields([
                'Request url' => Request::method().':'.Request::url(),
                'Request param' => json_encode(Request::all()),
                'Environment' => config('app.env'),
                'User' => auth()->check() ? "#{auth()->id()} ({auth()->user()->name})" : 'guest',
            ]);

        });
    }

    public function getTitle($e){
        return get_class($e).' in '.$e->getFile().' line '.$e->getLine().' with message '.$e->getMessage();
    }

}
