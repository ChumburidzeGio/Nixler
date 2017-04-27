<?php

namespace Modules\Product\Repositories;

use App\Repositories\BaseRepository;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use Modules\Product\Transformers\ProductTransformer;
use Modules\Product\Entities\Category;
use Modules\Stream\Services\RecommService;
use Illuminate\Container\Container as App;
use Modules\Product\Repositories\TagRepository;

class ProductRepository extends BaseRepository {

    private $tags;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function __construct(App $app, TagRepository $tags)
    {
        parent::__construct($app);

        $this->tags = $tags;
    }

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return "Modules\\Product\\Entities\\Product";
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

        $product->fill([
            'title' => array_get($attributes, 'title'),
            'description' => array_get($attributes, 'description'),
            'price' => array_get($attributes, 'price'),
            'category' => array_get($attributes, 'category'),
            'in_stock' => array_get($attributes, 'in_stock'),
        ]);

        //Media
        $this->sortMedia(array_get($attributes, 'media'), $product);

        //Variants & Tags
        $variants = collect(json_decode(array_get($attributes, 'variants'), 1))->flatten();
        $product->addTags($variants, 'variants');

        $tags = collect(json_decode(array_get($attributes, 'tags'), 1))->flatten();
        $product->addTags($tags, 'tags');


        $product->save();

        if(array_get($attributes, 'action') == 'publish') {
            $product->markAsActive();
        }

        return $product;
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
        $ids = (new RecommService)->similar($id, 5, auth()->id());
        
        return $this->model->whereIn('id', $ids)->with('firstPhoto')->take(5)->get();
    }

}