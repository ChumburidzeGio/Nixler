<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class Login extends BasePage
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return '/login';
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
            ->assertSee('Sign in');
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@email' => '#email',
            '@password' => '#password',
            '@button' => 'form button',
            '@forgot-pass' => '#password-reset',
        ];
    }
    
    /**
     * Log in user
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @param  string  $name
     * @return void
     */
    public function forgotPassword(Browser $browser, $email)
    {
        $browser->click('@forgot-pass')
            ->assertSee('Reset Password')
            ->value('@email', $email)
            ->click('@button')
            ->assertSee('We have e-mailed your password reset link!');
    }
    
    /**
     * Log in user
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @param  string  $name
     * @return void
     */
    public function loginUser(Browser $browser, $email)
    {
        $browser->type('@email', $email)
                ->type('@password', $email)
                ->click('@button');
    }
}
