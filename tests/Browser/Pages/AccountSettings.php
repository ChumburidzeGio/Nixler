<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class AccountSettings extends BasePage
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return '/settings/account';
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
            ->assertSee('General settings');
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@username' => '#username',
            '@email' => '#email',
            '@cities' => '#city',
            '@city' => '#city ul li:not(.selector-optgroup)',
            '@website' => '#website',
            '@headline' => '#headline',
            '@phone' => '#phone',
            '@button-general' => '#general form button',
            '@button-password' => '#password form button',
            '@password-old' => '#current_password',
            '@password-new' => '#new_password',
            '@password-repeat' => '#new_password_confirmation',
        ];
    }
    
    /**
     * Update general settings
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @param  string  $name
     * @return void
     */
    public function updageGeneralSettings(Browser $browser)
    {
        $browser->value('@name', 'TestUserName')
            ->value('@username', rand(43452345, 1324321432551))
            ->value('@email', $this->faker('email'))
            ->click('@cities')->click('@city')
            ->value('@website', $this->faker('url'))
            ->value('@headline', $this->faker('sentence'))
            ->value('@phone', '+995'.rand(574711000, 574999999))
            ->click('@button-general')
            ->assertInputValue('@name', 'TestUserName')
            ->assertSee('Confirmation code');

        $browser->screenshot('as-general');
    }
    
    /**
     * Update password
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @param  string  $name
     * @return void
     */
    public function updagePassword(Browser $browser, $email)
    {
        $newPassword = md5($email);

        $browser->value('@password-old', $email)
            ->value('@password-new', $newPassword)
            ->value('@password-repeat', $newPassword)
            ->click('@button-general');

        $browser->screenshot('as-password');
    }
}
