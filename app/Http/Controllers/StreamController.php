<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Entities\Product;
use App\Entities\ProductCategory;
use App\Repositories\StreamRepository;
use App\Repositories\ProductRepository;

class StreamController extends Controller
{

    /**
     * @var PostRepository
     */
    protected $repository;
    protected $productRepository;

    public function __construct(StreamRepository $repository, ProductRepository $productRepository){
        parent::__construct();
        $this->repository = $repository;
        $this->productRepository = $productRepository;
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

        if($request->has('query')){

            $result = $this->productRepository->search($request->all());
            
            $products = array_get($result, 'products');

            $facets = array_get($result, 'facets');

            if(!$request->has('cat')){
                $users = $this->repository->searchUsers($request->input('query'));
            }

        } else {
            $products = $this->productRepository->getUserStream($request->input('cat'));
        }

        $categories = $this->productRepository->getProductCategories($request->input('cat'));

        return $request->isMethod('post') ? $products->toJson() : view('stream.index', compact('products', 'categories', 'users', 'facets'));
    }

}
