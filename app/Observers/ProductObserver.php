<?php

namespace App\Observers;

use App\Entities\Product;
use App\Services\RecommService;
use App\Notifications\ProductPublished;
use App\Notifications\ProductUpdated;

class ProductObserver
{

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