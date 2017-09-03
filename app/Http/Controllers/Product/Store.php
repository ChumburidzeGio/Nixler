<?php

namespace App\Http\Controllers\Product;

use App\Repositories\ProductRepository;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProduct;
use App\Entities\Product;

class Store extends Controller
{
    /**
     * Update product with request data
     *
     * @param  int  $id
     * @return Response
     */
    public function __invoke($id, UpdateProduct $request)
    {
        $product = Product::where('owner_id', $request->user()->id)->findOrFail($id);

        $repository = app(ProductRepository::class);

        $repository->sortMedia($request->media, $product);

        $repository->syncVariants($request->variants, $product);

        $repository->syncTags($request->tags, $product);

        $product->fill([
            'title' => $request->title,
            'description' =>$request->description,
            'category_id' =>$request->category,
            'buy_link' =>$request->buy_link,
            'is_used' =>$request->input('is_used', 0),
            'sku' =>$request->sku,
        ]);

        if(!$product->has_variants)
        {
            $product->price = $request->price;

            $product->in_stock = $request->in_stock;
        }

        $product->save();
        
        if($request->action == 'publish' && $this->authorize('create', $product)) 
        {
            $product->markAsActive();
        } 
        else 
        {
            $product->markAsInactive();
        }

        $isPublish = ($request->action == 'publish');
        
        $status = $isPublish ? 
            __('Your product has been saved, you can anytime publish it from this page or "My Products" section.') : 
            __('Your product has been updated and is now live. Do you want to add another one or go to product page?');

        return redirect()->route('product.edit', ['id' => $id])->with('status', $status)->with('buttons', $isPublish);
    }
}