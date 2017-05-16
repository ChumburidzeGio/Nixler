<?php

namespace App\Services;

use GuzzleHttp\Client as GuzzleHttp;

class RestcountriesService {

    /**
     * @return string
     */
    public function get($code)
    {	
        $endpoint = 'https://restcountries.eu/rest/v2/alpha/%s';

    	$request = app(GuzzleHttp::class)->request('GET', sprintf($endpoint, $code));

        return json_decode($request->getBody());
    }

}