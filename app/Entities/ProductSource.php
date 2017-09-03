<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class ProductSource extends Model
{
    public $table = 'product_sources';
    
    protected $fillable  = [
        'product_id', 'merchant_id', 'source', 'status', 'params'
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
}