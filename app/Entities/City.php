<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Dimsav\Translatable\Translatable;

class City extends Model
{
	use Translatable;

    public $table = 'geo_cities';
    
    public $translatedAttributes = ['name'];

    protected $fillable  = [
        'country_id', 'region_id', 'population', 'lat', 'lng', 'geonames_id'
    ];

    protected $visible  = [
        'id', 'name'
    ];


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
    ];

}