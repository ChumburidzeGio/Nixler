<?php

namespace App\Repositories;

use App\Repositories\BaseRepository;
use App\Entities\ProductCategory;
use App\Services\RecommService;
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
use App\Events\ProductDisabled;
use App\Events\ProductLiked;
use App\Events\ProductDisliked;
use Carbon\Carbon;
use Ayeo\Price\Price;
use Illuminate\Support\Facades\Cache;
use App\Crawler\Crawler;
use App\Crawler\Model as CrawlerModel;
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
     * Create new product
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = auth()->user();

        //$model = $this->model->where([
        //    'owner_id' => $user->id
        //])->latest('id')->first();

        //if($model && !$model->is_active && !$model->in_stock) {
        //    return $model;
        //}
        
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
    public function publish($product, $user)
    {
        $product->markAsActive();

        return $product;
    }
    
    /**
     * Import product from url
     *
     * @return void
     */
    public function import($link)
    {
        $patternPath = app(Crawler::class)->findPattern($link);

        $pattern = app($patternPath)->parse($link);

        if($pattern->getAvailableParams())
        {
            foreach ($pattern->getAvailableParams() as $param) 
            {
                $model = app(CrawlerModel::class)->setPattern($pattern->withParam($param));

                $this->importFromCrawler($model);
            }
        }
        else
        {
            $model = (new CrawlerModel)->setPattern($pattern);

            $this->importFromCrawler($model);
        }
    }
    
    /**
     * Save crawler model to database
     *
     * @return void
     */
    public function importFromCrawler($model)
    {
        if(!$model)
        {
            return null;
        }

        $source = ProductSource::firstOrNew([
            'merchant_id' => auth()->id(),
            'source' => $model->source,
            'params' => $model->param
        ]);

        if($source->created_at) 
        {
            return null;
        } 

        $product = $this->create();

        $this->fillProductFromCrawler($product, $model);

        $product->save();

        $product->markAsActive();
        
        $source->status = 'success';

        $source->product_id = $product->id;

        $source->save();
    }
    
    /**
     * Fill product with data from crawler
     *
     * @return void
     */
    public function fillProductFromCrawler($product, $metadata)
    {
        $this->syncVariants($metadata->variants, $product);

        $this->syncTags($metadata->tags, $product);

        foreach ($metadata->media as $media) 
        {
            $product->uploadPhoto($media, 'photo');
        }

        $this->refreshFeaturedMediaForProduct($product);

        $product->fill([
            'title' => $metadata->title,
            'description' => $metadata->description,
            'category_id' => $metadata->category,
            'target' => $metadata->target,
            'sku' => $metadata->sku,
        ]);

        if(!$product->has_variants){

            $product->price = $metadata->price;

            $product->original_price = $metadata->originalPrice;

        }
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
     * Prepare product for editing
     *
     * @return void
     */
    public function sortMedia($attribute, $product)
    {
        $media_sorted = json_decode($attribute);

        if($media_sorted && is_array($media_sorted))
        {
            $media = $product->getMedia('photo');

            $media = $media->sortBy(function ($photo, $key) use ($media_sorted) 
            {
                foreach ($media_sorted as $key => $value) 
                {
                    if(isset($value->id) && $value->id == $photo->id)
                    {
                        return $key;
                    }
                }
            });

            $product->syncMedia($media, 'photo');
        }

        $this->refreshFeaturedMediaForProduct($product);
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

            $tags = collect($tags)->pluck('type', 'text');

        } else {

            $tags = collect($tags);

        }

        $ids = $tags->map(function ($key, $tag) use ($product) {

            $model = $this->updateOrCreateTag($tag, $product, $key);

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
    public function updateOrCreateTag($tag, Product $product, $key = null)
    {  
        $name = trim($tag);

        $model = ProductTag::where('name', $name)->where('product_id', $product->id)->first();

        if(!$model) 
        {
            $model = ProductTag::create([
                'name' => $name,
                'user_id' => $product->owner_id,
                'product_id' => $product->id
            ]);
        }

        if(in_array($key, ['color', 'category', 'silhouetteCode']) && $model->type !== $key) {

            $model->update([
                'type' => $key
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
    public function getCitiesWithShipping($user)
    {
        $prices = $user->shippingPrices;

        $cities = $user->country()->first()->cities;

        $cities->load('translations');

        return $cities->transform(function($city) use ($prices) {

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
     * Delete all inactive products
     *
     * @return boolean
     */
    public function cleanStorage()
    {
        $yesterday = Carbon::now()->subDays(1);

        return $this->model->where('is_active', false)->whereNull('slug')->whereNull('media_id')->where('created_at', '<=', $yesterday)->delete();
    }

}