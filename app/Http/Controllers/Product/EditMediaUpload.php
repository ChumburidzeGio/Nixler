<?php

namespace App\Http\Controllers\Product;

use App\Repositories\ProductRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EditMediaUpload extends Controller
{
    /**
     * Upload media for product
     *
     * @param  int  $id
     * @return Response
     */
    public function __invoke($id, Request $request)
    {
        $product = $request->user()->products()->findOrFail($id);

        $media = $product->uploadPhoto($request->file('file'), 'photo');

        app(ProductRepository::class)->refreshFeaturedMediaForProduct($product);

        return [
            'success' => true,
            'id' => $media->id,
            'thumb' => $media->photo('thumb')
        ];
    }
}