<?php

namespace App\Crawler;

use App\Entities\ProductCategory;
use Symfony\Component\DomCrawler\Crawler;

class BasePattern
{
    
    private $crawler;
    
    private $media;

    /**
     * Construct the pattern
     *
     * @param $crawler Crawler
     * 
     * @return $this
     */
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
     *
     * @param $string string
     * 
     * @return string
     */
    public function clean($string)
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
     *
     * @param $prop string
     * 
     * @return string
     */
    public function getMeta($prop)
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
     * @param $haystack array
     * @param $needle array
     * 
     * @return int
     */
    public function findCategory($haystack, $needle)
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
     * Parse meta tag
     *
     * @param $prop string
     * 
     * @return string
     */
    public function textFrom($prop)
    {
        $content = $this->crawler($prop)->first();

        if($content->count()) {
            return $this->clean($content->text());
        }

        return null;
    }

    /**
     * Parse meta tag
     *
     * @param $prop string
     * 
     * @return string
     */
    public function crawleList($el)
    {
        return array_values($this->clean($this->crawler($el)->each(function($item) {
            return $item->text();
        })));
    }

    /**
     * Parse meta tag
     *
     * @param $prop string
     * 
     * @return string
     */
    public function markdownify($html)
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

    /**
     * Parse the title of product from meta tags
     *
     * @return string
     */
    public function getTitle()
    {
    	return $this->getMeta('title');
    }

    /**
     * Parse the description of product from meta tags
     *
     * @return string
     */
    public function getDescription()
    {
    	return $this->getMeta('description');
    }

    /**
     * Parse the image of product from meta tags
     *
     * @return array
     */
    public function getMedia()
    {
    	return [$this->getMeta('image')];
    }

    /**
     * Parse the variants of product
     *
     * @return array
     */
    public function getVariants()
    {
    	return [];
    }

    /**
     * Parse the category of product
     *
     * @return integer
     */
    public function getCategory()
    {
        return null;
    }

    /**
     * Parse the category of product
     *
     * @return integer
     */
    public function getCategoryName()
    {
        $category = $this->getCategory();

        return $category ? ProductCategory::find($category)->name : null;
    }

    /**
     * Parse the price of product
     *
     * @return integer
     */
    public function getPrice()
    {
    	$variants = $this->getVariants();

        if(count($variants)){
            return collect($variants)->min('price');
        }

        return $this->textFrom('[itemprop="price"]');
    }

    /**
     * Parse the tags of product from meta tags
     *
     * @return array
     */
    public function getTags()
    {
    	$keywords = $this->getMeta('keywords');

    	return $this->clean(explode(',', $keywords));
    }

    /**
     * Parse the image of product
     *
     * @return array
     */
    public function parseUrl($url, $sep='+')
    {
        return preg_replace('/[[:space:]]+/', '%20', $url);
    }

    /**
     * Get target group
     *
     * @return string
     */
    public function getTarget()
    {
        return null;
    }

    /**
     * Return pattern as an array
     *
     * @return array
     */
    function toArray()
    {
        $title = $this->getTitle();

        $description = $this->getDescription();

        $price = $this->getPrice();

        $media = $this->getMedia();

        $varinats = $this->getVariants();

        $category = $this->getCategory();

        $categoryName = $this->getCategoryName();

        $tags = $this->getTags();

        $target = $this->getTarget();

        return compact('title', 'description', 'price', 'categoryName', 'category', 'media', 'varinats', 'tags', 'target');
    }
}