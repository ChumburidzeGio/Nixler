<?php

namespace Modules\Address\Entities;

use Illuminate\Database\Eloquent\Model;

class CountryTranslation extends Model
{
	public $timestamps = false;
    public $table = 'geo_countries_translations';
    
    protected $fillable = ['name'];

    /**
     * 
     *
     * @return collection
     */
    public function getNameAttribute()
    {   
    	$value = $this->attributes['name'];
        return starts_with($value, 'Rzeczpospolita') ? str_replace('Rzeczpospolita', '', $value) : $value;
    }
}