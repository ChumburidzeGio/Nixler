<?php

namespace Modules\Stream\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Product\Entities\Product;

class StreamController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $products = auth()->user()->stream()->take(20)->get();

        if($products->count() < 20){
            $popular = Product::where('status', 'active')
                ->orderBy('likes_count', 'desc')
                ->orderBy('id', 'desc')->take(20)->pluck('id');

            auth()->user()->pushInStream($popular, 'popular');

            $products = auth()->user()->stream()->take(20)->get();
        }

        return view('stream::index', compact('products'));
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
