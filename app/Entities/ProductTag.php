<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class ProductTag extends Model
{
    public $table = 'product_tags';
    
    protected $fillable = [
    	'user_id', 'product_id', 'name', 'type'
    ];

}