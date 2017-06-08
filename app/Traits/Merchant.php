<?php

namespace App\Traits;

use Intervention\Image\ImageManagerStatic as Image;
use Plank\Mediable\Mediable;
use Plank\Metable\Metable;
use MediaUploader;
use App\Entities\Product;
use App\Entities\Activity;


trait Merchant {
    
    
    /**
     *  Relationships
     */
    public function liked()
    {   
        return $this->belongsToMany(Product::class, 'activities', 'actor', 'object')->where('verb', 'product:liked');
    }

    
    /**
     *  Relationships
     */
    public function followCallback($target)
    {   
        $products = $target->products()->take(10)->orderBy('likes_count', 'desc')->active()->pluck('id');
        $this->pushInStream($products, 'user:'.$target->id);
    }

    
    /**
     *  Relationships
     */
    public function unfollowCallback($target)
    {   
        $this->streamRemoveBySource('user:'.$target->id);
    }
}