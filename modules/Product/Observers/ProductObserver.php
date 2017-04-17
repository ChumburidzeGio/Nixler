<?php

namespace Modules\Product\Observers;

use Modules\Product\Entities\Product;
use Modules\Stream\Services\RecommService;
use Modules\Product\Notifications\ProductPublished;

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

            (new RecommService)->addProduct($product);

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