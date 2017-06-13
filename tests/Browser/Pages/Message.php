<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class Message extends BasePage
{

    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return '';
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param  Browser  $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertSee('BUY NOW');
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [

        ];
    }

    /**
     * Like product
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return void
     */
    public function product(Browser $browser)
    {
        $browser->visit('/@nixler')
            ->click('#messageAccount')
            ->type('textarea', 'Some message')
            ->press('button')
            ->pause(5000)
            ->assertSeeIn('._media:last-child .text p', 'Some message');
    }

}
