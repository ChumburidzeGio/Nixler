<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Dimsav\Translatable\Translatable;

class Country extends Model
{
	use Translatable;
    
    public $table = 'geo_countries';
    
    protected $fillable  = [
        'iso_code', 'geonames_id', 'area', 'continent', 'population', 
        'currency', 'calling_code', 'language', 'gini', 'capital', 'currency_symbol'
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

    /**
     * 
     *
     * @return collection
     */
    public function regions()
    {   
        return $this->hasMany(Region::class);
    }

}