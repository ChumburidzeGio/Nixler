<?php

namespace Modules\Order\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Product\Entities\Product;
use Modules\Address\Entities\ShippingPrice;
use Modules\Address\Entities\UserAddress;
use Carbon\Carbon;

class Order extends Model
{
    public $table = 'orders';
    
    protected $fillable  = [
        'status', 'amount', 'currency', 'quantity', 'address_id', 'shipping_cost', 'payment_method', 'user_id', 'product_id', 'merchant_id', 'product_variant', 'shipping_window_from', 'shipping_window_to', 'note'
    ];


    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'shipping_window_from',
        'shipping_window_to',
    ];


    /**
     * 
     *
     * @return collection
     */
    public function product()
    {   
        return $this->hasOne(Product::class, 'id', 'product_id');
    }


    /**
     * 
     *
     * @return collection
     */
    public function address()
    {   
        return $this->hasOne(UserAddress::class, 'id', 'address_id');
    }


    /**
     * 
     *
     * @return collection
     */
    public function isStatus($status)
    {   
       return ($this->status == $status);
    }


    /**
     * 
     *
     * @return collection
     */
    public function stagePassed($status)
    {   
        if($status == 'confirmation') {

            return ($this->status == 'confirmed' || $this->status == 'sent' || $this->status == 'closed');

        } elseif($status == 'shipping') {

            return ($this->status == 'sent' || $this->status == 'closed');

        } elseif($status == 'delivery') {

            return ($this->status == 'closed');

        }
    }

}