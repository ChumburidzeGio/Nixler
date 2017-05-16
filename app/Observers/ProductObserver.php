<?php

namespace App\Observers;

use App\Entities\Product;
use App\Services\RecommService;
use App\Notifications\ProductPublished;
use App\Notifications\ProductUpdated;

class ProductObserver
{
    /**
     * Listen to the Product created event.
     *
     * @param  Product  $product
     * @return void
     */
    public function saved(Product $product)
    {
        if($product->is_active){
            $product->notify(new ProductUpdated);
        }
    }

    /**
     * Listen to the Product created event.
     *
     * @param  Product  $product
     * @return void
     */
    public function updating(Product $product)
    {
        if (!$product->getOriginal('is_active') && $product->is_active) {
            $product->notify(new ProductPublished());
        }
    }

    /**
     * Listen to the Product deleting event.
     *
     * @param  Product  $product
     * @return void
     */
    public function deleting(Product $product)
    {
        (new RecommService)->removeProduct($product);
    }
}