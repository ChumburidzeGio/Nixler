<?php

namespace Modules\Stream\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Product\Entities\Product;
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
        if($request->has('query')){
            $products = $this->repository->search($request->input('query'), $request->input('cat'));
        } else {
            $products = $this->repository->all($request->input('cat'));
        }

        $categories = [
            1 => ['icon' => 'wc', 'name' => 'Clothing & Accessories'],
            5 => ['icon' => 'child_friendly', 'name' => 'Kids & Babe'],
            13 => ['icon' => 'phone_iphone', 'name' => 'Electronics'],
            22 => ['icon' => 'laptop_mac', 'name' => 'Computers'],
            30 => ['icon' => 'directions_car', 'name' => 'Vehicles'],
            36 => ['icon' => 'domain', 'name' => 'Real estate'],
            44 => ['icon' => 'home', 'name' => 'Home'],
            52 => ['icon' => 'spa', 'name' => 'Beauty & Healthcare'],
            58 => ['icon' => 'fitness_center', 'name' => 'Sport & Leisure'],
            64 => ['icon' => 'card_giftcard', 'name' => 'Spare time & Gifts'],
            73 => ['icon' => 'pets', 'name' => 'Pets'],
            81 => ['icon' => 'restaurant', 'name' => 'Food'],
        ];

        return $request->isMethod('post') ? $products->toJson() : view('stream::index', compact('products', 'categories'));
    }


    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function discover(Request $request)
    {
         return $this->repository->discover();
    }


    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('stream::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('stream::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('stream::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }
}
