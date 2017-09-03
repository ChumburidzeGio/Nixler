<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Entities\Product;
use App\Entities\ProductCategory;

class Edit extends Controller
{
    /**
     * Show the edit page of given product
     *
     * @param  int  $id
     * @return Response
     */
    public function __invoke($id, Request $request)
    {
        $product = Product::where('owner_id', $request->user()->id)->findOrFail($id);

        capsule('frontend')->addJs($this->getJs($product));

        return view('products.edit', compact('product'));
    }
    
    /**
     * Get JS vars for product
     *
     * @return array
     */
    private function getJs(Product $product)
    {
        $description = old('description', $product->description);

        $category = old('category', $product->category_id);

        $variants = old("variants", $product->variants);

        $in_stock = old('in_stock', $product->in_stock);

        $tags = old('tags', $this->getTags($product));

        $media = old('media', $this->getMedia($product));

        $price = old('price', $product->price);

        $categories = $this->getCategories();

        $id = $product->id;

        $product = compact('description', 'category', 'variants', 'in_stock', 'tags', 'media', 'price', 'categories', 'id');

        return compact('product');
    }
    
    /**
     * Get product media
     *
     * @return Collection
     */
    private function getMedia(Product $product)
    {
        return $product->getMedia('photo')->map(function($media){
            return [
                'success' => true,
                'id' => $media->id,
                'thumb' => url('media/'.$media->id.'/avatar/profile.jpg')
            ];
        });
    }
    
    /**
     * Get product tags
     *
     * @return Collection
     */
    private function getTags(Product $product)
    {
        $tags = $product->tags()->get(['name', 'type']);

        return $tags->map(function($item){
            return [
                'text' => $item->name, 
                'type' => $item->type
            ];
        });
    }
    
    /**
     * Get categories
     *
     * @return Collection
     */
    private function getCategories()
    {
        $categories = ProductCategory::with('translations', 'children.translations')->whereNull('parent_id')->orderBy('order')->get();

        return $categories->map(function ($item) {
            return $item->children->map(function ($subitem) use ($item) {
                return [
                    'zone' => $item->name,
                    'id' => $subitem->id,
                    'label' => $subitem->name,
                ];
            });
        })->collapse();
    }
}