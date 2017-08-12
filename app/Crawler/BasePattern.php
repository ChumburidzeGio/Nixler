<?php

namespace App\Crawler;

use App\Entities\ProductCategory;
use Symfony\Component\DomCrawler\Crawler;

class BasePattern
{
    
    private $crawler;
    
    private $media;

    public function parse(Crawler $crawler)
    {
        $this->crawler = $crawler;

        return $this;
    }

    /**
     * Return crawler instance
     *
     * @return Crawler
     */
    public function crawler($tag = null)
    {
    	if(!is_null($tag)) {
    		return $this->crawler->filter($tag);
    	}

    	return $this->crawler;
    }

    /**
     * Trim the string and strip tags inside
     */
    public function clean(string $string) : string
    {
        if(is_array($string)) {
            return array_filter($string, 'strlen');
        }

        $string = str_ireplace(["<br />","<br>","<br/>", "</p>"], "\n", $string);

        $string = str_replace("&nbsp;", ' ', $string);

    	return trim(strip_tags($string));
    }

    /**
     * Parse meta tag
     */
    public function getMeta(string $prop) : string
    {
        $content = $this->crawler('meta[property="og:'.$prop.'"]')->first();

        if(!$content->count()){
            $content = $this->crawler('meta[name="'.$prop.'"]')->first();
        }

        if($content->count()) {
            return $this->clean($content->attr('content'));
        }

        return null;
    }

    /**
     * Find category in list of tags or match category with list of subcats
     *
     * @return int|null
     */
    public function findCategory(array $haystack, array $needle)
    { 
        foreach ($needle as $cat => $value) {

            if(is_string($haystack)) {
                
                if(in_array($haystack, $value)) {
                    return $cat;
                } else {
                    continue;
                }

            }
            
            if(!in_array($cat, $haystack)) {
                continue;
            }

            if(!is_array($value)) {
                return $value;
            }

            foreach ($value as $subcat => $subvalue) {
                
                if(in_array($subcat, $haystack)) {
                    return $subvalue;
                }

            }

            return array_get($value, 'default');
        }
    }

    /**
     * Parse text from dom element
     */
    public function textFrom(string $prop)
    {
        $content = $this->crawler($prop)->first();

        if($content->count()) {
            return $this->clean($content->text());
        }

        return null;
    }

    /**
     * Parse text from the list of dom elements
     */
    public function crawleList($el)
    {
        return array_values($this->clean($this->crawler($el)->each(function($item) {
            return $item->text();
        })));
    }

    /**
     * Transform string from html to markdown
     */
    public function markdownify(string $html) : string
    {
    	$html = preg_replace_callback("/(<([^.]+)>)([^<]+)(<\\/\\2>)/s", function($matches){

            $text = $matches[3];

            if($matches[2] == 'li') {
                return "* {$text}";
            }

            if($matches[2] == 'strong') {
                   return "*{$text}*";
            }

            return $text;

        }, $html);

        return strip_tags($html);
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

    public function getVariants()
    {
    	return [];
    }

    public function getCategory()
    {
        return null;
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

    public function getTags()
    {
    	$keywords = $this->getMeta('keywords');

    	return $this->clean(explode(',', $keywords));
    }

    public function getTarget()
    {
        return null;
    }

    public function getSKU()
    {
        return null;
    }

    /**
     * Replace special characters in URL
     */
    public function parseUrl(string $url, $sep='+') : string
    {
        return preg_replace('/[[:space:]]+/', '%20', $url);
    }

    public function isInvalid()
    {
        return (method_exists($this, 'isProduct') && !$this->isProduct());
    }
    
    function toArray()
    {
        if($this->isInvalid()) {

            return [
                'error' => 'Product doesn\'t exist!'
            ];

        }

        $title = $this->getTitle();

        $description = $this->getDescription();

        $price = $this->getPrice();

        $originalPrice = $this->getOriginalPrice();

        $media = $this->getMedia();

        $varinats = $this->getVariants();

        $category = $this->getCategory();

        $categoryName = $this->getCategoryName();

        $tags = $this->getTags();

        $target = $this->getTarget();

        return compact('title', 'description', 'price', 'originalPrice', 'categoryName', 'category', 'media', 'varinats', 'tags', 'target');
    }
}