<?php

namespace Modules\Address\Entities;

use Illuminate\Database\Eloquent\Model;
use Dimsav\Translatable\Translatable;

class Country extends Model
{
	use Translatable;
    
    public $table = 'geo_countries';
    
    protected $fillable  = [
        'code', 'currency'
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