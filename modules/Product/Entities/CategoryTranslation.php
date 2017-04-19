<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;

class CategoryTranslation extends Model
{
	public $timestamps = false;

    public $table = 'product_cats_t';
    
    protected $fillable = ['name'];

}