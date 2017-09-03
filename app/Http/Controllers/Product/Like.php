<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Entities\Product;

class Like extends Controller
{
    /**
     * Like the product
     *
     * @param  int  $id
     * @return Response
     */
    public function __invoke($id, Request $request)
    {
        $product = Product::findOrFail($id);

        $liked = $product->toggleActivity('product:liked');

        if($liked) {
            event(new ProductLiked($product, $request->user()));
        } else {
            event(new ProductDisliked($product, $request->user()));
        }

        $product->likes_count = $product->getActivities('product:liked')->count();
        
        $product->save();
        
        return [
            'success' => $liked
        ];
    }
}