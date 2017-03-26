<?php

namespace Modules\Messages\Entities;

use Illuminate\Database\Eloquent\Model;
use Watson\Validating\ValidatingTrait;
use App\Traits\NPerGroup;
use DB;

class Message extends Model
{
    use ValidatingTrait, NPerGroup;

    public $table = 'messages';
    
    protected $fillable  = [
        'user_id',  'thread_id', 'body'
    ];

    protected $touches = ['thread'];

    protected $rules = [
        'user_id'   => 'required|numeric',
        'thread_id'   => 'required|numeric',
        'body'   => 'required|string'
    ];

    protected $throwValidationExceptions = true;
    
    public function getIsOwnAttribute(){
        return (auth()->user()->id == $this->user_id);
    }
    
    /**
     * Show comments for model
     */
    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

}