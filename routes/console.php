<?php

use App\Repositories\LocationRepository;
use App\Repositories\ProductRepository;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('countries:download {iso_code}', function ($code) {
    app(LocationRepository::class)->downloadCountry($code);
});

Artisan::command('crawler:page {uid} {url}', function ($uid, $url) {
	auth()->loginUsingId($uid);
    $crawler = app(\App\Crawler\Crawler::class)->all($url, $this);
});

Artisan::command('crawler:updateAll', function () {
    $crawler = app(\App\Crawler\Crawler::class)->updateAll($this);
});