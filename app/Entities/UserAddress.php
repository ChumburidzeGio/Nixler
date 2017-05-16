<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client as GuzzleHttp;
use App\Entities\User;
use Exception;

class UserAddress extends Model
{
    public $table = 'user_addresses';
    
    protected $fillable  = [
        'country_id', 'user_id', 'city_id', 'street', 'lat', 'lng', 'phone'
    ];
    
    /**
     * User to address one to one relationship
     */
    public function user()
    {   
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    
    /**
     * City to address one to one relationship
     */
    public function city()
    {   
        return $this->hasOne(City::class, 'id', 'city_id');
    }
    
    /**
     * Country to address one to one relationship
     */
    public function country()
    {   
        return $this->hasOne(Country::class, 'id', 'country_id');
    }


    /**
     * Boot model and add observers
     */
    public static function boot() {
        parent::boot();

        static::saving(function($address) {
            $address->geocode()->updateUserCity();
        });
    }

    /**
     * Set user city from address
     */
    public function updateUserCity() {
        $user = $this->user()->first();
        $user->city_id = $this->city_id;
        $user->save();
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
        $query[] = $this->country->name ?: '';

        $query = trim( implode(',', array_filter($query)) );
        $query = str_replace(' ', '+', $query);

        $url = 'https://maps.google.com/maps/api/geocode/json?address='.$query.'&sensor=false';

        try {

            $geocode = json_decode(((new GuzzleHttp(['timeout' => 2]))->request('GET', $url))->getBody());

            if ( $geocode && count($geocode->results) && isset($geocode->results[0]) ) {
                if ( $geo = $geocode->results[0]->geometry ) {
                    $this->lat = $geo->location->lat;
                    $this->lng = $geo->location->lng;
                }
            }

        } catch (Exception $e){}

        return $this;
    }

}