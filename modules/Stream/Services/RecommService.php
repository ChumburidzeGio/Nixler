<?php

namespace Modules\Stream\Services;

use KeenIO\Client\KeenIOClient;
use Recombee\RecommApi\Client;
use Recombee\RecommApi\Requests as Reqs;
use Recombee\RecommApi\Exceptions as Ex;
use Bugsnag;

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
            
        return $this->send($request);
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
            
        return $this->send($request);
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

        return $this->send(new Reqs\Batch($requests));
    }


    /**
     * Add activity to Keen
     *
     * @return string
     */
    public function similar($id, $count, $actor = null, $params = [])
    {   
        $params = $actor ? array_merge(['targetUserId' => $actor], $params) : $params;

        return $this->send(new Reqs\ItemBasedRecommendation($id, $count, $params));
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

        return $this->send($request);
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
            'user_id' => $product->owner_id,
            'category_id' => $product->category,
            'in_stock' => $product->in_stock,
            'currency' => $product->currency,
            'description' => $product->description
        ], [
          'cascadeCreate' => true
        ]);

        return $this->send(new Reqs\Batch([$addItem, $setValues]));
    }


    /**
     * Add activity to Keen
     *
     * @return string
     */
    public function removeProduct($product)
    {   
        return $this->send(new Reqs\DeleteItem($product->id));
    }


    /**
     * Add activity to Keen
     *
     * @return string
     */
    public function addUser($user)
    {   
        //$addUser = new Reqs\AddUser($user->id);

        $setValues = new Reqs\SetUserValues($user->id, [
            'currency' => $user->currency,
            'locale' => $user->locale,
            'gender' => $user->getMeta('gender'),
            'headline' => $user->getMeta('headline'),
            'age_range' => 4,
            'city_id' => 4,
            'income_lvl' => 2,
        ], [
          'cascadeCreate' => true
        ]);

        return $this->send(new Reqs\Batch([/*$addUser,*/ $setValues]));
    }


    /**
     * Add activity to Keen
     *
     * @return string
     */
    public function removeUser($user)
    {   
        return $this->send(new Reqs\DeleteUser($user->id));
    }


    /**
     * Add activity to Keen
     *
     * @return string
     */
    public function addProps()
    {   
        $requests = [];

        $product_props = [
            'price' => 'double',
            'title' => 'string',
            'user_id' => 'int',
            'category_id' => 'int',
            'in_stock' => 'int',
            'currency' => 'string',
            'description' => 'string',
        ];

        $user_props = [
            'currency' => 'string',
            'locale' => 'string',
            'gender' => 'int',
            'age_range' => 'int',
            'city_id' => 'int',
            'income_lvl' => 'int',
            'headline' => 'string',
        ];

        array_push($requests, new Reqs\ResetDatabase());
        
        foreach ($product_props as $name => $type) {
            array_push($requests, new Reqs\AddItemProperty($name, $type));
        }
        
        foreach ($user_props as $name => $type) {
            array_push($requests, new Reqs\AddUserProperty($name, $type));
        }

        return $this->send(new Reqs\Batch($requests));
    }


    /**
     * Add activity to Keen
     *
     * @return string
     */
    public function send($request, $default = [])
    {	
        try {
            return $this->client->send($request);
        } catch(\Exception $e) {
            Bugsnag::notifyException($e);
            return $default;
        }
    }

}