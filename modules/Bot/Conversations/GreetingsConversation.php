<?php

namespace Modules\Bot\Conversations;

use Illuminate\Foundation\Inspiring;
use Mpociot\BotMan\Answer;
use Mpociot\BotMan\Button;
use Mpociot\BotMan\Conversation;
use Mpociot\BotMan\Question;

class GreetingsConversation extends Conversation
{

    /**
     * First question
     */
    public function askReason()
    {
        $question = Question::create("Huh - you woke me up. So I have couple suggestions")
            ->fallback('Unable to ask question')
            ->callbackId('ask_reason')
            ->addButtons([
                Button::create('Have sex')->value('sex'),
                Button::create('Read a book')->value('quote'),
                Button::create('Sleep')->value('quote'),
                Button::create('Code')->value('quote'),
                Button::create('Contact investors')->value('quote'),
            ]);

        return $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                if ($answer->getValue() === 'joke') {
                    $joke = json_decode(file_get_contents('http://api.icndb.com/jokes/random'));
                    $this->say($joke->value->joke);
                } elseif ($answer->getValue() === 'sex') {
                    $this->say('Attractive ...');
                } else {
                    $this->say(Inspiring::quote());
                }
            }
        });
    }

    /**
     * Start the conversation
     */
    public function run()
    {
        $this->askReason();
    }

}