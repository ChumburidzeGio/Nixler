<?php

namespace Modules\Stream\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Contracts\CacheableInterface;
use Prettus\Repository\Traits\CacheableRepository;
use Prettus\Validator\Contracts\ValidatorInterface;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Modules\Product\Transformers\ProductTransformer;
use Modules\Product\Entities\Product;
use Modules\Stream\Console\Personalize;
use DB;
use Cviebrock\EloquentTaggable\Models\Tag;

class StreamRepository extends BaseRepository implements CacheableInterface {

	use CacheableRepository;


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
     * Prepare product for editing
     *
     * @return \Illuminate\Http\Response
     */
    public function all($columns = array('*'))
    {
        $user = auth()->user();

        $products = $user->stream()->with('firstPhoto', 'owner')->paginate(20);

        if($products->count() < 9){
            $popular = Product::where('status', 'active')
                ->orderBy('likes_count', 'desc')
                ->orderBy('id', 'desc')->take(20)->pluck('id');

            $user->pushInStream($popular, 'popular');

            $products = $user->stream()->with('firstPhoto', 'owner')->paginate(20);
        }

        $manager = new Manager();

        $resource = new Collection($products, new ProductTransformer());

        $resource->setPaginator(new IlluminatePaginatorAdapter($products));

        return $manager->createData($resource)->toJson();
    }


    /**
     * Prepare product for editing
     *
     * @return \Illuminate\Http\Response
     */
    public function search($query)
    {
        $products = $this->model->search($query)->where('status', 'active')->paginate(20);

        $products->load('firstPhoto', 'owner');

        $manager = new Manager();

        $resource = new Collection($products, new ProductTransformer());

        $resource->setPaginator(new IlluminatePaginatorAdapter($products));

        return $manager->createData($resource)->toJson();
    }


    /**
     * Prepare product for editing
     *
     * @return \Illuminate\Http\Response
     */
    public function discover()
    {
        $user = auth()->user();

        $products = (new Personalize)->handle();

        return compact('products');
    }
}