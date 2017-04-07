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
     * Prepare product for editing
     *
     * @return \Illuminate\Http\Response
     */
    public function all($columns = array('*'))
    {
        $user = auth()->user();

        $products = $user->stream()->with('firstPhoto', 'owner')->paginate(20);

        $manager = new Manager();

        $resource = new Collection($products, new ProductTransformer());

        $resource->setPaginator(new IlluminatePaginatorAdapter($products));

        return $manager->createData($resource);
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

        return $manager->createData($resource);
    }


    /**
     * Prepare product for editing
     *
     * @return \Illuminate\Http\Response
     */
    public function recommend($user)
    {
        $recommendations = (new RecommService)->recommendations($user->id, 30);

        $user->streamRemoveBySource('recs');

        $user->pushInStream($recommendations, 'recs');
    }


    /**
     * Prepare product for editing
     *
     * @return \Illuminate\Http\Response
     */
    public function refreshStreams()
    {
        $models = User::whereHas('sessions', function($q){
            return $q->whereBetween('updated_at', [Carbon::now()->subDay(), Carbon::now()]);
        })->get();

        $models->map(function($model){
            $this->recommend($model);
        });
    }


    /**
     * Prepare product for editing
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
     * Prepare product for editing
     *
     * @return \Illuminate\Http\Response
     */
    public function discover()
    {
        return $this->featuredAccounts();
        return $this->refreshStreams();

        $product = Product::first();

        return (new RecommService)->addProduct($product);
        //return request()->has('user_id') ? (new RecommService)->recommendations(request()->input('user_id'), 5) : 
           // (new RecommService)->similar(request()->input('pid'), 5);

        $verb = ['product:liked', 'product:purchased', 'product:viewed'];

        $activities = Activity::where('new', 1)->whereIn('verb', $verb)->get();

        return (new RecommService)->push($activities);

        $activities->load('mobject', 'mactor');

        $activities = $activities->map(function($item){
            return [
                'id' => $item->id,
                'verb' => $item->verb,
                'actor_id' => $item->mactor->id,
                'actor_country' => $item->mactor->country,
                'actor_locale' => $item->mactor->locale,
                'object_id' => $item->mobject->id,
                'object_price' => $item->mobject->price,
                'object_currency' => $item->mobject->currency,
                'object_category' => $item->mobject->category,
                'object_likes_count' => $item->mobject->likes_count,
            ];
        })->groupBy('verb')->toArray();


        Activity::where('new', 0)->whereIn('verb', $verb)->update([
          'new' => 0
        ]);

        return ;

        $query = Activity::select('object', DB::raw('count(activities.object) as total'))
                    ->whereIn('verb', ['product:viewed', 'product:liked'])
                    ->groupBy('object')
                    ->orderBy('total','desc')
                    ->whereBetween('activities.created_at', [Carbon::now()->subWeek(), Carbon::now()]);

        return [
            'products' => $query->pluck('object'),
            'users' => $query->join('products as p', 'activities.object', 'p.id')->addSelect('p.owner_id')
                    ->groupBy('owner_id')->get()
        ];
    }
}