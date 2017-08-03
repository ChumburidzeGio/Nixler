<?php

namespace App\Repositories;

use App\Repositories\BaseRepository;
use App\Entities\ProductCategory;
use App\Services\RecommService;
use App\Services\AnalyticsService;
use App\Services\SystemService;
use Illuminate\Container\Container as App;
use App\Entities\Product;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use App\Entities\Activity;
use App\Entities\ProductSource;
use App\Entities\ProductVariant;
use App\Entities\ProductTag;
use App\Entities\Metric;
use App\Entities\ShippingPrice;
use App\Entities\Order;
use App\Events\ProductPublished;
use App\Events\ProductDisabled;
use App\Events\ProductDeleted;
use App\Events\ProductLiked;
use App\Events\ProductDisliked;
use App\Events\OrderCreated;
use Carbon\Carbon;
use Ayeo\Price\Price;
use App\Notifications\OrderStatusChanged;
use Illuminate\Support\Facades\Cache;
use App\Crawler\Crawler;
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

        $product->setRelation('media', $product->media('photo')->get()->map(function($item, $key){
            return [
                'thumb' => $item->photo('thumb_s'),
                'full' => $item->photo('full'),
            ];
        }));

        $product->setRelation('comments', $product->comments()->latest('id')->paginate());

        $product->tags = ProductTag::where('product_id', $product->id)->get();

        $product->variants = ProductVariant::where('product_id', $product->id)->where('in_stock', '>', 0)->orderBy('price')->get()->map(function($item) use($product) {
            return [
                'id' => $item->id,
                'name' => $item->name.' - '.money($product->currency, $item->price)
            ];
        });

        $product->setRelation('similar', $this->similar($product));

        $product->trackActivity('product:viewed');

        $product->comments->transform(function($comment){
            return [
                'id' => $comment->id,
                'avatar' => $comment->author->avatar('comments'),
                'author' => $comment->author->name,
                'attachment' => media($comment, 'product', 'comment-attachment', null),
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

        $model = $this->model->where([
            'owner_id' => $user->id
        ])->latest('id')->first();

        if($model && !$model->is_active && !$model->in_stock) {
            return $model;
        }
        
        return $this->model->create([
            'in_stock' => 0,
            'is_active' => 0,
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
            'sku' => array_get($attributes, 'sku'),
        ]);

        if(!$product->has_variants){
            $product->price = array_get($attributes, 'price');
            $product->in_stock = array_get($attributes, 'in_stock');
        }

        $product->save();
        
        if(array_get($attributes, 'action') == 'publish' && $user->can('create', $product)) {

            $product = $this->publish($product, $user);

        } else {

            event(new ProductDisabled($product, $user));

        }

        return $product;
    }
    


    /**
     * Prepare product for editing
     *
     * @return \Illuminate\Http\Response
     */
    public function publish($product, $user)
    {
        event(new ProductPublished($product, $user));

        $product->markAsActive();

        return $product;
    }
    


    /**
     * Import product from url
     *
     * @return \Illuminate\Http\Response
     */
    public function import(string $url)
    {
        $user = auth()->user();

        $source = ProductSource::where([
            'merchant_id' => $user->id,
            'source' => $url
        ])->first();

        if($source) {

            $product = $this->model->find($source->product_id);

            if(!$product) {

                $source->delete();

                return null;
            }

        } else {

            $product = $this->create();

        }
        
        $metadata = app(Crawler::class)->get($url);

        if($metadata->isInvalid()) {

            return $source ? $this->hide($source->product_id) && false : null;

        }

        $this->syncVariants($metadata->getVariants(), $product);

        $this->syncTags($metadata->getTags(), $product);

        if(!$source) {

            foreach ($metadata->getMedia() as $src) {
                $this->uploadMediaForProduct($product->id, $src);
            }

        }

        $product->fill([
            'title' => $metadata->getTitle(),
            'description' => $metadata->getDescription(),
            'category_id' => $metadata->getCategory(),
            'target' => $metadata->getTarget(),
            'sku' => $metadata->getSKU(),
        ]);

        if(!$product->has_variants){

            $product->price = $metadata->getPrice();

            $product->original_price = $metadata->getOriginalPrice();

        }

        if(!$source) {

            $product->sources()->create([
                'product_id' => $product->id,
                'merchant_id' => $user->id,
                'source' => $url
            ]);

        }
        
        $product->save();

        return $product;
    }
    


    /**
     * @param $product Product
     * @param ? $user \App\Entities\User
     * @return boolean
     */
    public function refreshFeaturedMediaForProduct($product, $user = null)
    {
        $media = $product->firstMedia('photo');

        $product->media_id = $media ? $media->id : null;

        $product->media_count = $product->media()->count();

        return $product->save();
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

        $media = $product->uploadPhoto($file, 'photo');

        $this->refreshFeaturedMediaForProduct($product);

        return $media;
    }
    

    /**
     * @param $product_id integer
     * @param $media_id integer
     * @param ? $user \App\Entities\User
     * @return \App\Entities\Media
     */
    public function removeMediaFromProductById($product_id, $media_id, $user = null)
    {
        $user = $user ? : auth()->user();

        $product = $user->products()->findOrFail($product_id);

        $media = $product->media()->findOrFail($media_id);
        
        $deleted = $media->delete();

        $this->refreshFeaturedMediaForProduct($product);

        return $deleted;
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

        $this->refreshFeaturedMediaForProduct($product);
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

        if($liked) {
            event(new ProductLiked($product, $user));
        } else {
            event(new ProductDisliked($product, $user));
        }

        $product->likes_count = $product->getActivities('product:liked')->count();
        
        $product->save();
        
        return $liked;
    }


    /**
     * Tranform text into tokens
     *
     * @return array
     */
    public function similar($product)
    {
        $hash = md5('similar'.$product->id.auth()->id());

        return Cache::remember($hash, (60 * 24), function () use ($product) {

            $user = auth()->user();

            $ids = capsule('reco')->forProduct($product, $user)->get();

            return $this->model->whereIn('id', $ids)->active()->take(5)->get();

        });
        
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

        return $this->model->whereIn('id', $ids)->active()->orderByRaw(DB::raw("FIELD(id, $ids_ordered)"));
    }


    /**
     * Sync variants with product.
     *
     * @param $variants string(json)
     * @param $product Product
     */
    public function syncVariants($variants, Product $product)
    {
        if(is_string($variants)){
            $variants = json_decode($variants, 1);
        }

        $variants = collect($variants);

        $models = $variants->map(function ($variant) use ($product) {
            return $this->updateOrCreateVariant($variant, $product);
        });

        if($models->count()) {
            $product->has_variants = true;
            $product->price = $models->min('price');
            $product->original_price = $models->min('original_price');
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
            'name' => array_get($variant, 'name'),
            'original_price' => array_get($variant, 'original_price'),
            'price' => array_get($variant, 'price'),
            'in_stock' => array_get($variant, 'in_stock', 1),
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
        if(is_string($tags)){

            $tags = json_decode($tags);

            $tags = collect($tags)->pluck('text');

        } else {

            $tags = collect($tags);

        }

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
        $name = trim($tag);

        $model = ProductTag::where('name', $name)->where('product_id', $product->id)->first();

        if(!$model) {
            $model = ProductTag::create([
                'name' => $name,
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
    public function getWithShippingByCity($id, $params)
    {
        $product = $this->model->findOrFail($id);

        $product->load('owner.shippingPrices');

        if(array_get($params, 'variant')){

            $variant = ProductVariant::where('product_id', $product->id)->findOrFail(array_get($params, 'variant'));

            $product->price = $variant->price;

        }

        $prices = $product->owner->shippingPrices;

        $cities = $product->owner->country()->first()->cities;

        $cities->load('translations');

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
                $n = $shipping['window_from'] == $shipping['window_to'] 
                ? "{$shipping['window_from']}" 
                : "{$shipping['window_from']}-{$shipping['window_to']}";

                $shipping_text =  $shipping['window_from'] == $shipping['window_to']
                    ? __(":n day", compact('n'))
                    : __(":n days", compact('n'));
                
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
            'merchant_id' => $product->owner_id,
            'city_id' => $user->city_id,
            'phone' => $user->phone,
            'title' => $product->title,
        ]);

        $order->notify(new OrderStatusChanged());

        event(new OrderCreated($order, $user));

        if($product->has_variants) {

            $variant->decrement('in_stock', $quantity);

            $variant->update();

        } else {

            $product->decrement('in_stock', $quantity);

        }

        $product->increment('sales_count', $quantity);

        $product->update();

        return $order;
    }


    /**
     * Sync categoires from json file with database
     *
     * @return void
     */
    public function syncCategories()
    {
        $categories = json_decode(file_get_contents(resource_path('docs/categories.json')), 1);

        foreach ($categories as $key => $value) {

            $category = $this->updateOrCreateCategory($value, $key);

            foreach ($value['subcategories'] as $key => $value) {

                $this->updateOrCreateCategory($value, $key, $category);

            }
        }
    }


    /**
     * Update or create category
     *
     * @param $params array
     * @param $key int
     * @param $parent ProductCategory
     *
     * @return ProductCategory
     */
    public function updateOrCreateCategory($params, $key, $parent = null)
    {
        $category = ProductCategory::whereTranslation('name', array_get($params, 'name:en'), 'en')->first();

        if(!$category) {
            $category = ProductCategory::create([
                'icon' => array_get($params, 'icon'),
                'name:en' => array_get($params, 'name:en'),
                'order' => $key,
                'parent_id' => $parent ? $parent->id : null
            ]);
        }

        $category->update(array_filter([
            'name:pl' => array_get($params, 'name:pl'),
            'name:ka' => array_get($params, 'name:ka'),
            'order' => $key
        ], 'strlen'));

        return $category;
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
    public function hide($id)
    {
        $user = auth()->user();

        $product = $user->products()->findOrFail($id);

        $product->markAsInactive();

        event(new ProductDisabled($product, $user));
        
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
        if (app()->runningInConsole()){

            $user = User::find(1);
            
            $product = $this->model->findOrFail($id);

        } else {

            $user = auth()->user();

            $product = $user->products()->findOrFail($id);

        }

        event(new ProductDeleted($product, $user));

        $product->comments()->delete();
        
        $product->media()->delete();

        $product->meta()->delete();

        $product->activities()->delete();

        $product->sources()->delete();

        $product->delete();
    }


    /**
     * Get all products by user.
     *
     * @return Product
     */
    public function indexStock()
    {
        $user = auth()->user();

        return $this->model->where('owner_id', $user->id)->latest('id')->paginate(20);
    }


    /**
     * Delete all inactive products
     *
     * @return boolean
     */
    public function cleanStorage()
    {
        $yesterday = Carbon::now()->subDays(1);

        return $this->model->where('is_active', false)->whereNull('slug')->whereNull('media_id')->where('created_at', '<=', $yesterday)->delete();
    }


    /**
     * Refresh analytics data from GA
     *
     * @return boolean
     */
    public function updateAnalytics()
    {
        $metrics = app(AnalyticsService::class)->getBasicAnalyticsForPopularProducts();

        return $metrics->map(function($metric) {
            return $this->findByIdAndSetAnalytics(...$metric);
        });
    }


    /**
     * Refresh analytics data from GA
     *
     * @return boolean
     */
    public function findByIdAndSetAnalytics($slug, $username, $data)
    {
        $product = $this->model->where('slug', $slug)->where('owner_username', $username)->first();

        if(!$product) {
            return false;
        }

        foreach ($data as $key => $value) {

            if($key == 'views') {
                $product->increment('views_count', $value);
            }
            
        }

        $product->save();

    }

}