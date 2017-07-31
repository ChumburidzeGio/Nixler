<?php

namespace App\Crawler;

use Goutte\Client;
use App\Entities\ProductSource;
use App\Services\SystemService;
use App\Crawler\BasePattern;
use App\Crawler\Patterns\LuteciaGe;
use App\Crawler\Patterns\BeGe;
use App\Crawler\Patterns\DealsGe;
use App\Crawler\Patterns\Zalando;
use App\Repositories\ProductRepository;

class Crawler {

    private $client;

    public function __construct(Client $client) {

    	$this->client = $client;

    }

    /**
     * @param $name string
     * @return App\Entities\City
     */
    function get($url)
    {
        $crawler = $this->client->request('GET', $url);

        $pattern = app($this->findPattern($url))->parse($crawler);

        return $pattern;
    }

    /**
     * @param $name string
     * @return App\Entities\City
     */
    function all($url, $commander)
    {
        $crawler = $this->client->request('GET', $url);

        $links = app($this->findPattern($url))->parse($crawler)->detectProductsOnPage();

        $this->bulk($links, $commander);
    }

    /**
     * @param $name string
     * @return App\Entities\City
     */
    function updateAll($commander)
    {
        $startDate = strtotime('now');

        $merchants = ProductSource::groupBy('merchant_id')->pluck('merchant_id')->map(function($merchantId) use ($commander) {

            auth()->loginUsingId($merchantId);

            $query = ProductSource::where('merchant_id', $merchantId);

            $page = 1;

            $count = 10;

            do {

                $chunkStartDate = strtotime('now');

                $results = $query->forPage($page, $count)->pluck('source');

                $links = $results->filter(function($link) {

                    return ('zalando.it' == $this->getRootDomain($link));

                });

                $countResults = $links->count();

                if ($countResults == 0) {
                    break;
                }

                $commander->info("Updating {$countResults} links from chunk {$page}");

                $this->bulk($links->toArray(), $commander);

                $page++;

                sleep(10);

                $secondsUsed = strtotime('now') - $chunkStartDate;

                $commander->comment("Updated {$countResults} links from chunk {$page} for {$secondsUsed} seconds");

            } while ($countResults == $count);

        });

        $commander->info('Proccessed for '.(strtotime('now') - $startDate).' seconds');
    }

    /**
     * @param $name string
     * @return App\Entities\City
     */
    function bulk($links, $commander)
    {
        $repository = app(ProductRepository::class);

        foreach ($links as $key => $link) {

            $key++;
            
            try {

                $commander->comment($key.'. Crawling '.$link);

                $product = $repository->import($link);

                if($product && $product->category_id) {

                    $product = $repository->publish($product, auth()->user());
                }

                $commander->info($key.'. '.($product ? 'Success - ' : 'Skipped - ').$link);

            } catch (\Exception $e) {

                $commander->error('Rejected - '.$link." - ".$e->getMessage());

            }

        }
    }

    /**
     * @param $name string
     * @return App\Entities\City
     */
    function findPattern($url)
    {
        $domain = $this->getRootDomain($url);

        $patterns = [
            'lutecia.ge' => LuteciaGe::class,
            'be.ge' => BeGe::class,
            'deals.ge' => DealsGe::class,
            'zalando.it' => Zalando::class,
            'zalando.co.uk' => Zalando::class,
        ];

        $default = BasePattern::class;

        return array_get($patterns, $domain, $default);
    }

    /**
     * @param $name string
     * @return App\Entities\City
     */
    function getRootDomain($url)
    {
        $domain = array_get(parse_url($url), 'host');

        $original = $domain = strtolower($domain);

        if (filter_var($domain, FILTER_VALIDATE_IP)) { return $domain; }

        $arr = array_slice(array_filter(explode('.', $domain, 4), function($value){
            return $value !== 'www';
        }), 0); //rebuild array indexes

            if (count($arr) > 2)
            {
                $count = count($arr);
                $_sub = explode('.', $count === 4 ? $arr[3] : $arr[2]);

            if (count($_sub) === 2) // two level TLD
            {
                $removed = array_shift($arr);
                if ($count === 4) // got a subdomain acting as a domain
                {
                    $removed = array_shift($arr);
                }
            }
            elseif (count($_sub) === 1) // one level TLD
            {
                $removed = array_shift($arr); //remove the subdomain

                if (strlen($_sub[0]) === 2 && $count === 3) // TLD domain must be 2 letters
                {
                    array_unshift($arr, $removed);
                }
                else
                {
                    // non country TLD according to IANA
                    $tlds = array(
                        'aero', 'arpa', 'asia', 'biz', 'cat', 'com', 'coop', 'edu',
                        'gov', 'info', 'jobs', 'mil', 'mobi', 'museum', 'name', 'net',
                        'org', 'post', 'pro', 'tel', 'travel', 'xxx',
                    );

                    if (count($arr) > 2 && in_array($_sub[0], $tlds) !== false) //special TLD don't have a country
                    {
                        array_shift($arr);
                    }
                }
            }
            else // more than 3 levels, something is wrong
            {
                for ($i = count($_sub); $i > 1; $i--)
                {
                    $removed = array_shift($arr);
                }
            }
        }
        elseif (count($arr) === 2)
        {
            $arr0 = array_shift($arr);

            if (strpos(join('.', $arr), '.') === false
                && in_array($arr[0], array('localhost','test','invalid')) === false) // not a reserved domain
            {
                array_unshift($arr, $arr0);
            }
        }

        return join('.', $arr);
    }

}