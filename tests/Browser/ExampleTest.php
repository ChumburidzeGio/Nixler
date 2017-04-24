<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Faker\Factory;
use stdClass;

class ExampleTest extends DuskTestCase
{
    /**
     * A basic browser test example.
     *
       * 1. User sign up
     * 2. Account settings
     * 3. Website and social links settings
     * 4. Change password
     * 5. Log out
     * 6. Sign in
     * 7. Email settings
     * 8. Phone settings
     * 9. Address settings
       * 10. Product adding
       * 11. Product searching
     * 12. Product full view 
     * 13. Comments on product
     * 14. Putting the order
     * 15. Updating profile picture and cover
     * 16. Follow button
     * 17. Sending message
        * 18. Shipping rules
     * 
     * @return void
     */
    public function testBasicExample()
    {
        $this->browse(function (Browser $browser) {

            $faker = Factory::create();

            $user = $this->signUp($browser, $faker);
            
            $this->updateShippingRules($browser, $faker);

            $product = $this->addProduct($browser, $faker);

            $this->searchProduct($browser, $product);

        });
    }


    /**
     * Test user sign up
     *
     * @return void
     */
    public function signUp($browser, $faker)
    {
        $user = new stdClass;
        $user->name = $faker->name;
        $user->email = $faker->email;
        $user->password = 'testjdq1e2';

        $browser->visit('/')
            ->click('#register')
            ->assertSee('Register')
            ->value('#name', $user->name)
            ->value('#email', $user->email)
            ->value('#password', $user->password)
            ->value('#password-confirm', $user->password)
            ->click('form button')
            ->assertSee('message');

        return $user;
    }


    /**
     * Test product adding
     *
     * @return void
     */
    public function updateShippingRules($browser, $faker)
    {
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

    }


    /**
     * Test product adding
     *
     * @return void
     */
    public function addProduct($browser, $faker)
    {
        $product = new stdClass;
        $product->title = $faker->text(120);
        $product->description = $faker->text;
        $product->price = rand(1, 400);
        $product->in_stock = rand(1,900);

        $browser->visit('/new-product')
        ->assertSee('Publish')
        ->value('#title', $product->title)
        ->click('#category')
        ->click('#category ul li:not(.selector-optgroup)')
        ->value('#description', $product->description)
        ->type('#price', $product->price)
        ->type('#in_stock', $product->in_stock)
        ->attach('#picker-input', public_path('/img/aside.jpg'))
        ->pause(1000)
        ->press('#publish')
        ->assertSee('Your product has been updated and is now live');

        return $product;
    }


    /**
     * Test product searching
     *
     * @return void
     */
    public function searchProduct($browser, $product)
    {
        $browser->visit('/')
            ->value('#search', str_limit($product->title, 15))
            ->keys('#search', ['{enter}'])
            ->assertSee($product->title);
    }
}
