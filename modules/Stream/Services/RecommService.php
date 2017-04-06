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
    	$this->client = new Client('nixler', 'DaLxkWAdffLwmeU2orojf6s2ua6gMLdubZh2RGvDdA8Q062mf9je9o5Rk7KgVGOE', 'https');
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

        $request = new Reqs\UserBasedRecommendation($actor, $count, $params);

        $request->setTimeout(1);

        try {
            return $this->client->send($request);
        } catch(\Exception $e) {
            return [];
        }
    }


    /**
     * Add activity to Keen
     *
     * @return string
     */
    public function addProduct($product)
    {   
        $addItem = new Reqs\AddItem($product->id);

        $setValues = new Reqs\SetItemValues($product->id, [
            'price' => $product->price,
            'title' => $product->title,
            'likes_count' => $product->likes_count,
            'updated_at' => $product->updated_at,
            'created_at' => $product->created_at,
            'user_id' => $product->owner_id
        ], [
          'cascadeCreate' => true
        ]);

        return $this->client->send(new Reqs\Batch([$addItem, $setValues]));
    }


    /**
     * Add activity to Keen
     *
     * @return string
     */
    public function removeProduct($product)
    {   
        return $this->client->send(new Reqs\DeleteItem($product->id));
    }


    /**
     * Add activity to Keen
     *
     * @return string
     */
    public function addProductProps()
    {	
        $this->client->send(new Reqs\ResetDatabase());
        
        $this->addProp('price', 'double');
        $this->addProp('title', 'string');
        $this->addProp('likes_count', 'int');
        $this->addProp('updated_at', 'timestamp');
        $this->addProp('created_at', 'timestamp');
        $this->addProp('user_id', 'int');
    }

}