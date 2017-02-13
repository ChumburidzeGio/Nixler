<?php

namespace App\Http\Controllers\Product;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    /**
     * Show the product page
     *
     * @return \Illuminate\Http\Response
     */
    public function find()
    {
        return view('product.item', ['product' => auth()->user(),'user' => auth()->user(), 'photos' => [
        	'https://images.unsplash.com/photo-1466684921455-ee202d43c1aa?dpr=1&auto=format&fit=crop&w=1000&h=1000&q=80&cs=tinysrgb&crop=',
        	'https://images.unsplash.com/photo-1457168722771-cbf2d6281ff5?dpr=1&auto=format&fit=crop&w=1000&h=1000&q=80&cs=tinysrgb&crop=',
        	'https://images.unsplash.com/photo-1483118714900-540cf339fd46?dpr=1&auto=format&fit=crop&w=1000&h=1000&q=80&cs=tinysrgb&crop=',
        	'https://images.unsplash.com/1/irish-hands.jpg?dpr=1&auto=format&fit=crop&w=1000&h=1000&q=80&cs=tinysrgb&crop=',
        ]]);
    }
}
