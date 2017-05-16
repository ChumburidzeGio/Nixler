<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class RegionTranslation extends Model
{
	public $timestamps = false;
    public $table = 'geo_regions_translations';
    
    protected $fillable = ['name'];

}