<?php

namespace Modules\Product\Traits;

use Intervention\Image\ImageManagerStatic as Image;
use Plank\Mediable\Mediable;
use Plank\Metable\Metable;
use MediaUploader;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductStats;
use Modules\Stream\Entities\Activity;


trait Merchant {
    
    /**
     *  Relationships
     */
    public function products()
    {   
        return $this->hasMany(Product::class, 'owner_id');
    }

    
    /**
     *  Relationships
     */
    public function shopStats()
    {   
        return $this->hasMany(ProductStats::class, 'actor');
    }

    
    /**
     *  Relationships
     */
    public function shopStatistics()
    {   
        return (new ProductStats)->calculate($this->shopStats()->get());
    }

    

    /**
     *  Relationships
     */
    public function updateUsernameCallback($from, $to)
    {   
        return $this->products()->where('owner_username', $from)->update([
            'owner_username' => $to
        ]);
    }

    
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
        $products = $target->products()->take(10)->orderBy('likes_count', 'desc')->pluck('id');
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