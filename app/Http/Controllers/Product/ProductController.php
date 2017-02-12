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
        	'https://thingd-media-ec5.thefancy.com/default/1343516017304797699_2fe8c0cdcd4d.jpg',
        	'https://resize-ec3.thefancy.com/resize/crop/952/thingd/default/163906907386420176_c872f9138bc9.jpg',
        	'https://resize-ec3.thefancy.com/resize/crop/952/thingd/default/1280451873878318097_a3764df1c69e.jpg',
        	'https://thefancy-media-ec4.thefancy.com/310/20160602/1169742302369811513_ac1d181c9eb8.jpg',
        ]]);
    }
}
