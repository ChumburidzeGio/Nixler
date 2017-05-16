<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Dimsav\Translatable\Translatable;

class Region extends Model
{
	use Translatable;
	
    public $table = 'geo_regions';
    
    protected $fillable  = [
        'geonames_id', 'country_id', 'population', 'iso_code'
    ];
    
    public $translatedAttributes = ['name'];

    /**
     * 
     *
     * @return collection
     */
    public function cities()
    {   
        return $this->hasMany(City::class);
    }
}