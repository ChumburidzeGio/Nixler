<?php

namespace Modules\Address\Entities;

use Illuminate\Database\Eloquent\Model;
use Dimsav\Translatable\Translatable;

class City extends Model
{
	use Translatable;

    public $table = 'geo_cities';
    
    public $translatedAttributes = ['name'];

    protected $fillable  = [
        'country_id', 'region_id'
    ];

    protected $visible  = [
        'id', 'name'
    ];

}