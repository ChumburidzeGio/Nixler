<?php

namespace Modules\Stream\Services;

use KeenIO\Client\KeenIOClient;
use Recombee\RecommApi\Client;
use Recombee\RecommApi\Requests as Reqs;
use Recombee\RecommApi\Exceptions as Ex;

class RecommService {

	protected $client;

    /**
     * @return string
     */
    public function __construct()
    {	
    	$this->client = new Client('nixler', 'DaLxkWAdffLwmeU2orojf6s2ua6gMLdubZh2RGvDdA8Q062mf9je9o5Rk7KgVGOE');
    }


    /**
     * Add activity to Keen
     *
     * @return string
     */
    public function addProp($name, $type)
    {   
        $this->client->send(new Reqs\AddItemProperty($name, $type));
    }


    /**
     * Add activity to Keen
     *
     * @return string
     */
    public function push($actor, $object, $verb, $timestamp)
    {   
        $params = ['cascadeCreate' => true, 'timestamp' => $timestamp];

        if ($verb == 'product:liked') {
            $request = new Reqs\AddBookmark($actor, $object, $params);
        } elseif ($verb == 'product:purchased') {
            $request = new Reqs\AddPurchase($actor, $object, $params);
        } elseif ($verb == 'product:viewed') {
            $request = new Reqs\AddDetailView($actor, $object, $params);
        }
            
        return $this->client->send($request);
    }


    /**
     * Add activity to Keen
     *
     * @return string
     */
    public function remove($actor, $object, $verb, $timestamp)
    {   
        $params = ['timestamp' => $timestamp];

        if ($verb == 'product:liked') {
            $request = new Reqs\DeleteBookmark($actor, $object, $params);
        } elseif ($verb == 'product:purchased') {
            $request = new Reqs\DeletePurchase($actor, $object, $params);
        } elseif ($verb == 'product:viewed') {
            $request = new Reqs\DeleteDetailView($actor, $object, $params);
        }
            
        return $this->client->send($request);
    }


    /**
     * Add activity to Keen
     *
     * @return string
     */
    public function updateItem($data)
    {   
        $requests = [];

        foreach ($data as $id => $item) {
            array_push($requests, new Reqs\SetItemValues($id, $item, ['cascadeCreate' => true]));
        }

        return $this->client->send(new Reqs\Batch($requests));
    }


    /**
     * Add activity to Keen
     *
     * @return string
     */
    public function similar($id, $count, $actor = null, $params = [])
    {   
        $params = $actor ? array_merge(['targetUserId' => $actor], $params) : $params;

        return $this->client->send(new Reqs\ItemBasedRecommendation($id, $count, $params));
    }


    /**
     * Add activity to Keen
     *
     * @return string
     */
    public function recommendations($actor, $count, $params = [])
    {	
        $params = array_merge($params, [
            'allowNonexistent' => 1
        ]);

        return $this->client->send(new Reqs\UserBasedRecommendation($actor, $count, $params));
    }

}