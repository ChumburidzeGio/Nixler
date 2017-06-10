<?php

namespace App\Upgrade;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Entities\Product;
use App\Repositories\ProductRepository;
use App\Repositories\LocationRepository;
use App\Entities\Region;

class AIB
{
    /**
     * Upgrade system to version 1.92
     *
     * In this update we added SKU code to product, my products page to settings and replaced return policy with terms and conditions.
     * We also fixed some minor bugs and joined update console commands into one and added flag support to understand what to update exactly.
     *
     * @return void
     */
    public function upgrade()
    {
    	if (!Schema::hasColumn('products', 'sku')) {

            Schema::table('products', function (Blueprint $table) {
                $table->string('sku', 40)->nullable();
            });

        }
    }

}