<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Faker\Factory;
use stdClass;

class UserTest extends DuskTestCase
{
    /**
     * Test user registration, login and forgot password
     * 
     * @return void
     */
    public function testAuth()
    {
        $this->browse(function ($browser) {

            $faker = Factory::create();

            $email = $faker->email;

            $browser->visit('/')
            ->click('#register')
            ->assertSee('Register')
            ->value('#name', $faker->name)
            ->value('#email', $email)
            ->value('#password', $email)
            ->value('#password-confirm', $email)
            ->click('form button')
            ->assertSee('message');

            $browser->click('#menu')
            ->waitFor('#logout')
            ->pause(500)
            ->press('#logout');

            $browser->visit('/')
            ->click('#login')
            ->click('#password-reset')
            ->assertSee('Reset Password')
            ->value('#email', $email)
            ->click('form button')
            ->assertSee('We have e-mailed your password reset link!');

            $browser->visit('/')
            ->click('#login')
            ->assertSee('Sign in')
            ->value('#email', $email)
            ->value('#password', $email)
            ->click('form button')
            ->assertSee('message');

        });
    }

    /**
     * Test account settings
     * 
     * @return void
     */
    public function testSettings()
    {
        $this->browse(function ($browser) {

            $faker = Factory::create();

            $browser->visit('/settings/account')
            ->value('#name', 'TestUserName')
            ->value('#username', rand(43452345, 1324321432551))
            ->value('#email', $faker->email)
            ->click('#city')
            ->click('#city ul li:not(.selector-optgroup)')
            ->value('#website', $faker->url)
            ->value('#headline', $faker->sentence())
            ->value('#phone', rand(574711000, 574999999))
            ->click('form button')
            ->assertInputValue('#name', 'TestUserName')
            ->assertSee('Confirmation code');

        });
    }

    /**
     * Test merchant settings
     *
     * @return void
     */
    public function testMerchantSettings()
    {
        $this->browse(function ($browser) {

            $faker = Factory::create();

            $browser->visit('/new-product');

            $policy = $faker->text;
            $city = $faker->text;

            $browser->click('#shipping_settings_route')
            ->click('#delivery_full')
            ->click('#has_return')
            ->value('#policy', $policy)
            ->press('#update')
            ->assertSee($policy)
            ->click('#add_new_form #city')
            ->click('#add_new_form #city ul li:not(.selector-optgroup)');

            $city = $browser->value("#add_new_form input[name='location_id']");

            $browser->type("#add_new_form input[name='price']", 12)
            ->type("#add_new_form #add_window_from", 1)
            ->type("#add_new_form #add_window_to", 1)
            ->press('#add_new_form button')
            ->assertSee($city);

        });
    }


    /**
     * Test product adding
     *
     * @return void
     */
    public function testProductAdding()
    {
        $this->browse(function ($browser) {

            $faker = Factory::create();

            $browser->visit('/new-product')
            ->assertSee('Publish')
            ->value('#title', $faker->text(120).'TestProductName')
            ->click('#category')
            ->click('#category ul li:not(.selector-optgroup)')
            ->value('#description', $faker->text)
            ->type('#price', rand(1, 400))
            ->type('#in_stock', rand(1,900))
            ->click('#add-variants')
            ->type('#variants [ng-model="variant.name"]:nth-child(1)', $faker->word)
            ->type('#variants [ng-model="variant.price"]', rand(1, 400))
            ->type('#variants [ng-model="variant.in_stock"]', rand(1, 900))
            ->attach('#picker-input', public_path('/img/aside.jpg'))
            ->pause(1000)
            ->press('#publish')
            ->assertSee('Your product has been updated and is now live');

        });
    }


    /**
     * Test product searching
     *
     * @return void
     */
    public function testSearch()
    {
        $this->browse(function ($browser) {
           $browser->visit('/')
               ->value('#search', 'Test')
               ->keys('#search', ['{enter}']);
               //->assertSee('TestUserName')
               //->assertSee('TestProductName');
       });
    }

}
