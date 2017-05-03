<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;
use Dimsav\Translatable\Translatable;

class Category extends Model
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
        return $this->hasMany(Category::class, 'parent_id');
    }
}