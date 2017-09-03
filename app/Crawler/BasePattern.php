<?php

namespace App\Crawler;

use Goutte\Client;
use App\Entities\ProductCategory;
use Symfony\Component\DomCrawler\Crawler;
use App\Crawler\Traits\HelpersTrait;
use App\Crawler\Traits\PriceCalculatorTrait;

class BasePattern
{
    use PriceCalculatorTrait, HelpersTrait;

    private $source;

    private $crawler;
    
    public $param;
    
    private $media;
    
    private $shortProductTag;

    public function parse($url)
    {
        $this->source = $url;

        $this->crawler = app(Client::class)->request('GET', $url);

        return $this;
    }

    public function detectProductsOnPage($url)
    {
        $this->parse($url);

        $products = $this->crawler($this->shortProductTag);

        dd($products);

        if(!$products->count()) return [];

        return array_unique($products->each(function($a)
        {
            return $a->filter('a')->link()->getUri();
        }));
    }

    public function withParam($param)
    {
        $this->param = $param;

        return $this;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function getTitle()
    {
    	return $this->getMeta('title');
    }

    public function getDescription()
    {
    	return $this->getMeta('description');
    }

    public function getMedia()
    {
    	return [$this->getMeta('image')];
    }

    public function getCategoryName()
    {
        $category = $this->getCategory();

        return $category ? ProductCategory::find($category)->name : null;
    }

    public function getPrice()
    {
        $variants = $this->getVariants();

        if(count($variants)){
            return collect($variants)->min('price');
        }

        return $this->textFrom('[itemprop="price"]');
    }

    public function getOriginalPrice()
    {
        $variants = $this->getVariants();

        if(count($variants)){
            return collect($variants)->min('original_price');
        }

    	return null;
    }

    public function getTarget() {}

    public function getSKU() {}

    public function getParam() {}

    public function getCategory() {}

    public function getAvailableParams()
    {
        return [];
    }

    public function getVariants()
    {
        return [];
    }

    public function getTags()
    {
        return [];
    }

    public function isProduct()
    {
        return true;
    }

}