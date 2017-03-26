<?php

namespace Modules\Address\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\User\Entities\User;
use GuzzleHttp\Client as GuzzleHttp;

class UserAddress extends Model
{
    public $table = 'user_addresses';
    
    protected $fillable  = [
        'name', 'country_id', 'user_id', 'city_id', 'street', 'post_code', 'note', 'lat', 'lng', 'phone'
    ];
    
    /**
     * 
     *
     * @return collection
     */
    public function user()
    {   
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    
    /**
     * 
     *
     * @return collection
     */
    public function city()
    {   
        return $this->hasOne(City::class, 'id', 'city_id');
    }
    
    /**
     * 
     *
     * @return collection
     */
    public function country()
    {   
        return $this->hasOne(Country::class, 'id', 'country_id');
    }


    /**
     * {@inheritdoc}
     */
    public static function boot() {
        parent::boot();

        static::saving(function($address) {
            $address->geocode();
        });
    }

    /**
     * Try to fetch the coordinates from Google
     * and store it to database
     *
     * @return $this
     */
    public function geocode() {

        $query = [];
        $query[] = $this->street       ?: '';
        $query[] = $this->city->name         ?: '';
        $query[] = $this->post_code    ?: '';
        $query[] = $this->country->name ?: '';

        $query = trim( implode(',', array_filter($query)) );
        $query = str_replace(' ', '+', $query);

        $url = 'https://maps.google.com/maps/api/geocode/json?address='.$query.'&sensor=false';

        $geocode = json_decode(((new GuzzleHttp(['timeout' => 5]))->request('GET', $url))->getBody());

        if ( $geocode && count($geocode->results) && isset($geocode->results[0]) ) {
            if ( $geo = $geocode->results[0]->geometry ) {
                $this->lat = $geo->location->lat;
                $this->lng = $geo->location->lng;
            }
        }

        return $this;
    }

}