<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Faker\Factory;
use Tests\Browser\Pages\Register;
use Tests\Browser\Pages\Login;
use Tests\Browser\Pages\AccountSettings;
use Tests\Browser\Pages\MerchantSettings;
use Tests\Browser\Pages\ProductUpdate;
use Tests\Browser\Pages\Product;

class GeneralTest extends DuskTestCase
{
    /**
     * Test system if everyhting works
     * 
     * @return void
     */
    public function testGeneral()
    {
       /* $this->browse(function ($browser) {

            $email = $this->faker('email');

            $browser->visit(new Register)
            ->createAccount($email);
            
            $this->logout($browser);

            $browser->visit(new Login)
            ->forgotPassword($email);

            $browser->visit(new Login)
            ->loginUser($email);

            $browser->visit(new MerchantSettings)
            ->updageGeneralSettings()
            ->addNewLocation();

            $browser->visit(new ProductUpdate('/new-product'))
            ->createProduct();

            $link = $browser->attribute('@product-link', 'href');

            $browser->visit(new Product($link))
                ->like()
                ->comment();

        });*/
    }

    /**
     * Test merchant settings
     *
     * @return void
     */
    public function logout($browser)
    {
        /*$browser->click('#menu')
            ->waitFor('#logout')
            ->pause(500)
            ->press('#logout');*/
    }

}
