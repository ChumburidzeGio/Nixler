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
use Modules\Product\Entities\Category;
use Modules\Stream\Console\Personalize;
use DB;
use Cviebrock\EloquentTaggable\Models\Tag;
use Modules\Stream\Entities\Activity;
use Carbon\Carbon;
use Modules\Stream\Services\KeenService;
use Modules\Stream\Services\RecommService;
use Modules\User\Entities\User;

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
     * Get all product from stream
     *
     * @return \Illuminate\Http\Response
     */
    public function all($cat = array('*'))
    {
        $user = auth()->user();

        if(is_string($cat)){
            $products = $this->filter($cat);
        } elseif($user) {
            $products = $user->stream()->with('firstPhoto', 'owner')->latest()->paginate(20);
        } else {
            $products = $this->featuredProducts();
        }

        $manager = new Manager();

        $resource = new Collection($products, new ProductTransformer());

        $resource->setPaginator(new IlluminatePaginatorAdapter($products));

        return $manager->createData($resource);
    }


    /**
     * Filter products by category
     *
     * @return \Illuminate\Http\Response
     */
    public function filter($cat)
    {
        $cats = Category::where('id', $cat)->orWhere('parent_id', $cat)->pluck('id')->toArray();

        return $this->model->with('firstPhoto', 'owner')->where('status', 'active')->whereIn('category_id', $cats)->latest()->paginate(20);
    }


    /**
     * Search in products
     *
     * @return \Illuminate\Http\Response
     */
    public function search($query, $cat)
    {
        $products = $this->model->search($query)->where('status', 'active');

        if($cat){

            $cats = Category::where('id', $cat)->orWhere('parent_id', $cat)->pluck('id')->toArray();

            $ids = $products->get()->pluck('id')->toArray();
            $ids_ordered = implode(',', $ids);

            $products = $this->model->whereIn('category_id', $cats)->whereIn('id', $ids)->orderByRaw(DB::raw("FIELD(id, $ids_ordered)"));

        }

        $products = $products->paginate(20);

        $products->load('firstPhoto', 'owner');

        $manager = new Manager();

        $resource = new Collection($products, new ProductTransformer());

        $resource->setPaginator(new IlluminatePaginatorAdapter($products));

        return $manager->createData($resource);
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
     * Recomment products to user
     *
     * @return \Illuminate\Http\Response
     */
    public function recommend($user)
    {
        $recommendations = (new RecommService)->recommendations($user->id, 50, [
            'filter' => "'currency' == \"{$user->currency}\""
        ]);

        //$user->streamRemoveBySource('recs');

        $user->pushInStream($recommendations, 'recs');
    }


    /**
     * Push new recommendations in streams of recetly active users
     *
     * @return \Illuminate\Http\Response
     */
    public function refreshStreams()
    {
        $models = User::whereHas('sessions', function($q){
            return $q->whereBetween('updated_at', [Carbon::now()->subMinutes(10), Carbon::now()]);
        })->get();

        $models->map(function($model){
            $this->recommend($model);
        });
    }


    /**
     * Get most popular users
     *
     * @return \Illuminate\Http\Response
     */
    public function featuredAccounts($count = 6)
    {
        return Activity::join('products as p', 'activities.object', 'p.id')
                    ->whereIn('verb', ['product:viewed', 'product:liked'])
                    ->whereNotIn('owner_id', function($query){
                        $query->select('follow_id')->from('followers')->where('user_id', auth()->id());
                    })
                    ->where('owner_id', '<>', auth()->id())
                    ->groupBy('owner_id', 'owner_username')
                    ->orderBy(DB::raw('count(activities.object)'),'desc')
                    ->whereBetween('activities.created_at', [Carbon::now()->subWeek(), Carbon::now()])
                    ->take($count)
                    ->pluck('owner_username', 'owner_id');
    }



    /**
     * Get most popular users
     *
     * @return \Illuminate\Http\Response
     */
    public function featuredProducts($count = 6)
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
     * Discover new products
     *
     * @return \Illuminate\Http\Response
     */
    public function discover()
    {
        return $this->refreshStreams();
    }


    /**
     * Get categories for stream
     */
    public function categories($active)
    {
        if($active){
            $category = Category::find($active);

            if($category){
                $children = Category::where('parent_id', $category->id)->with('translations')->get();

                return $children->count() || !$category->parent_id 
                    ? $children 
                    : Category::where('parent_id', $category->parent_id)->with('translations')->get();
            }
        }

        return Category::whereNull('parent_id')->with('translations')->get();
    }
}