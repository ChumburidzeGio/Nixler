<?php

namespace Modules\Messages\Entities;

use Illuminate\Database\Eloquent\Model;
use Watson\Validating\ValidatingTrait;
use DB;

class Participant extends Model
{
    use ValidatingTrait;

    public $table = 'thread_participants';
    
    protected $fillable  = [
        'user_id', 'thread_id'
    ];

    protected $rules = [
        'user_id'   => 'required|numeric',
        'thread_id'   => 'required|numeric',
        //'last_read'   => ''
    ];

    protected $throwValidationExceptions = true;
 	

}