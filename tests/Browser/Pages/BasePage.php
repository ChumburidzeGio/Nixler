<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Page;
use Faker\Factory;

class BasePage extends Page
{

    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url() {}

    /**
     * Prepare for Dusk test execution.
     *
     * @beforeClass
     * @return void
     */
    public function faker($key = null)
    {
        $faker = Factory::create();

        if($key) {
            return $faker->{$key};
        }

        return $faker;
    }
}
