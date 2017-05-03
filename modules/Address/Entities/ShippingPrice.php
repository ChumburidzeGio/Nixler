<?php

namespace Modules\Address\Entities;

use Illuminate\Database\Eloquent\Model;

class ShippingPrice extends Model
{
    public $table = 'shipping_prices';
    
    protected $fillable  = [
        'user_id', 'type', 'location_id', 'price', 'window_from', 'window_to', 'currency'
    ];

    public function city()
    {   
        return $this->hasOne(City::class, 'id', 'location_id');
    }

    /**
     * Relationship to get city or country depending on type attribute
     */
    public function location()
    {   
        return $this->attributes['type'] == 'country' 
            ? $this->hasOne(Country::class, 'id', 'location_id') 
            : $this->hasOne(City::class, 'id', 'location_id');
    }

    public function getSidAttribute(){
        return $this->attributes['type'].$this->attributes['location_id'].$this->attributes['id'];
    }
}