<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{

    public $table = 'user_phones';
    
    protected $fillable  = [
        'user_id', 'country_code', 'number', 'is_verified', 'is_default'
    ];


    public function makeDefault()
    {
    	$this->where('user_id', $this->user_id)->where('is_default', true)->update([
    		'is_default' => false
    	]);

    	$this->update([
    		'is_default' => true
    	]);
    }


    public function getPhoneNumberAttribute()
    {
    	return $this->attributes['country_code'].$this->attributes['number'];
    }


    public function verify()
    {
        $code = mt_rand(100000, 999999);
        //...
    }

}