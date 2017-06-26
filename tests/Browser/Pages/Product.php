<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class Product extends BasePage
{
    protected $url = null;

    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return $this->url;
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
            '@like' => '#like',
            '@comment' => '#comment-form textarea',
            '@comment-submit' => '#comment-form button',
            '@first-comment-text' => '#comments div:first-child p',
        ];
    }

    /**
     * Like product
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return void
     */
    public function like(Browser $browser)
    {
        $browser->press('@like');

        $browser->screenshot('product-like');
    }


    /**
     * Comment product
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return void
     */
    public function comment(Browser $browser)
    {
        $text = $this->faker('text');

        $browser->script('window.scrollTo(0, 500);');

        $browser->click('@comment')->type('@comment', $text);

        $browser->click('@comment-submit');

        $browser->script('window.scrollTo(0, 500);');

        $browser->waitFor('@first-comment-text')->assertSeeIn('@first-comment-text', $text);
    }
}
