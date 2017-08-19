<?php

namespace App\Capsules;

use App\Entities\Comment;

class CommentsCapsule {
	
	private $model;

    protected $page;

	protected $items;

    /**
     * Construct the capsule
     *
     * @return void
     */
    public function __construct(Comment $model)
    {
    	$this->model = $model->from('comments as c')->select('c.id', 'c.text', 'c.media_id', 'c.created_at', 'c.user_id');

        $this->page = 1;
    }

    /**
     * Filter by privacy
     *
     * @return void
     */
    public function page($num)
    {
        $this->page = $num;

        return $this;
    }

    /**
     * Filter by privacy
     *
     * @return void
     */
    public function whereTarget($id)
    {
    	$this->model = $this->model->where('target_id', $id);

    	return $this;
    }

    /**
     * OrderBy Latest
     *
     * @return void
     */
    public function latest()
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
    	$this->model = $this->model->leftJoin('users as u', 'c.user_id', '=', 'u.id')->addSelect('u.name as unm');
    }

    /**
     * Execute
     *
     * @return void
     */
    public function get()
    {
    	$this->addUserData();

    	$comments = $this->model->simplePaginate(10, ['*'], null, $this->page);

    	$this->items = $this->transform($comments);

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
     * Execute
     *
     * @return void
     */
    public function count()
    {
    	return $this->items->count();
    }

    /**
     * Transform
     *
     * @return void
     */
    public function transform($comments)
    {
        return $comments->map(function($item){

            return [
                'id' => $item->id,
                'avatar' => route('avatar', ['id' => $item->user_id, 'place' => 'comments']),
                'author' => $item->unm,
                'attachment' => media($item, 'product', 'comment-attachment', null),
                'text' => nl2br(str_limit($item->text, 1000)),
                'time' => $item->created_at->format('c'),
                'can_delete' => auth()->check() && auth()->user()->can('delete', $item) ? 1 : 0
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