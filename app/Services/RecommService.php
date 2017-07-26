<?php

namespace App\Services;

use Recombee\RecommApi\Client;
use Recombee\RecommApi\Requests as Reqs;
use Recombee\RecommApi\Exceptions as Ex;
use App\Services\SystemService;
use App\Services\DeleteItem;

class RecommService {

	protected $client;

    /**
     * @return string
     */
    public function __construct()
    {	
    	$this->client = new Client(env('RECOMM_DB'), env('RECOMM_KEY'));
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
     * List empty items
     *
     * @return string
     */
    public function listEmptyItems()
    {   
        $items = new Reqs\ListItems([
            'filter' => "'currency' == null"
        ]);

        return $this->send($items);
    }


    /**
     * Remove empty items
     *
     * @return string
     */
    public function removeEmptyItems()
    {   
        $ids = $this->listEmptyItems();

        $requests = [];

        foreach ($ids as $id) {
            array_push($requests, new Reqs\DeleteItem($id));
        }

        return $this->send(new Reqs\Batch($requests));
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
    public function similar($id, $count, $params = [], $actor = null)
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
    public function addObject($content, $objectId)
    {
        return $this->send(new Reqs\SetItemValues($objectId, $content, ['cascadeCreate' => true]));
    }


    /**
     * Add activity to Keen
     *
     * @return string
     */
    public function removeProduct($id)
    {   
        return $this->send(new Reqs\DeleteItem($id));
    }


    /**
     * Add activity to Keen
     *
     * @return string
     */
    public function addUser($user)
    {   
        $setValues = new Reqs\SetUserValues($user->id, [
            'name' => $user->name,
            'currency' => $user->currency,
            'locale' => $user->locale,
            'gender' => $user->getMeta('gender') == 'male' ? 1 : 2,
            'headline' => $user->getMeta('headline'),
            //'age_range' => 4,
            'city_id' => $user->city_id,
        ], [
          'cascadeCreate' => true
        ]);

        return $this->send($setValues);
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
    public function removeProp($prop, $table)
    {   
        if($table == 'product') {

            $request = new Reqs\DeleteItemProperty($prop);

        } else {

            $request = new Reqs\DeleteUserProperty($prop);
            
        }

        return $this->send($request);
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
            'variants' => 'set',
            'tags' => 'set',
            'likes_count' => 'int',
            'owner' => 'string',
        ];

        $user_props = [
            'name' => 'string',
            'currency' => 'string',
            'locale' => 'string',
            'gender' => 'int',
            'age_range' => 'int',
            'city_id' => 'int',
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
        if (!app()->environment('development', 'production')) {
            return $default;
        }

        try {
            return $this->client->send($request);
        } catch(\Exception $e) {
            app(SystemService::class)->reportException($e);
            return $default;
        }
    }

}