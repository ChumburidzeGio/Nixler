<?php

namespace Modules\Address\Entities;

use Illuminate\Database\Eloquent\Model;
use Dimsav\Translatable\Translatable;

class Region extends Model
{
	use Translatable;
	
    public $table = 'geo_regions';
    
    protected $fillable  = [
        'country_id', 'name'
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