<?php

namespace App\Console\Commands;

use Goutte\Client;
use App\Crawler\Crawler;
use App\Entities\Product;
use Illuminate\Console\Command;

class CrawlSet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawl:set';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawl the webpage with set of products';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $links = [];

        $userId = $this->ask('Who to act instead?');

        auth()->loginUsingId($userId);

        while ($link = $this->ask('What is the url of webpage?')) {
             $links[] = $link;
        }

        foreach ($links as $link) {
            $this->crawl($link);
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function crawl($url)
    {
        $commander = $this;

        $crawler = app(Client::class)->request('GET', $url);

        $pattern = app(Crawler::class)->findPattern($url);
        
        $links = app($pattern)->parse($crawler)->detectProductsOnPage();
        
        Product::withoutSyncingToSearch(function () use ($links, $commander) {
            app(Crawler::class)->bulk($links, $commander);
        });
    }

}