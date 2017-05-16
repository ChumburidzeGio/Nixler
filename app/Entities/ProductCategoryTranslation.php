<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class ProductCategoryTranslation extends Model
{
	public $timestamps = false;

    public $table = 'product_cats_t';
    
    protected $fillable = ['name'];

}