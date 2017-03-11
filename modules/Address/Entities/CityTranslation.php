<?php

namespace Modules\Address\Entities;

use Illuminate\Database\Eloquent\Model;

class CityTranslation extends Model
{
	public $timestamps = false;
    public $table = 'geo_cities_translations';
    
    protected $fillable = ['name'];

}