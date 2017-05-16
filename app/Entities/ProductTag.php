<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Dimsav\Translatable\Translatable;

class ProductTag extends Model
{
	use Translatable;

    public $table = 'product_tags';
    
    public $translatedAttributes = [
    	'name', 'slug'
    ];
    
    protected $fillable = [
    	'user_id', 'product_id'
    ];

}