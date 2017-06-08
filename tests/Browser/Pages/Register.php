<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class Register extends BasePage
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return '/register';
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param  Browser  $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertPathIs($this->url())
            ->assertSee('Register');
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@name' => '#name',
            '@email' => '#email',
            '@password' => '#password',
            '@button' => 'form button',
        ];
    }
    
    /**
     * Create a new account.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @param  string  $name
     * @return void
     */
    public function createAccount(Browser $browser, $email)
    {
        $browser->type('@name', $this->faker('email'))
                ->type('@email', $email)
                ->type('@password', $email)
                ->click('@button')
                ->assertSee('message');
    }
}
