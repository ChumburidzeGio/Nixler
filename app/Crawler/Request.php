<?php

namespace App\Crawler;

use GuzzleHttp\Client as GuzzleHttp;

class Request {

	private $getProxy = 'https://gimmeproxy.com/api/getProxy?post=true&country=US&websites=google';

	protected $body;

    public function get()
    {
        return $this->body;
    }

    public function toArray($key = null)
    {
        $data = json_decode($this->body, 1);

        if(!is_null($key))
        {
        	return array_get($data, $key);
        }

        return $data;
    }

    public function request($type, $url, $options = [], $withProxy = false)
    {
        $this->body = retry(3, function () use ($type, $url, $options, $withProxy) {

        	$guzzle = app(GuzzleHttp::class);

	        if($withProxy)
	        {
	        	$options = array_merge($options, [
	            	"proxy" => $this->getProxy()
	        	]);
	        }

        	$request = $guzzle->request($type, $url, $options);

        	return $request->getBody();

        }, 10);

        return $this;
    }

    private function getProxy()
    {
        $proxies = config('crawler.proxies');

        if(!is_array($proxies))
        {
        	$proxies = [];

        	for ($i=0; $i < 3; $i++) 
        	{ 
        		$proxies[] = (new self)->request('GET', $this->getProxy)->toArray('ipPort');
        	}

        	config([
	        	'crawler.proxies' => $proxies
	        ]);
        }

        shuffle($proxies);

        return array_first($proxies);
    }

}