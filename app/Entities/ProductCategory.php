<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Dimsav\Translatable\Translatable;

class ProductCategory extends Model
{
	use Translatable;

    public $table = 'product_cats';
    
    public $translatedAttributes = ['name'];

    protected $fillable  = [
        'parent_id', 'order', 'icon'
    ];

    /**
     *  Relationships
     */
    public function children()
    {   
        return $this->hasMany(ProductCategory::class, 'parent_id');
    }

    public function getForeignKey() {
        return 'category_id';
    }
}