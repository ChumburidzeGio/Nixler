<?php

namespace App\Repositories;

use App\Repositories\BaseRepository;
use App\Entities\ProductCategory;
use App\Services\RecommService;
use Illuminate\Container\Container as App;
use App\Entities\Product;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use App\Entities\Activity;
use App\Entities\ProductVariant;
use App\Entities\ProductTag;
use App\Entities\ShippingPrice;
use App\Entities\Order;
use Carbon\Carbon;
use Ayeo\Price\Price;
use App\Notifications\ProductUpdated;
use App\Notifications\ProductDeleted;
use App\Notifications\OrderStatusChanged;
use Auth, DB;

class ProductRepository extends BaseRepository {

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return Product::class;
    }
    

    /**
     * @param $slug string
     * @param $owner string
     * @return array
     */
    public function findBySlug($slug, $owner_username)
    {
        $product = $this->model->where(compact('slug', 'owner_username'))->firstOrFail();

        if(!$product->is_active) {
            abort_if(auth()->guest() || auth()->user()->cannot('view', $product), 404);
        }

        $product->load('owner.shippingPrices', 'category');
        $product->owner->shippingPrices->load('location');
        $product->setRelation('media', $product->media('photo')->take(10)->get());
        $product->setRelation('comments', $product->comments()->sortBy('most_recent')->paginate());

        $product->tags = ProductTag::where('product_id', $product->id)->get();

        $product->variants = ProductVariant::where('product_id', $product->id)->get();

        $product->setRelation('similar', $this->similar($product->id, $product->owner_id));

        $product->trackActivity('product:viewed');

        $product->comments->transform(function($comment){
            return [
                'id' => $comment->id,
                'avatar' => $comment->author->avatar('comments'),
                'author' => $comment->author->name,
                'text' => nl2br(str_limit($comment->text, 1000)),
                'time' => $comment->created_at->format('c'),
                'can_delete' => auth()->check() && auth()->user()->can('delete', $comment) ? 1 : 0
            ];
        });

        return $product;
    }
    


    /**
     * Create new product
     *
     * @return \Illuminate\Http\Response
     */
    public function create(array $attributes = [])
    {
        $user = auth()->user();

        return $this->model->create([
            'status' => 'inactive',
            'currency' => $user->currency,
            'owner_id' => $user->id,
            'owner_username' => $user->username,
        ]);
    }
    


    /**
     * Prepare product for editing
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = auth()->user();

        $product = $this->model->where([
            'id' => $id,
            'owner_id' => $user->id
        ])->firstOrFail();

        $product->variants = ProductVariant::where('product_id', $product->id)->get();

        $product->tags = ProductTag::where('product_id', $product->id)->get()->map(function($item){
            return ['text' => $item->name];
        });

        $product->media = $product->getMedia('photo')->map(function($media){
            return [
                'success' => true,
                'id' => $media->id,
                'thumb' => url('media/'.$media->id.'/avatar/profile.jpg')
            ];
        });

        $categories = ProductCategory::with('translations', 'children.translations')->whereNull('parent_id')->orderBy('order')->get()->map(function($item){
            return $item->children->map(function($subitem) use ($item){
                return [
                    'zone' => $item->name,
                    'id' => $subitem->id,
                    'label' => $subitem->name,
                ];
            });
        })->collapse();

        return compact('product', 'categories', 'user');
    }
    


    /**
     * Prepare product for editing
     *
     * @return \Illuminate\Http\Response
     */
    public function update(array $attributes, $id)
    {
        $user = auth()->user();

        $product = $this->model->where([
            'id' => $id,
            'owner_id' => $user->id
        ])->firstOrFail();

        $this->sortMedia(array_get($attributes, 'media'), $product);

        $this->syncVariants(array_get($attributes, 'variants'), $product);

        $this->syncTags(array_get($attributes, 'tags'), $product);

        $product->fill([
            'title' => array_get($attributes, 'title'),
            'description' => array_get($attributes, 'description'),
            'category_id' => array_get($attributes, 'category'),
            'buy_link' => array_get($attributes, 'buy_link'),
            'is_used' => array_get($attributes, 'is_used', 0),
        ]);

        if(!$product->has_variants){
            $product->price = array_get($attributes, 'price');
            $product->in_stock = array_get($attributes, 'in_stock');
        }

        $product->save();

        if(array_get($attributes, 'action') == 'publish' && $user->can('create', $product)) {
            $product->notify(new ProductUpdated);
            $product->markAsActive();
        }

        return $product;
    }
    


    /**
     * @param $id integer
     * @param $file string|mixed
     * @param ? $user \App\Entities\User
     * @return \App\Entities\Media
     */
    public function uploadMediaForProduct($id, $file, $user = null)
    {
        $user = $user ? : auth()->user();

        $product = $user->products()->findOrFail($id);

        return $product->uploadPhoto($file, 'photo');
    }
    


    /**
     * @param $product_id integer
     * @param $media_id integer
     * @return \App\Entities\Media
     */
    public function removeMediaFromProductById($id, $file, $user = null)
    {
        $user = $user ? : auth()->user();

        $product = $user->products()->findOrFail($product_id);

        $media = $product->media()->findOrFail($media_id);
        
        return $media->delete();
    }
    


    /**
     * Prepare product for editing
     *
     * @return void
     */
    public function sortMedia($attribute, $product)
    {
        $media_sorted = json_decode($attribute);

        if($media_sorted){
            $media = $product->getMedia('photo');
            $media = $media->sortBy(function ($photo, $key) use ($media_sorted) {
                foreach ($media_sorted as $key => $value) {
                    if(isset($value->id) && $value->id == $photo->id){
                        return $key;
                    }
                }
            });
            $product->syncMedia($media, 'photo');
        }
    }



    /**
     * Tranform text into tokens
     *
     * @return array
     */
    public function like($id)
    {
        $product = $this->model->findOrFail($id);

        $user = auth()->user();

        $liked = $product->toggleActivity('product:liked');

        $product->likes_count = $product->getActivities('product:liked')->count();
        
        $product->save();
        
        return $liked;
    }


    /**
     * Tranform text into tokens
     *
     * @return array
     */
    public function similar($id, $owner_id)
    {
        $props = auth()->guest() ? [] : [
            'filter' => "'currency' == \"".auth()->user()->currency."\""
        ];

        $ids = (new RecommService)->similar($id, 5, auth()->id(), $props);
        
        if($ids) {
            return $this->model->whereIn('id', $ids)->with('firstPhoto')->where('status', 'active')->take(5)->get();
        } else {
            return $this->model->where('owner_id', $owner_id)->where('id', '<>', $id)->where('status', 'active')->with('firstPhoto')->take(5)->get();
        }
        
    }


    /**
     * @return \Illuminate\Http\Response
     */
    public function getUserStream()
    {
        $user = auth()->user();

        if($user) {

            $products = $user->stream()->with('firstPhoto', 'owner')->latest()->paginate(20);

            if(!$products->total() && !request()->has('page')) {
                $user->pushInStream($this->getPopularProducts(20, 1), 'pop');
                $products = $user->stream()->with('firstPhoto', 'owner')->latest()->paginate(20);
            }

        } else {

            $products = $this->getPopularProducts();
            
        }

        return $this->transformProducts($products);

    }



    /**
     * @param $active integer
     * @return Category
     */
    public function getProductCategories($active)
    {
        if($active){
            $category = ProductCategory::find($active);

            if($category){
                $children = ProductCategory::where('parent_id', $category->id)->with('translations')->get();

                return $children->count() || !$category->parent_id 
                    ? $children 
                    : ProductCategory::where('parent_id', $category->parent_id)->with('translations')->get();
            }
        }

        return ProductCategory::whereNull('parent_id')->with('translations')->get();
    }


    /**
     * Price filter
     */
    public function filterPrice($value)
    {   
        return filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }


    /**
     * Search in products
     *
     * @return \Illuminate\Http\Response
     */
    public function search($filters)
    {   
        $query = array_get($filters, 'query');

        $category = intval(array_get($filters, 'cat'));

        $priceMin = $this->filterPrice(array_get($filters, 'price_min', 0));

        $priceMax = $this->filterPrice(array_get($filters, 'price_max', 9999));

        if($query) {

            $results = $this->model->whereKeyword($query)->where('currency', 'PLN')->where('status', 'active')
                ->limit(1000)->get(['id', 'price', 'category_id']);

            $facets = collect([
                'price' => $this->getPriceRangeForProducts($results)
            ]); 

            if($category) {
                $results = $this->filterByCategory($results, $category);
            }

            if($priceMin || $priceMax != 9999) {
                $results = $this->filterByPrice($results, $priceMin, $priceMax);
            }

            $currentPage = request()->input('page', 1);

            $ids = $results->take(($currentPage * 20 + 1))->pluck('id')->toArray();

            $products = count($ids) ? $this->findByIds($ids)->with('firstPhoto', 'owner')->simplePaginate(20) : collect([]);

        } else {

            $results = $this->model->where('currency', 'PLN')->where('status', 'active');

            if($category) {

                $category_ids = ProductCategory::where('id', $category)->orWhere('parent_id', $category)->pluck('id')->toArray();

                $results->whereIn('category_id', $category_ids);

            }

            $productsForFaceting = $results->get(['id', 'price', 'category_id']);

            $facets = collect([
                'price' => $this->getPriceRangeForProducts($productsForFaceting)
            ]); 

            if($priceMin || $priceMax != 9999) {
                $results->whereBetween('price', [$priceMin, $priceMax]);
            }

            $products = $results->with('firstPhoto', 'owner')->simplePaginate(20);
        }

        $products = $this->transformProducts($products);

        return compact('products', 'facets');
    }


    /**
     * @param $count integer
     */
    public function filterByCategory($collection, $id)
    {
        $category_ids = ProductCategory::where('id', $id)->orWhere('parent_id', $id)->pluck('id')->toArray();

        return $collection->filter(function ($item) use ($category_ids) {
            return in_array($item->category_id, $category_ids);
        });
    }


    /**
     * @param $count integer
     */
    public function filterByPrice($collection, $min, $max)
    {
        return $collection->filter(function ($item) use ($min, $max) {

            $price = floatval($item->price);

            return  $price >= $min && $price <= $max;

        });
    }


    /**
     * @param $count integer
     */
    public function transformProducts($products)
    {
        $items = [];

        $nextPageUrl = false;

        if(!method_exists($products, 'items')) {
            return collect(compact('items', 'nextPageUrl'));
        }

        $items = collect($products->items())->reject(function($item){
            return !$item->owner;
        })->map(function($item, $id){

            return [
            'id'      => (int) $item->id,
            'title'   => $item->title,
            'url'   => $item->url(),
            'price' => $item->currency . ' ' . $item->price,
            'likes_count' => $item->likes_count,
            'owner' => $item->owner->name,
            'photo' => route('photo', [
                'id' => $item->firstPhoto ? array_get($item->firstPhoto->first(), 'id', '-') : '-',
                'type' => 'product',
                'place' => 'short-card'
            ])
            ];

        });

        $nextPageUrl = count($items) ? $products->appends(request()->only(['cat', 'price_min', 'price_max', 'query']))->nextPageUrl() : false;

        return collect(compact('items', 'nextPageUrl'));
    }


    /**
     * @param $count integer
     */
    public function getPriceRangeForProducts($products)
    {
        $prices = $products->groupBy('price')->mapWithKeys(function($items, $price){
            return [floatval($price) => count($items)];
        })->sort();

        if(!isset($prices->{'0'})){
            $prices->prepend(0, 0);
        }

        if(!isset($prices->{'9999'})){
            $prices->put(9999, 0);
        }

        return $prices->all();
    }


    /**
     * @param $count integer
     */
    public function findByIds($ids)
    {
        if(!count($ids)) {
            return collect([]);
        }

        $ids_ordered = implode(',', $ids);

        return $this->model->whereIn('id', $ids)->where('status', 'active')->orderByRaw(DB::raw("FIELD(id, $ids_ordered)"));
    }


    /**
     * @param $count integer
     */
    public function getPopularProducts($count = 6, $justIds = false)
    {
        $ids = Activity::select('object', DB::raw('count(activities.object) as total'))
                    ->whereIn('verb', ['product:viewed', 'product:liked'])
                    ->groupBy('object')
                    ->orderBy('total','desc')
                    ->whereBetween('activities.created_at', [Carbon::now()->subWeek(), Carbon::now()])
                    ->pluck('object');

        if($justIds) {
            return $ids;
        }

        return $this->model->whereIn('id', $ids)->with('firstPhoto', 'owner')->where('status', 'active')->paginate(20);
    }


    /**
     * Sync variants with product.
     *
     * @param $variants string(json)
     * @param $product Product
     */
    public function syncVariants($variants, Product $product)
    {
        $variants = collect(json_decode($variants));

        $models = $variants->map(function ($variant) use ($product) {
            return $this->updateOrCreateVariant($variant, $product);
        });

        if($models->count()) {
            $product->has_variants = true;
            $product->price = $models->min('price');
            $product->in_stock = $models->sum('in_stock');
        }

        return ProductVariant::whereNotIn('id', $models->pluck('id'))->where('product_id', $product->id)->delete();
    }


    /**
     * Update or create new variant for product.
     *
     * @param $variant object
     * @param $product Product
     */
    public function updateOrCreateVariant($variant, Product $product)
    {
        $model = new ProductVariant;

        if(isset($variant->id)) {
            $model = ProductVariant::find($variant->id);
        }

        $model->fill([
            'product_id' => $product->id,
            'name' => $variant->name,
            'price' => $variant->price,
            'in_stock' => $variant->in_stock,
        ]);

        $model->save();

        return $model;
    }


    /**
     * Sync tags with product.
     *
     * @param $tags string(json)
     * @param $product Product
     */
    public function syncTags($tags, Product $product)
    {
        $tags = collect(json_decode($tags))->pluck('text');

        $ids = $tags->map(function ($tag) use ($product) {
            $model = $this->updateOrCreateTag($tag, $product);
            return $model->id;
        })->flatten();

        return ProductTag::whereNotIn('id', $ids)->where('product_id', $product->id)->delete();
    }


    /**
     * Update or create new tag for product.
     *
     * @param $tag object
     * @param $product Product
     *
     * @return ProductTag
     */
    public function updateOrCreateTag($tag, Product $product)
    {
        $tag = trim($tag);
        $slug = str_slug($tag);

        $model = ProductTag::whereTranslation('slug', $slug)->where('product_id', $product->id)->first();

        if(!$model) {
            $model = ProductTag::create([
                'name' => $tag,
                'slug' => $slug,
                'user_id' => auth()->id(),
                'product_id' => $product->id
            ]);
        }

        return $model;
    }


    /**
     * Find product and shipping conditions for this product for each city inside country.
     *
     * @param $id integer
     *
     * @return array
     */
    public function getWithShippingByCity($id)
    {
        $product = $this->model->findOrFail($id);

        $product->load('owner.shippingPrices', 'owner.country.cities');

        $prices = $product->owner->shippingPrices;

        $cities = $product->owner->country()->first()->cities;

        $cities->transform(function($city) use ($prices) {

            $shipping = $prices->filter(function($item) use ($city) {
                return ($item->type == 'city' && $item->location_id == $city->id);
            });

            if(!$shipping->count()){
                $shipping = $prices->filter(function($item) use ($city) {
                    return ($item->type == 'country' && $item->location_id == $city->country_id);
                });
            }

            $shipping_text = $shipping_price = null;

            $shipping = $shipping->map(function($item){
                extract($item->toArray());
                return compact('price', 'currency', 'window_from', 'window_to');
            })->first();

            if($shipping) {
                $delivery = $shipping['window_from'] == $shipping['window_to'] 
                ? "{$shipping['window_from']} day" 
                : "{$shipping['window_from']}-{$shipping['window_to']} days";

                $price = $shipping['price'] == '0.00' ? 'free' : $shipping['currency'].$shipping['price'];

                $shipping_text = "Delivery in {$delivery} for {$price}";
                
                $shipping_price = $shipping['price'];
            }

            return [
                'id' => $city->id,
                'label' => $city->name,
                'shipping' => $shipping_text,
                'shipping_price' => $shipping_price,
            ];

        });

        return compact('product', 'cities');
    }


    /**
     * Store new order
     *
     * @param $id integer
     * @param $quantity int
     * @param $variant int
     *
     * @return Order
     */
    public function order($id, $quantity, $variant)
    {
        $product = $this->model->findOrFail($id);

        $user = auth()->user();

        $variants_count = ProductVariant::where('product_id', $product->id)->count();

        if($product->has_variants && !$variants_count) {
            $product->update([
                'has_variants' => 0
            ]);

            logger()->error("Incorrect HasVariant attribute on product {$product->id}");
        }

        if($product->has_variants && $variants_count) {

            $variant = ProductVariant::where('product_id', $product->id)->find($variant);

            $productPrice = Price::buildByGross($variant->price, 0, $product->currency);

        } else {
            $productPrice = Price::buildByGross($product->price, 0, $product->currency);
        }

        $subTotal = $productPrice->multiply($quantity);

        $mShippingPrice = $this->getShippingPriceForCity($user->city_id, $product->owner_id);

        $shippingPrice = Price::buildByGross($mShippingPrice->price, 0, $product->currency);

        $total = $productPrice->add($shippingPrice)->getGross();

        $windowFrom = Carbon::now()->addDays($mShippingPrice->window_from);

        $windowTo = Carbon::now()->addDays($mShippingPrice->window_to);

        $order = Order::create([
            'status' => 'created',
            'amount' => $total,
            'currency' => $product->currency,
            'quantity' => $quantity,
            'address' => $user->getMeta('address'),
            'shipping_cost' => $shippingPrice->getGross(),
            'shipping_window_from' => $windowFrom,
            'shipping_window_to' => $windowTo,
            'payment_method' => 'COD',
            'user_id' => $user->id,
            'product_id' => $product->id,
            'product_variant' => $variant ? $variant->name : null,
            'merchant_id' => $product->owner_id
        ]);

        $order->notify(new OrderStatusChanged());

        if($product->has_variants) {

            $variant->decrement('in_stock', $quantity);

            $variant->update();

        } else {

            $product->decrement('in_stock', $quantity);

            $product->update();

        }

        return $order;
    }


    /**
     * Get shippng price for particular city
     *
     * @param $city_id int
     * @param $merchant_id int
     *
     * @return float
     */
    public function getShippingPriceForCity($city_id, $merchant_id)
    {
        $model = ShippingPrice::where([
            'user_id' => $merchant_id,
            'type' => 'city',
            'location_id' => $city_id
        ])->first();

        if(!$model) {
            $model = ShippingPrice::where([
                'user_id' => $merchant_id,
                'type' => 'country',
            ])->firstOrFail();
        }

        return $model;
    }


    /**
     * Get shippng price for particular city
     *
     * @param $city_id int
     * @param $merchant_id int
     *
     * @return float
     */
    public function delete($id)
    {
        $user = auth()->user();

        $product = $user->products()->findOrFail($id);
        
        $product->notify(new ProductDeleted);

        $product->delete();
    }

}