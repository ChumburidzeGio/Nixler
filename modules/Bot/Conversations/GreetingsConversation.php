<?php

namespace Modules\Bot\Conversations;

use Illuminate\Foundation\Inspiring;
use Mpociot\BotMan\Answer;
use Mpociot\BotMan\Button;
use Mpociot\BotMan\Conversation;
use Mpociot\BotMan\Question;

class GreetingsConversation extends Conversation
{
    protected $firstname;
    
    protected $email;

    public function askFirstname()
    {
        $this->ask('Hello! What is your name?', function(Answer $answer) {
            // Save result
            $this->firstname = $answer->getText();

            $this->say('Nice to meet you '.$this->firstname);

            $this->askEmail();
        });
    }

    public function askEmail()
    {
        $this->ask('One more thing - what is your email?', function(Answer $answer) {
            // Save result
            $this->email = $answer->getText();

            $this->say('Great - that is all we need, '.$this->firstname);

        });
    }

    public function run()
    {
        // This will be called immediately
        $this->askFirstname();
    }

}