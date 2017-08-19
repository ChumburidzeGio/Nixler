<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use App\Entities\User;
use App\Entities\Product;
use App\Entities\ProductVariant;

class OrderControllerTest extends DuskTestCase
{
    /**
     * Test system if everyhting works
     * 
     * @return void
     */
    public function testShow()
    { 
        $user = factory(User::class)->create();

        $merchant = factory(User::class)->create();

        $product = factory(Product::class)->create([
            'owner_id' => $merchant->id,
            'owner_username' => $merchant->username,
            'currency' => $merchant->currency
        ]);

        if(!rand(0,1)) 
        {
            $variants = factory(ProductVariant::class, rand(1, 100))->create([
                'product_id' => $product->id
            ]);

            $product->setRelation('variants', $variants);
        }

        $this->browse(function ($browser) use ($product, $user) {

            //Go to product page
            $browser->loginAs($user)->visit($product->url())->waitForText($product->title);

            //Click on purchase
            if($product->variants->count() > 0)
            {
                $browser->click('.visible-lg #variant > div')->click('.visible-lg #variant > div ul li:not(.selector-optgroup)');
            }

            $browser->click('.visible-lg #submit button')->waitForText($product->title); //HotFix to show on large screen

        });
    }

}
