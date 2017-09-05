<?php

namespace App\Crawler\Patterns;

use App\Crawler\Request;
use App\Crawler\BasePattern;

class Aliexpress extends BasePattern {

    public function detectProductsOnPage($url)
    {
        //parent::parse($url);

        dd(app(Request::class)->request('POST', $url, [], true)->get());
    }

    public function getTitle()
    {
        return ;
    }

    public function getDescription()
    {
    	
    }

    public function getMedia()
    {
    	
    }

    public function getVariants()
    {
    	
    }

    public function getCategory()
    {
        
    }

    public function getPrice()
    {
        
    }

    public function getOriginalPrice()
    {
        
    }

    public function getTags()
    {
    	
    }

    public function getTarget()
    {
        
    }

    public function getSKU()
    {
        
    }

}