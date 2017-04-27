<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;
use Dimsav\Translatable\Translatable;

class Tag extends Model
{
	use Translatable;

    public $table = 'product_tags';
    
    public $translatedAttributes = ['name', 'slug'];
    
    protected $fillable = ['user_id'];

    public function product()
    {   
        return $this->belongsToMany(Product::class, (new ProductTag)->getTable(), 'tag_id', 'product_id');
    }
}