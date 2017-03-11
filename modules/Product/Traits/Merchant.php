<?php

namespace Modules\Product\Traits;

use Intervention\Image\ImageManagerStatic as Image;
use Plank\Mediable\Mediable;
use Plank\Metable\Metable;
use MediaUploader;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductStats;


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
    public function createProduct($currency)
    {   
        $product = new Product;
        $product->status = 'inactive';
        $product->currency = $currency;
        $product->owner_id = $this->id;
        $product->owner_username = $this->username;
        $product->save();
        return $product;
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
        return $this->belongsToMany(Product::class, 'product_likes', 'actor', 'object');
    }

    
    /**
     *  Relationships
     */
    public function followCallback($target)
    {   
        $products = $target->products()->take(10)->latest()->pluck('id');
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