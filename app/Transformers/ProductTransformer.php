<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\Product;

class ProductTransformer extends TransformerAbstract
{
    public function transform(Product $product)
    {
        return [
            'id'      => (int) $product->id,
            'title'   => $product->title,
            'url'   => $product->url(),
            'price' => $product->currency . ' ' . $product->price,
            'likes_count' => $product->likes_count,
            'owner' => $product->owner->name,
            'photo' => route('photo', [
            	'id' => array_get($product->firstPhoto->first(), 'id', '-'),
            	'type' => 'product',
            	'place' => 'short-card'
            ])
        ];
    }
}