<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;

class ProductLike extends Model
{
	
    public $table = 'product_likes';
    
    protected $fillable  = [
        'actor', 'object'
    ];

}