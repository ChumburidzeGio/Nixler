<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Entities\Product;

class ProductVariant extends Model
{	
    public $table = 'product_variants';
    
    protected $fillable  = [
        'product_id', 'name', 'price', 'in_stock', 'sales_count', 'original_price'
    ];
    
    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['product'];

    public function product()
    {   
        return $this->belongsTo(Product::class);
    }

    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }
}