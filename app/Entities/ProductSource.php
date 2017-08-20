<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class ProductSource extends Model
{
    public $table = 'product_sources';
    
    protected $fillable  = [
        'product_id', 'merchant_id', 'source', 'status'
    ];
}