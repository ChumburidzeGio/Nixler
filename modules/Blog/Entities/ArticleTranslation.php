<?php

namespace Modules\Blog\Entities;

use Illuminate\Database\Eloquent\Model;

class ArticleTranslation extends Model
{
	public $timestamps = false;
    public $table = 'articles_t';
    
    protected $fillable = ['title', 'body'];

}