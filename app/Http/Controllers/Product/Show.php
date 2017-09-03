<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Entities\Product;

class Show extends Controller
{
    /**
     * Show the page of given product
     *
     * @param  string  $owner_username
     * @param  string  $slug
     * @return Response
     */
    public function __invoke($owner_username, $slug)
    {
        $product = Product::where(compact('slug', 'owner_username'))->firstOrFail();

        if(!$product->is_active) 
        {
            abort_if(auth()->guest() || auth()->user()->cannot('view', $product), 404);
        }

        $product->tags = $product->tags()->get();

        $product->setRelation('similar', $this->similar($product));

        capsule('frontend')->addJs($this->getJs($product));

        capsule('frontend')->addMeta($this->getMeta($product));

        return view('products.show', compact('product'));
    }
    
    /**
     * Get JS vars for product
     *
     * @return array
     */
    private function getJs(Product $product)
    {
        $liked = $product->isLiked();

        $media = $this->getMedia($product);

        $variants = $this->getVariants($product);

        $quantities = [1,2,3,4,5,6,7,8];

        $comments = capsule('comments')->whereTarget($product->id)->latest()->get()->items();

        $product = $product->setVisible(['id', 'price', 'likes_count', 'comments_count']);

        $product = array_merge(
            $product->toArray(), 
            compact('liked', 'quantities', 'variants', 'comments', 'media')
        );

        return compact('product');
    }
    
    /**
     * Get meta for product
     *
     * @return array
     */
    private function getMeta(Product $product)
    {
        $title = $product->title.' · '.$product->price_formated;

        $description = $product->description;

        $image = $product->photo('full');

        $type = 'product';

        return compact('title', 'description', 'image', 'type');
    }
    
    /**
     * Get media for product
     *
     * @return Collection
     */
    private function getMedia(Product $product)
    {
        return $product->media('photo')->get()->map(function($item, $key){
            return [
                'thumb' => $item->photo('thumb_s'),
                'full' => $item->photo('full'),
            ];
        });
    }
    
    /**
     * Get variants for product
     *
     * @return Collection
     */
    private function getVariants(Product $product)
    {
        $variants = $product->variants()->where('in_stock', '>', 0)->orderBy('price')->get();

        return $variants->map(function($item) use ($product) {
            return [
                'id' => $item->id,
                'name' => $item->name.' - '.money($product->currency, $item->price)
            ];
        });
    }

    /**
     * Get similar products
     *
     * @return Collection
     */
    private function similar($product)
    {
        return capsule('stream')->relevant('similar', [
            $product->id, 
            $product->owner_id,
            $product->title
        ])->whereNotIds([$product->id])->perPage(7)->get(false);

        /*$hash = md5('similar'.$product->id.auth()->id());

        return cache()->remember($hash, (60 * 24), function () use ($product) {

            $user = auth()->user();

            $ids = capsule('reco')->forProduct($product, $user)->get();

            return Product::whereIn('id', $ids)->active()->take(5)->get();

        });*/
    }
}