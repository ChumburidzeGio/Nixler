<?php

namespace App\Http\Controllers\Product;

use App\Repositories\ProductRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EditMediaRemove extends Controller
{
    /**
     * Remove media from product
     *
     * @param  int  $id
     * @param  int  $media_id
     * @return Response
     */
    public function __invoke($id, $media_id, Request $request)
    {
        $product = $request->user()->products()->findOrFail($id);

        $media = $product->media()->findOrFail($media_id);
        
        $success = $media->delete();

        app(ProductRepository::class)->refreshFeaturedMediaForProduct($product);

        return compact('success');
    }
}