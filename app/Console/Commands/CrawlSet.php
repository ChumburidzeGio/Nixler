<?php

namespace App\Console\Commands;

use Goutte\Client;
use App\Crawler\Crawler;
use Illuminate\Console\Command;
use App\Services\SystemService;
use App\Entities\ProductSource;
use App\Repositories\ProductRepository;

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

        array_map(function($link) {

            $pattern = app(Crawler::class)->findPattern($link);

            $productLinks = app($pattern)->detectProductsOnPage($link);

            array_map(function($link) {

                $this->info($link);

                app('db')->transaction(function () use ($link) 
                {
                    app(ProductRepository::class)->import($link);
                });

            }, $productLinks);

        }, $links);
    }

}