<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Entities\Product;
use App\Entities\ProductCategory;
use App\Repositories\ProductRepository;
use App\Repositories\UserRepository;

class StreamController extends Controller
{

    /**
     * @var PostRepository
     */
    protected $productRepository;

    public function __construct(ProductRepository $productRepository){
        parent::__construct();
        $this->productRepository = $productRepository;
    }


    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function discover(Request $request)
    {
        return app(UserRepository::class)->updateStreams();
    }


    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        /*if(auth()->guest()){

            $what = collect(trans('landing.what.items'))->chunk(2);

            $why = collect(trans('landing.why.items'))->chunk(3);

            $who = collect(trans('landing.who.items'))->chunk(4);

            return view('landing.page', compact('what', 'why', 'who'));
        }*/

        if($request->has('query') || $request->has('cat')){

            $result = $this->productRepository->search($request->all());
            
            $products = array_get($result, 'products');

            $facets = array_get($result, 'facets');

            if(!$request->has('cat')){
                $users = app(UserRepository::class)->search($request->input('query'));
            }

        } else {
            $products = $this->productRepository->getUserStream();
        }

        $categories = $this->productRepository->getProductCategories($request->input('cat'));

        return $request->isMethod('post') ? $products->toJson() : view('stream.index', compact('products', 'categories', 'users', 'facets'));
    }

}
