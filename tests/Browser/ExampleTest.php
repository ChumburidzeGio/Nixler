<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Modules\User\Entities\User;
use Faker\Factory;

class ExampleTest extends DuskTestCase
{
    /**
     * A basic browser test example.
     *
     * @return void
     */
    public function testBasicExample()
    {
        $this->browse(function (Browser $browser) {

            $faker = Factory::create();

            //Sign up
            $browser->visit('/')
                    ->click('#register')
                    ->assertSee('Register')
                    ->value('#name', $faker->name)
                    ->value('#email', $faker->email)
                    ->value('#password', 'testjdq1e2')
                    ->value('#password-confirm', 'testjdq1e2')
                    ->click('form button')
                    ->assertSee('message');

            //Search
            /*$browser->value('#search', 'apple')
                    ->keys('#search', ['{enter}'])
                    ->assertSee('iPhone');*/

            //Add product
            $browser->visit('/new-product')
                    ->value('#title', $faker->text)
                    ->click('#category')
                    ->click('#category ul li:not(.selector-optgroup)')
                    ->value('#description', $faker->text)
                    ->type('#price', rand(1, 400))
                    ->type('#in_stock', rand(1,900))
                    ->attach('#picker-input', public_path('/img/aside.jpg'))
                    ->pause(1000)
                    ->press('#publish')
                    ->assertSee('Your product has been updated and is now live');

        });
    }
}
