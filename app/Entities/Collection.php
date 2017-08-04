<?php

namespace App\Entities;

use App\Entities\Product;
use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    public $table = 'collections';
    
    protected $fillable  = [
        'user_id', 'name', 'description', 'media_id'
    ];
    
    /**
     * Relationship with owner
     *
     * @return collection
     */
    public function owner()
    {   
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id', 'id');
    }

    /**
     * Relationship with owner
     *
     * @return collection
     */
    public function products()
    {   
        return $this->belongsToMany(Product::class, 'collection_items', 'collection_id', 'product_id');
    }

    /**
     * Link attribute for collection
     *
     * @return string
     */
    public function getLinkAttribute()
    {   
        return route('collections.show', [
            'id' => $this->getKey()
        ]);
    }

}