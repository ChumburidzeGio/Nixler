<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;

class TagTranslation extends Model
{
	public $timestamps = false;

    public $table = 'product_tags_t';
    
    protected $fillable = ['name', 'slug'];

}