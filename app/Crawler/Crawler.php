<?php

namespace App\Crawler;

use Goutte\Client;
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
        
        if(method_exists($pattern, 'isProduct') && !$pattern->isProduct()) {
            return null;
        }

        return $pattern;
    }

    /**
     * @param $name string
     * @return App\Entities\City
     */
    function all($url)
    {
        $repository = app(ProductRepository::class);

        $crawler = $this->client->request('GET', $url);

        $links = app($this->findPattern($url))->parse($crawler)->detectProductsOnPage();

        foreach ($links as $link) {

            $product = $repository->create();

            $product = $repository->import($link, $product->id);

            if($product) {
                $repository->publish($product, auth()->user());
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
    function getRootDomain($url, $debug = false)
    {
        $domain = array_get(parse_url($url), 'host');

        $original = $domain = strtolower($domain);

        if (filter_var($domain, FILTER_VALIDATE_IP)) { return $domain; }

        $debug ? print('<strong style="color:green">&raquo;</strong> Parsing: '.$original) : false;

        $arr = array_slice(array_filter(explode('.', $domain, 4), function($value){
            return $value !== 'www';
        }), 0); //rebuild array indexes

            if (count($arr) > 2)
            {
                $count = count($arr);
                $_sub = explode('.', $count === 4 ? $arr[3] : $arr[2]);

                $debug ? print(" (parts count: {$count})") : false;

            if (count($_sub) === 2) // two level TLD
            {
                $removed = array_shift($arr);
                if ($count === 4) // got a subdomain acting as a domain
                {
                    $removed = array_shift($arr);
                }
                $debug ? print("<br>\n" . '[*] Two level TLD: <strong>' . join('.', $_sub) . '</strong> ') : false;
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
                        'aero',
                        'arpa',
                        'asia',
                        'biz',
                        'cat',
                        'com',
                        'coop',
                        'edu',
                        'gov',
                        'info',
                        'jobs',
                        'mil',
                        'mobi',
                        'museum',
                        'name',
                        'net',
                        'org',
                        'post',
                        'pro',
                        'tel',
                        'travel',
                        'xxx',
                        );

                    if (count($arr) > 2 && in_array($_sub[0], $tlds) !== false) //special TLD don't have a country
                    {
                        array_shift($arr);
                    }
                }
                $debug ? print("<br>\n" .'[*] One level TLD: <strong>'.join('.', $_sub).'</strong> ') : false;
            }
            else // more than 3 levels, something is wrong
            {
                for ($i = count($_sub); $i > 1; $i--)
                {
                    $removed = array_shift($arr);
                }
                $debug ? print("<br>\n" . '[*] Three level TLD: <strong>' . join('.', $_sub) . '</strong> ') : false;
            }
        }
        elseif (count($arr) === 2)
        {
            $arr0 = array_shift($arr);

            if (strpos(join('.', $arr), '.') === false
                && in_array($arr[0], array('localhost','test','invalid')) === false) // not a reserved domain
            {
                $debug ? print("<br>\n" .'Seems invalid domain: <strong>'.join('.', $arr).'</strong> re-adding: <strong>'.$arr0.'</strong> ') : false;
                // seems invalid domain, restore it
                array_unshift($arr, $arr0);
            }
        }

        $debug ? print("<br>\n".'<strong style="color:gray">&laquo;</strong> Done parsing: <span style="color:red">' . $original . '</span> as <span style="color:blue">'. join('.', $arr) ."</span><br>\n") : false;

        return join('.', $arr);
    }

}