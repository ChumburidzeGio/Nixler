<?php

namespace App\Services;

use GuzzleHttp\Client as GuzzleHttp;

class GeonamesService {

    /**
     * @return string
     */
    public function get($id)
    {	
        $endpoint = 'http://api.geonames.org/getJSON';

        $params = http_build_query([
            'geonameId' => $id,
            'username' => collect(['nixler', 'nixe', 'nixlerinfo'])->random()
        ]);

    	$request = app(GuzzleHttp::class)->request('GET', "{$endpoint}?{$params}");

        return json_decode($request->getBody());
    }


    /**
     * @return string
     */
    public function getName($id, $locale, $response = null)
    {	
        $response = $response ? : $this->get($id);

        $name = collect($response->alternateNames)->where('lang', $locale)->last();

        return isset($name->name) ? $name->name : $response->name;
    }

}