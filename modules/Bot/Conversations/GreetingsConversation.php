<?php

namespace Modules\Bot\Conversations;

use Illuminate\Foundation\Inspiring;
use Mpociot\BotMan\Answer;
use Mpociot\BotMan\Button;
use Mpociot\BotMan\Conversation;
use Mpociot\BotMan\Question;
use Mpociot\BotMan\GenericTemplate;

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

            $this->sendInfo();
        });
    }

    public function sendInfo()
    {
        $this->reply(GenericTemplate::create()
            ->addElements([
                Element::create('Nixler Documentation')
                    ->subtitle('All about Nixler')
                    ->image('http://botman.io/img/botman-body.png')
                    ->addButton(ElementButton::create('visit')->url('http://nixler.pl'))
                    ->addButton(ElementButton::create('tell me more')
                        ->payload('tellmemore')->type('postback')),
                Element::create('Nixler Bot Docs')
                    ->subtitle('This is the best way to start with NixlerBot')
                    ->image('http://botman.io/img/botman-body.png')
                    ->addButton(ElementButton::create('visit')
                        ->url('https://nixler.pl/NixlerBot')
                    )
            ])
        );
    }

    public function run()
    {
        // This will be called immediately
        $this->askFirstname();
    }

}