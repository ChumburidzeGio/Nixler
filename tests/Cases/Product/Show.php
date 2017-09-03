<?php

namespace Tests\Cases\Product;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use App\Entities\Product;

class Show extends DuskTestCase
{
    /**
     * Test system if everyhting works
     * 
     * @return void
     */
    public function testShow()
    {
        $this->browse(function ($browser) {

            $product = Product::latest()->active()->first();

            $browser->visit($product->url())
                    ->assertVisible('#product-gallery');

        });
    }

}
