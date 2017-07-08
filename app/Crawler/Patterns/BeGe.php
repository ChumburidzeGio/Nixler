<?php

namespace App\Crawler\Patterns;

use Goutte\Client;
use App\Crawler\BasePattern;

class BeGe extends BasePattern {

    /**
     * Parse the title for product
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->clean($this->crawler('[itemprop="name"]')->first()->text());
    }

    /**
     * Parse the description for product
     *
     * @return string
     */
    public function getDescription()
    {
        $basic = $this->clean($this->crawler('[itemprop="description"]')->first()->text());

        $details = $this->clean($this->crawler('.cpt_product_description')->first()->text());

        return "{$basic}\n\n#მახასიათებლები\n{$details}";
    }

    /**
     * Parse the tags of product
     *
     * @return array
     */
    public function getTags(){}
    
}