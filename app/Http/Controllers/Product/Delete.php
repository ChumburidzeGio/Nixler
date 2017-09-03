<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Events\ProductDeleted;
use Illuminate\Http\Request;
use App\Entities\Product;

class Delete extends Controller
{
    /**
     * Delete the product
     *
     * @param  int  $id
     * @return Response
     */
    public function __invoke($id, Request $request)
    {
        $product = $request->user()->products()->findOrFail($id);

        event(new ProductDeleted($product, $request->user()));

        $product->delete();

        return redirect('/');
    }
}