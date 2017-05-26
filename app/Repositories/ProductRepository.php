<?php

namespace App\Repositories;

use App\Repositories\BaseRepository;
use App\Entities\ProductCategory;
use App\Services\RecommService;
use Illuminate\Container\Container as App;
use App\Entities\Product;
use App\Transformers\ProductTransformer;
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
use App\Services\AlgoliaService;
use App\Notifications\ProductUpdated;
use App\Notifications\ProductDeleted;
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

        $product->setRelation('similar', $this->similar($product->id));

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

        $product->variants = ProductVariant::where('product_id', $product->id)->get()->toJson();

        $product->tags = ProductTag::where('product_id', $product->id)->get()->toJson();

        $product->media = $product->getMedia('photo')->map(function($media){
            return [
                'success' => true,
                'id' => $media->id,
                'thumb' => url('media/'.$media->id.'/avatar/profile.jpg')
            ];
        })->toJson();

        $categories = ProductCategory::with('translations', 'children.translations')->orderBy('order')->get();

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
    public function similar($id)
    {
        $props = auth()->guest() ? [] : [
            'filter' => "'currency' == \"{auth()->user()->currency}\""
        ];

        $ids = (new RecommService)->similar($id, 5, auth()->id(), $props);
        
        return $this->model->whereIn('id', $ids)->with('firstPhoto')->take(5)->get();
    }


    /**
     * @return \Illuminate\Http\Response
     */
    public function getUserStream($cat = array('*'))
    {
        $user = auth()->user();

        if(is_string($cat)){
            $products = $this->filterProductsByCategory($cat);
        } elseif($user) {
            $products = $user->stream()->with('firstPhoto', 'owner')->latest()->paginate(20);
        } else {
            $products = $this->getPopularProducts();
        }

        $manager = new Manager();

        $resource = new Collection($products, new ProductTransformer());

        $resource->setPaginator(new IlluminatePaginatorAdapter($products));

        return $manager->createData($resource);
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

        $params = [
            //'customRanking' => ['desc(price)'],
            'hitsPerPage' => 50,
            'attributesToRetrieve' => ['objectID'],
            //'attributesForFaceting' => ["price", "category_id"],
            'attributesToHighlight' => [],
            'facets' => ['price'],
            'numericFilters' => ["price:{$priceMin} TO {$priceMax}"],
            //'aroundLatLng' => '40.71, -74.01'
        ];

        if($category) {
            $params['filters'] = "category_id:{$category}";
        }

        $results = (new AlgoliaService)->search('products', $query, $params);

        $ids = array_flatten(array_get($results, 'hits'));

        $facets = $this->transformSearchFacets(array_get($results, 'facets'), $ids);

        $products = $this->findByIds($ids)->with('firstPhoto', 'owner')->paginate(20);

        $products = $this->transformToCollection($products);

        return compact('products', 'facets');
    }


    /**
     * @param $count integer
     */
    public function transformSearchFacets($facets, $ids)
    {
        if(!$facets || count($ids) < 10) {
            return collect([]);
        }

        $facets['price'][9999] = 0;
        $facets['price'][0] = 0;

        return collect($facets);
    }


    /**
     * @param $count integer
     */
    public function transformToCollection($products)
    {
        $manager = new Manager();

        $resource = new Collection($products, new ProductTransformer());

        $resource->setPaginator(new IlluminatePaginatorAdapter($products));

        return $manager->createData($resource);
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
    public function getPopularProducts($count = 6)
    {
        $ids = Activity::select('object', DB::raw('count(activities.object) as total'))
                    ->whereIn('verb', ['product:viewed', 'product:liked'])
                    ->groupBy('object')
                    ->orderBy('total','desc')
                    ->whereBetween('activities.created_at', [Carbon::now()->subWeek(), Carbon::now()])
                    ->pluck('object');

        return $this->model->whereIn('id', $ids)->with('firstPhoto', 'owner')->where('status', 'active')->paginate(20);
    }


    /**
     * Filter products by category
     *
     * @return \Illuminate\Http\Response
     */
    public function filterProductsByCategory($cat)
    {
        $cats = ProductCategory::where('id', $cat)->orWhere('parent_id', $cat)->pluck('id')->toArray();

        return $this->model->with('firstPhoto', 'owner')->where('status', 'active')->whereIn('category_id', $cats)->latest()->paginate(20);
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
        $tags = collect(json_decode($tags));

        $ids = $tags->map(function ($tag) use ($product) {
            $model = $this->updateOrCreateTag($tag->text, $product);
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

        $model = ProductTag::whereTranslation('slug', $slug)->first();

        if(!$model) {
            $model = ProductTag::create([
                'name' => $tag,
                'slug' => $slug,
                'user_id' => auth()->id()
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

        if($product->has_variants) {

            $variant = ProductVariant::where('product_id', $product->id)->findOrFail($variant);

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
            'product_variant' => $variant->name,
            'merchant_id' => $product->owner_id
        ]);

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