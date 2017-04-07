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
            $products = $this->repository->search($request->input('query'));
            $accounts = [];
        } else {
            $products = $this->repository->all();
            $accounts = $this->repository->featuredAccounts(12);
        }

        return $request->isMethod('post') ? $products : view('stream::index', compact('products', 'accounts'));
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
