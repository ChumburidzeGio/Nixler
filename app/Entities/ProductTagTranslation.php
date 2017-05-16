<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class ProductTagTranslation extends Model
{
	public $timestamps = false;

    public $table = 'product_tags_t';
    
    protected $fillable = [
    	'name', 'slug'
    ];

}