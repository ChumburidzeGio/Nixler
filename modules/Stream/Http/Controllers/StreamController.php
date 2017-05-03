<?php

namespace Modules\Stream\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\Category;
use Modules\Stream\Repositories\StreamRepository;

class StreamController extends Controller
{

    /**
     * @var PostRepository
     */
    protected $repository;

    public function __construct(StreamRepository $repository){
        $this->repository = $repository;
    }


    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        /*if(auth()->guest()){

            $this->seo()->setTitle(trans('landing.meta.title'));

            $this->seo()->setDescription(trans('landing.meta.description'));

            $this->seo()->opengraph()->setUrl(request()->fullUrl());
            
            $this->seo()->opengraph()->addProperty('type', 'website');

            $what = collect(trans('landing.what.items'))->chunk(2);

            $why = collect(trans('landing.why.items'))->chunk(3);

            $who = collect(trans('landing.who.items'))->chunk(4);

            return view('landing.page', compact('what', 'why', 'who'));
        }*/

        if($request->has('query')){

            $products = $this->repository->search($request->input('query'), $request->input('cat'));
            
            if(!$request->has('cat')){
                $users = $this->repository->searchUsers($request->input('query'));
            }

        } else {
            $products = $this->repository->all($request->input('cat'));
        }

        $categories = $this->repository->categories($request->input('cat'));

        return $request->isMethod('post') ? $products->toJson() : view('stream::index', compact('products', 'categories', 'users'));
    }

}
