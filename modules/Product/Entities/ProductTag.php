<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;

class ProductTag extends Model
{
    public $table = 'product_tags_r';
    
    protected $fillable  = [
        'product_id', 'tag_id'
    ];

}