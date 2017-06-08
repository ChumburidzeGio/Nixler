<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class ProductUpdate extends BasePage
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
        $browser->assertSee('Product Statistics');
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@title' => '#title',
            '@categories' => '#category',
            '@category' => '#category ul li:not(.selector-optgroup)',
            '@description' => '#description',
            '@price' => '#price',
            '@in_stock' => '#in_stock',
            '@variant-add' => '#add-variants',
            '@variant-name' => '#variants [ng-model="variant.name"]:nth-child(1)',
            '@variant-price' => '#variants [ng-model="variant.price"]',
            '@variant-in_stock' => '#variants [ng-model="variant.in_stock"]',
            '@media-selector' => '#picker-input',
            '@button' => '#publish',
        ];
    }
    
    /**
     * Create new product
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @param  string  $name
     * @return void
     */
    public function createProduct(Browser $browser)
    {
        $browser->value('@title', $this->faker('sentence').'TestProductName')
            ->click('@categories')
            ->click('@category')
            ->value('@description', $this->faker('text'))
            ->type('@price', rand(1, 400))
            ->type('@in_stock', rand(1,900))
            ->click('@variant-add')
            ->type('@variant-name', $this->faker('word'))
            ->type('@variant-price', rand(1, 400))
            ->type('@variant-in_stock', rand(1, 900))
            ->attach('@media-selector', public_path('/img/aside.jpg'))
            ->pause(1000)
            ->press('@button')
            ->pause(100)
            ->assertSee('Your product has been saved');
    }
}
