<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Contracts\CacheableInterface;
use Prettus\Repository\Traits\CacheableRepository;
use Prettus\Validator\Contracts\ValidatorInterface;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use App\Transformers\ProductTransformer;
use App\Entities\Product;
use App\Entities\ProductCategory;
use App\Console\Personalize;
use DB;
use Cviebrock\EloquentTaggable\Models\Tag;
use App\Entities\Activity;
use Carbon\Carbon;
use App\Services\KeenService;
use App\Services\RecommService;
use App\Entities\User;

class StreamRepository extends BaseRepository implements CacheableInterface {

	use CacheableRepository;


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
     * Search in products
     *
     * @return \Illuminate\Http\Response
     */
    public function searchUsers($query)
    {
        return User::search($query)->take(6)->get();
    }


    /**
     * Discover new products
     *
     * @return \Illuminate\Http\Response
     */
    public function discover()
    {
        return $this->refreshStreams();
    }
}