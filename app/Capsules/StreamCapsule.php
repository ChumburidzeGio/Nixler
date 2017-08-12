<?php

namespace App\Capsules;

use App\Entities\ProductCategory;
use App\Entities\Product;
use App\Entities\ProductTag;
use DB;

class StreamCapsule {
	
	private $model;

	private $page;

	private $query;

	private $perPage;

	private $currency;

	private $category;

	private $priceMin;

    private $priceMax;

    private $tag;

	private $targetGroup;

	private $executed;

	private $facetQuery;

	protected $items;

	protected $nextPageUrl;

    /**
     * Construct the capsule
     *
     * @return void
     */
    public function __construct(Product $model)
    {
    	$this->model = $model->from('products as p')->select(
            'p.id', 'p.title', 'owner_username', 'p.slug', 'p.currency', 'price', 'likes_count', 'p.media_id', 'p.original_price'
        );

    	$this->page = request('page', 1);

    	$this->perPage = 20;

    	$this->currency = config('app.currency');

    	$this->category = request('cat');

    	$this->priceMin = request('price_min', 0);

        $this->priceMax = request('price_max', 9999);

        $this->tag = request('tag');

    	$this->targetGroup = request('target');

    	$this->facetQuery = $model->from('products as p');

    	$this->query = null;

    	$this->executed = false;

    	return $this;
    }

    /**
     * Filter products by ids
     *
     * @return this
     */
    public function whereIds(array $ids)
    {
        $this->model = $this->model->whereIn('p.id', $ids);

        return $this;
    }

    /**
     * Filter products by merchant
     *
     * @return this
     */
    public function whereSeller($id)
    {
    	$this->model = $this->model->where('p.owner_id', $id);

    	return $this;
    }

    /**
     * Filter products by user likes (just liked products)
     *
     * @return this
     */
    public function whereInCollection($id)
    {
        $this->model = $this->model->join('collection_items as ci', function ($join) use ($id) {
            return $join->on('p.id', '=', 'ci.product_id')->where('ci.collection_id', $id);
        })->orderBy('ci.order', 'desc');

        return $this;
    }

    /**
     * Filter products by user likes (just liked products)
     *
     * @return this
     */
    public function whereLikeBy($id)
    {
    	$this->model = $this->model->join('activities as a', function ($join) use ($id) {
    		return $join->on('p.id', '=', 'a.object')->where('a.actor', $id)->where('a.verb', 'product:liked');
    	})->orderBy('a.id', 'desc');

    	return $this;
    }

    /**
     * Filter products by recommendations
     *
     * @return this
     */
    public function recommendedFor($id)
    {
    	$this->model = $this->model->join('feeds as f', function ($join) use ($id) {
    		return $join->on('p.id', '=', 'f.object_id')->where('f.user_id', $id);
    	})->orderBy('f.id', 'desc');

    	return $this;
    }

    /**
     * Filter products by price
     *
     * @return void
     */
    public function wherePrice($min, $max)
    {
        $this->priceMin = floatval($min);

        $this->priceMax = floatval($max);

        return $this;
    }

    /**
     * Filter products by price
     *
     * @return void
     */
    public function whereTag($tag)
    {
    	$this->tag = $tag;

    	return $this;
    }

    /**
     * Filter products by category
     *
     * @return void
     */
    public function whereCategory($id)
    {
    	$this->category = $id;

    	return $this;
    }

    /**
     * Filter products by search query
     *
     * @return void
     */
    public function search($query)
    {
    	$this->query = $query;

    	return $this;
    }

    /**
     * Set the limit for products per page
     *
     * @return this
     */
    public function perPage(int $amount)
    {
    	$this->perPage = $amount;

    	return $this;
    }

    /**
     * Order products by date of creation
     *
     * @return this
     */
    public function latest()
    {
    	$this->model = $this->model->latest('p.id');

    	return $this;
    }

    /**
     * Order products by popularity
     *
     * @return this
     */
    public function popular()
    {
    	$this->model = $this->model
    	->orderBy('p.likes_count', 'desc')
    	->orderBy('p.sales_count', 'desc')
    	->orderBy('p.updated_at', 'desc')
    	->orderBy('p.views_count', 'desc');

    	return $this;
    }

    /**
     * Select user name with join clause
     *
     * @return void
     */
    private function addUserData()
    {
    	$this->model = $this->model->leftJoin('users as u', 'p.owner_id', '=', 'u.id')->addSelect('u.name as uname');
    }

    /**
     * Filter products with query
     *
     * @return void
     */
    private function getCategoryFamily()
    {
    	$id = $this->category;

    	return app('cache')->remember("category:family:{$id}", pow(10, 10), function() use ($id) {
    		return ProductCategory::where('id', $id)->orWhere('parent_id', $id)->pluck('id')->toArray();
    	});
    }

    /**
     * Filter products by search query, price and category
     *
     * @return void
     */
    private function filter()
    {
    	$searchQuery = trim($this->query);

    	$query = $this->model;

        /*if($searchQuery){

            $resultsLimit = intval($this->page * 20);

            $searchQuery = str_slug($searchQuery, ' ') !== $searchQuery ? $searchQuery . " " . str_slug($searchQuery, ' ') : $searchQuery;

            $searchedIds = Product::search($searchQuery)->take($resultsLimit)->keys()->toArray();

            if(count($searchedIds)) {

                $searchedIdsImploded = implode(',', $searchedIds);

                $query = $query->whereIn('p.id', $searchedIds)->orderByRaw("FIELD(p.id, $searchedIdsImploded)");

                $this->facetQuery = $this->facetQuery->whereIn('p.id', $searchedIds);

            } else {

                $this->model = Product::where('id', '-1');

                $this->facetQuery = Product::where('id', '-1');

                return null;

            }

        }*/

    	if($searchQuery){

            $searchQuery = str_slug($searchQuery, ' ') !== $searchQuery ? $searchQuery . " " . str_slug($searchQuery, ' ') : $searchQuery;

            $query = $query->whereKeyword('owner_username,slug,title,description,p.currency,buy_link,sku,target', $searchQuery);

            $this->facetQuery = $this->facetQuery->whereKeyword('owner_username,slug,title,description,p.currency,buy_link,sku,target', $searchQuery);

    	}

        if($this->category) {

            $query = $query->whereIn('category_id', $this->getCategoryFamily());

            $this->facetQuery = $this->facetQuery->whereIn('category_id', $this->getCategoryFamily());

        }

        if($this->tag) {

            $tag = '%'.strtolower($this->tag).'%';

            $query = $query->join('product_tags as pt', function ($join) use ($tag) {
                return $join->on('p.id', '=', 'pt.product_id')->whereRaw('LOWER(pt.name) like ?', $tag);
            })->distinct('p.id');

            $this->facetQuery = $this->facetQuery->join('product_tags as pt', function ($join) use ($tag) {
                return $join->on('p.id', '=', 'pt.product_id')->whereRaw('LOWER(pt.name) like ?', $tag);
            });

        }

    	if($this->targetGroup && $this->category && $this->category < 9) {

    		$query = $query->whereIn('target', $this->getTargetGroup());

    		$this->facetQuery = $this->facetQuery->whereIn('target', $this->getTargetGroup());

    	}

    	if($this->priceMin || $this->priceMax != 9999) {

    		$query = $query->whereBetween('price', [$this->priceMin, $this->priceMax]);

    		$this->facetQuery = $this->facetQuery->whereBetween('price', [$this->priceMin, $this->priceMax]);

    	}

        $this->model = $query->where('p.currency', $this->currency)->active()->latest('p.id');

        $this->facetQuery = $this->facetQuery->where('p.currency', $this->currency)->active();

        $this->addUserData();

    }

    /**
     * Get price range from products collection
     *
     * @return this
     */
    private function getPriceRange($prices)
    {
        $prices = array_map('intval', $prices);

    	$prices = array_count_values($prices);

    	if(!array_get($prices, '0')){
    		$prices = array_prepend($prices, 0, '0');
    	}

    	if(!array_get($prices, '9999')){
    		$prices = array_prepend($prices, 0, '9999');
    	}

    	krsort($prices);

    	return $prices;
    }

    /**
     * Run the query and transform paginated list of products
     *
     * @return this
     */
    public function get()
    {
        $this->filter();

        $this->items = $this->cache('items', 0, function() {

            $paginate = $this->model->skip(($this->page - 1) * $this->perPage)->simplePaginate($this->perPage);

            return $this->transform($paginate->items());

        });

        $this->executed = true;

        return $this;
    }

    /**
     * Get the array of possible targets for target group query
     *
     * @return array
     */
    private function getTargetGroup() : array
    {
        switch ($this->targetGroup) {
            case 'men':
                return ['men', 'unia'];
            case 'women':
                return ['women', 'unia'];
            case 'tboys':
                return ['tboys', 'unit'];
            case 'tgirls':
                return ['tgirls', 'unit'];
            case 'kboys':
                return ['kboys', 'unik'];
            case 'kgirls':
                return ['kgirls', 'unik'];
            case 'bboys':
                return ['bboys', 'unib'];
            case 'bgirls':
                return ['bgirls', 'unib'];
        }
    }

    /**
     * Run and take just ids
     *
     * @return this
     */
    public function keys()
    {
    	$this->filter();
    	
    	$this->items = $this->model->take($this->perPage)->select('p.id')->pluck('id');

        $this->executed = true;

    	return $this;
    }

    /**
     * Transform products
     *
     * @return this
     */
    private function transform($products)
    {
    	return array_map(function($item){

    		return [
        		'id'      => (int) $item->id,
        		'title'   => $item->title,
        		'url'   => $item->url(),
        		'price' => $item->price_formated,
        		'likes_count' => $item->likes_count,
        		'owner' => $item->uname,
                'photo' => media($item, 'product', 'short-card'),
        		'discount' => $item->discount
    		];

    	}, $products);

    }

    /**
     * Return products
     *
     * @return array
     */
    public function items()
    {
    	if(!$this->executed){
    		$this->get();
    	}

    	return $this->items;
    }

    /**
     * Return next page URL
     *
     * @return string
     */
    private function stateId($key = null)
    {
    	$params = request()->only(['cat', 'price_min', 'price_max', 'query', 'tab', 'target', 'tag']);

    	$params = array_prepend($params, intval($this->page), 'page');

    	$params = array_prepend($params, auth()->id(), 'user');

    	$params = array_prepend($params, request()->path(), 'path');

    	$params = array_prepend($params, 'stream', 'capsule');

        $params = array_prepend($params, $key, 'key');

        $params = array_prepend($params, app()->getLocale(), 'locale');

        $params = array_prepend($params, config('app.currency'), 'currency');

    	$params = array_prepend($params, config('app.country'), 'country');

    	return md5(implode('-', $params));
    }

    /**
     * Return next page URL
     *
     * @return string
     */
    public function nextPageUrl()
    {
    	if(count($this->items) < $this->perPage) {
    		return null;
    	}

    	$path = request()->path();

    	$params = request()->only(['cat', 'price_min', 'price_max', 'query', 'tab', 'tag', 'target']);

    	$params = http_build_query(array_prepend($params, intval($this->page + 1), 'page'));

    	return url($path.'?'.$params);
    }

    /**
     * Return price facets
     *
     * @return array
     */
    private function cache($key, $minutes, $callback)
    {
    	$key = $this->stateId($key);

    	return app('cache')->remember($key, $minutes, $callback);
    }

    /**
     * Return price facets
     *
     * @return array
     */
    public function priceFacet()
    {
        if(!$this->category && !$this->query && !$this->tag) {
            return false;
        }

    	return $this->cache('priceFacet', 18, function() {

    		$prices = $this->facetQuery->pluck('price')->toArray();

    		return $this->getPriceRange($prices);
    			
    	});
    }

    /**
     * Return price facets
     *
     * @return array
     */
    public function categories()
    {
        return $this->cache('categories', 100, function(){

            $categories = new ProductCategory;

            $category = $this->category;

            $ids = $this->category ? array_unique($this->facetQuery->pluck('category_id')->toArray()) : [];

            $match = [
                1 => [0, 9],
                9 => [8, 30],
                30 => [29, 40],
                40 => [39, 54],
                54 => [53, 69],
                69 => [68, 80],
            ];

            if($category && !array_key_exists($category, $match)) {

                $tags = $this->tagsAsCategories();

                if($tags->count()) {

                    return $tags->map(function($item) use ($category) {

                        return [
                            'id'     => null,
                            'icon'   => null,
                            'name'   => $item->name,
                            'href'   => route('feed', array_merge(request()->only(['price_min', 'price_max', 'query', 'target']), [
                                'cat' => $category, 
                                'tag' => $item->name
                            ])),
                            'active' => $this->tag == $item->name,
                            'empty' => $item->count,
                        ];

                    });

                }

            }

            foreach ($match as $key => $value) {
                
                if($category > array_first($value) && $category < array_last($value)) {
                    $category = $key;
                }
            }

            $categories = $this->category ? $categories->where('parent_id', $category) : $categories->whereNull('parent_id');

            $categories = $categories->select('id', 'icon')->with('translations')->get()->map(function($item) use ($ids) {
                return [
                    'id'     => (int) $item->id,
                    'icon'   => $item->icon,
                    'name'   => $item->name,
                    'href'   => route('feed', array_merge(request()->only(['price_min', 'price_max', 'query', 'target','tag']), ['cat' => $item->id])),
                    'active' => (request('cat') == $item->id),
                    'empty' => ((count($ids) || $this->category) && !in_array($item->id, $ids)),
                ];
            });

            return $categories->toArray();

        });

    }

    /**
     * Return price facets
     *
     * @return array
     */
    public function tagsAsCategories()
    {
        $categories = $this->getCategoryFamily();

        $targetGroup = $this->targetGroup ? $this->getTargetGroup() : null;

        return ProductTag::whereExists(function ($query) use ($categories, $targetGroup) {

            $query->select(DB::raw(1))

            ->from('products as p')->whereIn('p.category_id', $categories)

            ->whereRaw('p.id = product_tags.product_id')

            ->where('p.is_active', true);

            if(count($targetGroup)) 
            {
                $query = $query->whereIn('p.target', $targetGroup);
            }


        })

        ->groupBy('name')->orderBy('total', 'desc')

        ->where('type', 'silhouetteCode')

        ->select('name', DB::raw('count(*) as total'))->get();

    }

    /**
     * Return target facets
     *
     * @return array
     */
    public function targets() : array
    {
    	return $this->cache('targetGroup', 100, function(){

            if(!$this->category || $this->category > 8) {
                return [];
            }

            $targets = [
                'adults' => [],
                'boys' => [
                    'title' => __('For Boys'),
                ],
                'girls' => [
                    'title' => __('For Girls'),
                ],
            ];

            $targets = $this->pushTargetGroup($targets, 'adults', 'For Men', 'men');
            $targets = $this->pushTargetGroup($targets, 'adults', 'For Women', 'women');

            $targets = $this->pushTargetGroup($targets, 'boys', 'Teens (9 - 16 years)', 'tboys');
            $targets = $this->pushTargetGroup($targets, 'boys', 'Kids (2 - 9 years)', 'kboys');
            $targets = $this->pushTargetGroup($targets, 'boys', 'Babies (0 - 2 years)', 'bboys');

            $targets = $this->pushTargetGroup($targets, 'girls', 'Teens (9 - 16 years)', 'tgirls');
            $targets = $this->pushTargetGroup($targets, 'girls', 'Kids (2 - 9 years)', 'kgirls');
            $targets = $this->pushTargetGroup($targets, 'girls', 'Babies (0 - 2 years)', 'bgirls');

            return $targets;

    	});

    }

    /**
     * Return capsule as array
     *
     * @return array
     */
    public function pushTargetGroup($targets, $group, $name, $key)
    {
        array_push($targets[$group], [
            'name' => __($name),
            'link' => route('feed', array_merge(request()->only(['price_min', 'price_max', 'query', 'cat', 'tag']), ['target' => $key])),
            'active' => $this->targetGroup == $key
        ]);

        return $targets;
    }

    /**
     * Return capsule as array
     *
     * @return array
     */
    public function toArray()
    {
    	$items = $this->items();

    	$nextPageUrl = $this->nextPageUrl();

    	return compact('items', 'nextPageUrl');
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