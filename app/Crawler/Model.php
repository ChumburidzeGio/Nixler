<?php

namespace App\Crawler;

use Illuminate\Database\Eloquent\Model as BaseModel;

class Model extends BaseModel
{
    protected $fillable = [
    	'title', 'description', 'price', 'originalPrice', 'categoryName', 'category', 'media', 'variants', 'tags', 'target', 'param', 'sku', 'source'
    ];

    protected $pattern;

    public function setPattern($pattern)
    {
        if(!$pattern->isProduct())
        {
            return $this;
        }

        $source = $pattern->getSource();

        $title = $pattern->getTitle();

        $description = $pattern->getDescription();

        $price = $pattern->getPrice();

        $originalPrice = $pattern->getOriginalPrice();

        $media = $pattern->getMedia();

        $variants = $pattern->getVariants();

        $category = $pattern->getCategory();

        $categoryName = $pattern->getCategoryName();

        $tags = $pattern->getTags();

        $target = $pattern->getTarget();

        $param = $pattern->getParam();

        $sku = $pattern->getSKU();

        $this->pattern = $pattern;

        $this->fill(
        	compact('title', 'description', 'price', 'originalPrice', 'categoryName', 'category', 'media', 'variants', 'tags', 'target', 'param', 'sku', 'source')
        );

        return $this;
    }

    public function pattern()
    {
        return $this->pattern;
    }

    public function getKey()
    {
        return $this->sku;
    }
    
}