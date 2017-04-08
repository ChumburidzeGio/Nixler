<?php

use Modules\Bot\Http\Controllers\BotController;

$botman = resolve('botman');

$botman->hears('Start conversation', BotController::class.'@startConversation');

Route::match(['get', 'post'], '/bot', BotController::class.'@handle');