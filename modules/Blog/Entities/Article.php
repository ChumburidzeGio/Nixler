<?php

namespace Modules\Blog\Entities;

use Illuminate\Database\Eloquent\Model;
use Dimsav\Translatable\Translatable;
use Cviebrock\EloquentSluggable\Sluggable;
use App\Services\Markdown;

class Article extends Model
{
	use Translatable;

    public $table = 'articles';
    
    public $translatedAttributes = ['title', 'body'];

    protected $fillable  = [
        'user_id', 'slug'
    ];


    /**
     * Return description parsed with Markdown
     */
    public function getBodyParsedAttribute()
    {   
        return (new Markdown)->text($this->body);
    }
}