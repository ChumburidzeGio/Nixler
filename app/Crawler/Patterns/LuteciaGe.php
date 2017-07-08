<?php

namespace App\Crawler\Patterns;

use Goutte\Client;
use App\Crawler\BasePattern;

class LuteciaGe extends BasePattern {

    /**
     */
    public function getVariants()
    {
        $varints = $this->crawler('.col-sm-6.product-right .list-float-left li')->each(function($item) {
            return [
                'name' => $item->filter('p')->first()->text(),
                'price' => $item->filter('.product-price')->first()->text(),
            ];
        });

        return $varints; 
    }

    /**
     * Parse the category of product
     *
     * @return integer
     */
    public function getCategory()
    {
    	$breadcrumb = $this->getTags();

        return $this->findCategory($breadcrumb, [
            'თმის და ტანის მოვლა' => [
                'default' => 56,
                'საშხაპე საშუალებები' => 61,
                'დეოდორანტი და ანტიპერსპერანტი' => 61,
                'თმის მოვლა' => 57,
            ],
            'სახის მოვლა' => 56,
            'სუნამოები' => 58,
            'დეკორატიული კოსმეტიკა' => 55,
        ]);

    }

    /**
     * Parse the tags for product
     *
     * @return array
     */
    public function getTags()
    {
        return $this->crawleList('.breadcrumb li:not(:first-child):not(:last-child)');
    }

    /**
     * Parse the image of product
     *
     * @return array
     */
    public function getMedia()
    {
        return [$this->crawler('.image .thumbnail img')->first()->attr('src')];
    }
    
}