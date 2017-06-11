<?php

namespace App\Upgrade;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Entities\Product;
use App\Notifications\ProductUpdated;
use App\Services\RecommService;

class AIC
{
    /**
     * Upgrade system to version 1.93
     *
     * Fix issue with sending data to recombee.
     * Fix issue with adding tags to product in recombee.
     * Remove empty items from recombee.
     * Add dot in footer links and change color of year
     * Add useful links to product adding page
     * Remove hidden overflow from product title
     * Fix issue with getting recommendations from recombee
     * Fix issue with wrong angular controller on orders page
     * Add ability to save SKU for products
     * Sort products in "stock" by id desc
     * Edit phone number on about us page to  Tikos one
     *
     * @return void
     */
    public function upgrade()
    {
    	Product::active()->get()->map(function($product){
            $product->notify(new ProductUpdated);
        });

        app(RecommService::class)->removeEmptyItems();
    }

}