<?php

namespace Modules\Product\Observers;

use Modules\Product\Entities\Product;
use Modules\Stream\Services\RecommService;

class ProductObserver
{
    /**
     * Listen to the Product created event.
     *
     * @param  Product  $product
     * @return void
     */
    public function created(Product $product)
    {
        (new RecommService)->addProduct($product);
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