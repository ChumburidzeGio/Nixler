<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Dimsav\Translatable\Translatable;

class Article extends Model
{
	use Translatable;

    public $table = 'articles';
    
    public $translatedAttributes = ['title', 'body'];

    protected $fillable  = [
        'user_id', 'slug'
    ];

}