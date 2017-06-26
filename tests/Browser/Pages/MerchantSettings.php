<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class MerchantSettings extends BasePage
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return '/settings/shipping';
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
            ->assertSee('Add new shipping location');
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@delivery' => '#delivery_full',
            '@return' => '#has_return',
            '@policy' => '#policy',
            '@update' => '#update',
            '@cities' => '#add_new_form #city',
            '@city' => '#add_new_form #city ul li:not(.selector-optgroup)',
            '@price' => '#add_new_form input[name=\'price\']',
            '@window-from' => '#add_new_form #add_window_from',
            '@window-to' => '#add_new_form #add_window_to',
            '@add' => '#add_new_form button',
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
        $policy = $this->faker('text');

        $browser->click('@delivery')
            ->press('@return')
            ->pause(100)
            ->value('@policy', $policy)
            ->press('@update')
            ->pause(100)
            ->assertInputValue('@policy', $policy);

    }



    /**
     * Update general settings
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @param  string  $name
     * @return void
     */
    public function addNewLocation(Browser $browser)
    {
        $browser->click('@cities')
            ->click('@city')
            ->type("@price", 12)
            ->type("@window-from", 1)
            ->type("@window-to", 1)
            ->press('@add')
            ->pause(100)
            ->assertSee($browser->click('@cities')->text("@city"));

    }
}
