<?php

namespace Tests\Cases\Product;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use App\Entities\User;

class Edit extends DuskTestCase
{
    /**
     * Test system if everyhting works
     * 
     * @return void
     */
    public function testEdit()
    {
        $this->browse(function ($browser) {

            $browser->visit(route('product.create'))
                    ->assertPathIs('/login');

            $user = User::first();

            $browser->loginAs($user)
                    ->visit(route('product.create'))
                    ->assertVisible('form[name="product"]');

            $browser->click('#category')
                    ->click('#category ul li:not(.selector-optgroup)');

            $browser->attach('#picker-input', public_path('/img/meta.jpg'));

            $browser->press('#import')
                    ->assertVisible('.ngdialog .ngdialog-content form');

        });
    }

}
