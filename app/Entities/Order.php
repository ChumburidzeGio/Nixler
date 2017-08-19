<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Entities\Product;
use App\Entities\ShippingPrice;
use App\Entities\UserAddress;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;

class Order extends Model
{
    use Notifiable;
    
    public $table = 'orders';
    
    protected $fillable  = [
        'status', 'amount', 'currency', 'quantity', 'address', 'shipping_cost', 'payment_method', 'user_id', 'product_id', 'merchant_id', 'product_variant', 'shipping_window_from', 'shipping_window_to', 'city_id', 'phone', 'title'
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
    public function merchant()
    {   
        return $this->belongsTo(config('auth.providers.users.model'), 'merchant_id', 'id');
    }

    /**
     * 
     *
     * @return collection
     */
    public function user()
    {   
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id', 'id');
    }


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
    public function url()
    {   
       return route('orders.show', ['id' => $this->id]);
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

    public function canUpdate()
    {   
        $user = auth()->user();

        if($this->status == 'closed' || $this->status == 'rejected') {
            return true;
        }

        return ($user->can('update-status', [$this, 'confirmed']) || 
                    $user->can('update-status', [$this, 'sent']) || 
                    $user->can('update-status', [$this, 'closed']));
    }

}