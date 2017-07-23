<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class CollectionItem extends Model
{
    public $table = 'collection_items';
    
    protected $fillable  = [
        'order', 'collection_id', 'product_id'
    ];

}