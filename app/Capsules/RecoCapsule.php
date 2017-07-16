<?php

namespace App\Capsules;

use App\Entities\ProductCategory;
use App\Entities\Product;
use Illuminate\Support\Facades\Auth;
use App\Services\RecommService;

class RecoCapsule {
	
	private $model;
	
	private $boosters;
	
	private $object_id;
	
	private $user;

	private $service;

	private $type;

	protected $ids;

    /**
     * Construct the capsule
     *
     * @return void
     */
    public function __construct(Product $model, RecommService $service)
    {
    	$this->model = $model;

    	$this->boosters = ['0'];

    	$this->object_id = null;

    	$this->user = auth()->user();

    	$this->service = $service;

    	$this->ids = [];

    	$this->type = null;
    }

    /**
     * Create the statement
     * 
     * @return void
     */
    private function statement($statement, $max, $min = 0)
    {
    	return array_push($this->boosters, "+ (if {$statement} then {$max} else {$min})");
    }

    /**
     * Boost same category in similar products recomendations
     * 
     * @return void
     */
    private function boostSameCategory()
    {
    	$this->statement("'category_id' == context_item[\"category_id\"]", 20);
    }

    /**
     * Boost by description size, if it's longer than 50 characters give to products +7 points
     * 
     * @return void
     */
    private function boostDescriptionSize()
    {
    	$this->statement("size('description') > 50", 7);
    }

    /**
     * Boost products by likes count, 0.5 point for each like
     * 
     * @return void
     */
    private function boostByLikesCount()
    {
    	$this->statement("'likes_count' > 0", "('likes_count' * 0.5)");
    }

    /**
     * Boost products by people who user is following to, +20 points
     * 
     * @return void
     */
    private function boostByFollowings()
    {
    	if(!$this->user) {
    		return false;
    	}

    	$followings = $this->user->followings()->take(20)->pluck('follow_id')->implode(',');

    	$this->statement("'user_id' in {{$followings}}", 20);
    }

    /**
     * Boost products from category clothing and technics by 5 points
     * 
     * @return void
     */
    private function boostClothesAndTechnics()
    {
    	$this->statement("'category_id' < 30", 5);
    }

    /**
     * Get product suggestions for user
     * 
     * @return void
     */
    public function forUser($user = null)
    {
    	$this->user = $user ?? $this->user;

    	$this->object_id = $this->user->id;

    	$this->boostByFollowings();

    	$this->boostClothesAndTechnics();

    	$this->boostDescriptionSize();

    	$this->boostByLikesCount();

    	$this->request('recommendations', 50);

    	$ids_count = count($this->ids);

    	if($ids_count < 50) {

    		$new_ids = capsule('stream')->perPage(50)->popular()->keys()->items()->toArray();

    		$this->ids = array_merge($new_ids, $this->ids);

    	}

    	return $this;
    }

    /**
     * Get item based recommendations
     * 
     * @return void
     */
    public function forProduct($product, $user = null)
    {
    	$this->user = $user ?? $this->user;

    	$this->object_id = $product->id;

    	$this->boostSameCategory();

    	$this->boostDescriptionSize();

    	$this->boostByLikesCount();

    	$this->request('similar', 5);

    	$ids_count = count($this->ids);

    	if($ids_count < 5) {

    		$new_ids = capsule('stream')->whereSeller($product->owner_id)->perPage(5)->keys()->items()->toArray();

    		$this->ids = array_merge($new_ids, $this->ids);

    		$ids_count = count($this->ids);
    		
    	}

    	if($ids_count < 5) {

    		$new_ids = capsule('stream')->whereCategory($product->category_id)->perPage(5)->popular()->keys()->items()->toArray();

    		$this->ids = array_merge($new_ids, $this->ids);
    		
    	}

    	return $this;
    }

    /**
     * 
     * 
     * @return void
     */
    private function request($type, $amount)
    {
    	$boosters = implode(' ', $this->boosters);

    	$currency = config('app.currency');

    	$this->ids = $this->service->$type($this->object_id, $amount, [
            'filter' => "'currency' == \"{$currency}\"",
            'booster' => $boosters,
            'rotationRate' => '0.1'
        ]);

    	return $this;
    }

    /**
     *
     * 
     * @return void
     */
    public function get()
    {
    	return $this->ids;
    }

}