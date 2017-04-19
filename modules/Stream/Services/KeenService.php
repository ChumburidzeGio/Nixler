<?php

namespace Modules\Stream\Services;

use KeenIO\Client\KeenIOClient;

class KeenService {

	protected $client;

    /**
     * @return string
     */
    public function __construct()
    {	
    	$this->client = KeenIOClient::factory(config('services.keen'));
    }


    /**
     * Add activity to Keen
     *
     * @return string
     */
    public function push($verb, $data)
    {	
    	$this->client->addEvents($activities);
    }

}