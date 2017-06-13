<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Watson\Validating\ValidatingTrait;
use DB;

class Comment extends Model
{
    use ValidatingTrait;
    
    public $table = 'comments';
    
    protected $fillable  = [
        'user_id',  'target_id', 'text', 'target_type'
    ];

    protected $with = ['author'];

    protected $rules = [
        'user_id'   => 'required|numeric',
        'target_id'   => 'required|numeric',
        'text'   => 'required|string',
    ];

    protected $throwValidationExceptions = true;


    /**
     * Show comments for model
     */
    public function author()
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id', 'id');
    }


}