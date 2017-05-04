<?php

namespace Modules\Product\Repositories;

use App\Repositories\BaseRepository;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use Modules\Product\Transformers\ProductTransformer;
use Modules\Product\Entities\Category;
use Modules\Stream\Services\RecommService;
use Illuminate\Container\Container as App;
use Modules\Product\Entities\Product;
use Auth;

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
            abort_if(auth()->guest() || auth()->user()->cannot('view', $product), 403);
        }

        $product->load('owner.shippingPrices', 'category');
        $product->owner->shippingPrices->load('location');
        $product->setRelation('media', $product->media('photo')->take(10)->get());
        $product->setRelation('comments', $product->comments()->sortBy('most_recent')->paginate());
        $product->setRelation('tags', $product->tags('tags')->take(3)->get());
        $product->setRelation('variants', $product->tags('variants')->get());
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

        $this->calculateShippingPriceForProduct($product);

        return $product;
    }
    


    /**
     * @param $product \Modules\Product\Entities\Product
     * @return \Modules\Product\Entities\Product
     */
    public function calculateShippingPriceForProduct($product)
    {
        if(auth()->guest()) {
            $product->owner->shippingPrices->take(2);
            return $product;
        }

        $shipping_prices = $product->owner->shippingPrices;

        $user = auth()->user();

        $addresses = $user->addresses()->with('city')->get(['street', 'id', 'city_id', 'country_id']);

        $addresses = $this->calculateShippingPriceForEachAddress($addresses, $shipping_prices);

        $product->setRelation('addresses', $addresses);

        return $product;
    }
    


    /**
     * @param $addresses \Modules\Address\Entities\Address
     * @param $shipping_prices \Modules\Address\Entities\ShippingPrice
     * @return \Modules\Address\Entities\Address
     */
    public function calculateShippingPriceForEachAddress($addresses, $shipping_prices)
    {
        return $addresses->map(function($address) use ($shipping_prices) {

            $shipping = $shipping_prices->filter(function($item) use ($address) {
                return ($item->type == 'city' && $item->location_id == $address->city_id);
            });

            if(!$shipping->count()){
                $shipping = $shipping_prices->filter(function($item) use ($address) {
                    return ($item->type == 'country' && $item->location_id == $address->country_id);
                });
            }

            $address->shipping = $shipping->map(function($item){
                extract($item->toArray());
                return compact('price', 'currency', 'window_from', 'window_to');
            })->first();

            return [
                'id' => $address->id,
                'label' => $address->street,
                'shipping' => $address->shipping
            ];

        });
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

        $product->variants = collect($product->tags('variants')->get())->mapWithKeys(function ($item, $key) {
            return [$key => ['text' => $item->name]];
        })->toJson();

        $product->tags = collect($product->tags('tags')->get())->mapWithKeys(function ($item, $key) {
            return [$key => ['text' => $item->name]];
        })->toJson();

        $product->media = $product->getMedia('photo')->map(function($media){
            return [
                'success' => true,
                'id' => $media->id,
                'thumb' => url('media/'.$media->id.'/avatar/profile.jpg')
            ];
        })->toJson();

        $categories = Category::with('translations', 'children.translations')->orderBy('order')->get();

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

        if($user->cannot('create', $product)){
            return false;
        }

        $product->fill([
            'title' => array_get($attributes, 'title'),
            'description' => array_get($attributes, 'description'),
            'price' => array_get($attributes, 'price'),
            'category_id' => array_get($attributes, 'category'),
            'in_stock' => array_get($attributes, 'in_stock'),
            'buy_link' => array_get($attributes, 'buy_link'),
            'is_used' => array_get($attributes, 'is_used', 0),
        ]);

        //Media
        $this->sortMedia(array_get($attributes, 'media'), $product);

        //Variants & Tags
        $variants = collect(json_decode(array_get($attributes, 'variants'), 1))->flatten();
        $product->syncTags($variants, 'variants');

        $tags = collect(json_decode(array_get($attributes, 'tags'), 1))->flatten();
        $product->syncTags($tags, 'tags');


        $product->save();

        if(array_get($attributes, 'action') == 'publish') {
            $product->markAsActive();
        }

        return $product;
    }
    


    /**
     * @param $id integer
     * @param $file string|mixed
     * @param ? $user \Modules\User\Entities\User
     * @return \Modules\Media\Entities\Media
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
     * @return \Modules\Media\Entities\Media
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
     * Prepare product for editing
     *
     * @return \Illuminate\Http\Response
     */
    public function all($columns = array('*'))
    {
        $products = $this->model->where('status', 'active')->with('firstPhoto', 'owner')->get();

        $manager = new Manager();

        $resource = new Collection($products, new ProductTransformer());

        return $manager->createData($resource)->toJson();
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

}