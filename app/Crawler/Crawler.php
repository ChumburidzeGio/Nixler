<?php

namespace App\Crawler;

use Goutte\Client;
use App\Entities\ProductSource;
use App\Services\UrlService;
use App\Crawler\Model;
use App\Crawler\BasePattern;
use App\Repositories\ProductRepository;
use App\Crawler\Patterns\LuteciaGe;
use App\Crawler\Patterns\BeGe;
use App\Crawler\Patterns\DealsGe;
use App\Crawler\Patterns\Zalando;
use App\Crawler\Patterns\Forever21;
use App\Crawler\Patterns\Aliexpress;
use App\Crawler\Patterns\Forever21Controller;

class Crawler {

    private $client;

    public function __construct(Client $client) {

    	$this->client = $client;

    }

    /**
     * @param $name string
     * @return App\Entities\City
     */
    function get($url, $param = null)
    {
        $pattern = app($this->findPattern($url))->parse($url);

        if(!is_null($param))
        {
            return app(Model::class)->setPattern($pattern->withParam($param));
        }

        if($pattern->getAvailableParams())
        {
            $models = [];

            foreach ($pattern->getAvailableParams() as $param) 
            {
                $models[] = app(Model::class)->setPattern($pattern->withParam($param));
            }

            return $models;
        }

        $model = (new Model)->setPattern($pattern);

        return $model;
    }

    /**
     * @param $name string
     * @return App\Entities\City
     */
    function findPattern($url)
    {
        $domain = app(UrlService::class)->parse($url)->getRootDomain();

        $patterns = [
            'lutecia.ge' => LuteciaGe::class,
            'be.ge' => BeGe::class,
            'deals.ge' => DealsGe::class,
            'zalando.it' => Zalando::class,
            'zalando.co.uk' => Zalando::class,
            'forever21.com' => Forever21::class,
            'aliexpress.com' => Aliexpress::class,
        ];

        $default = BasePattern::class;

        return array_get($patterns, $domain, $default);
    }

}