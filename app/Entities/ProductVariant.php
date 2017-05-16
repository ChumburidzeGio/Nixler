<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Entities\Product;

class ProductVariant extends Model
{	
    public $table = 'product_variants';
    
    protected $fillable  = [
        'product_id', 'name', 'price', 'in_stock', 'sales_count'
    ];

}