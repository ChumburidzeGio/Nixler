<?php

namespace Modules\Bot\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Bot\Conversations\GreetingsConversation;
use Mpociot\BotMan\BotMan;

class BotController extends Controller
{
    /**
     * Place your BotMan logic here.
     */
    public function handle()
    {
        $botman = app('botman');

        //$botman->verifyServices(env('TOKEN_VERIFY'));

        // Simple respond method
        $botman->hears('Hello', function (BotMan $bot) {
            $bot->reply('Hi there :)');
        });

        $botman->hears('(Hey|Hello|Hi)', function (BotMan $bot) {
            $bot->startConversation(new GreetingsConversation());
        });

        $botman->fallback(function($bot) {
            $bot->reply('Sorry, I did not understand these commands.');
        });

        $botman->listen();
    }

}
