<?php

namespace App\Capsules;

use App\Entities\Collection;

class CollectionsCapsule {
	
	private $model;

	protected $items;

    /**
     * Construct the capsule
     *
     * @return void
     */
    public function __construct(Collection $model)
    {
    	$this->model = $model->from('collections as c')->select('c.id', 'c.name', 'c.media_id');
    }

    /**
     * Filter by category
     *
     * @return void
     */
    public function whereCategory(int $id)
    {
    	$this->model = $this->model->where('category_id', $id);

    	return $this;
    }

    /**
     * Filter by owner
     *
     * @return void
     */
    public function whereOwner(int $id)
    {
    	$this->model = $this->model->where('user_id', $id);

    	return $this;
    }

    /**
     * Filter by privacy
     *
     * @return void
     */
    public function wherePrivate()
    {
    	$this->model = $this->model->where('is_private', true);

    	return $this;
    }

    /**
     * OrderBy Latest
     *
     * @return void
     */
    public function orderByTime()
    {
    	$this->model = $this->model->latest();

    	return $this;
    }

    /**
     * Select user name with join clause
     *
     * @return void
     */
    private function addUserData()
    {
    	$this->model = $this->model->leftJoin('users as u', 'c.user_id', '=', 'u.id')->addSelect('u.name as unm', 'u.id as uid');
    }

    /**
     * Execute
     *
     * @return void
     */
    public function get()
    {
    	$this->addUserData();

    	$collections = $this->model->get();

    	$this->items = $this->transform($collections);

    	return $this;
    }

    /**
     * Execute
     *
     * @return void
     */
    public function items()
    {
    	return $this->items;
    }

    /**
     * Transform
     *
     * @return void
     */
    public function transform($collections)
    {
        return $collections->map(function($item){

            return [
                'url' => route('collections.show', ['id' => $item->id]),
                'name' => $item->name,
                'owner_name' => $item->unm,
                'owner_photo' => route('avatar', ['id' => $item->uid, 'place' => 'nav']),
                'photo' => media($item->media_id, 'collection', 'stream')
            ];

        });
    }

    /**
     * Return capsule as array
     *
     * @return array
     */
    public function toArray()
    {
        $items = $this->items();

        return compact('items');
    }

    /**
     * Return capsule as array
     *
     * @return array
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

}