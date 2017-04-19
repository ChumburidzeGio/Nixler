<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Modules\User\Notifications\SendVerificationCode;
use Exception;

class Phone extends Model
{
    use Notifiable;
    
    public $table = 'user_phones';
    
    protected $fillable  = [
        'user_id', 'country_code', 'number', 'is_verified', 'is_default'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['phone_number'];

    public function user()
    {   
        return $this->belongsTo(config('auth.providers.users.model'));
    }

    public function makeDefault()
    {
        if(!$this->is_verified){
            return false;
        }

        $this->where('user_id', $this->user_id)->where('is_default', 1)->update([
            'is_default' => false
        ]);

        $this->update([
            'is_default' => true
        ]);

        return true;
    }


    public function getPhoneNumberAttribute()
    {
    	return '+'.$this->attributes['country_code'].$this->attributes['number'];
    }


    public function verify()
    {
        if($this->is_verified){
            return true;
        }

        $code = mt_rand(100000, 999999);

        $this->notify(new SendVerificationCode($code));
        
        $this->verification_code = $code;
        
        $this->save();

        return $code;
    }


    public function makeVerified($code)
    {
        if($this->verification_code != $code){
            return false;
        }
        
        $verified = $this->update([
            'is_verified' => true
        ]);

        if($verified && !$this->where('user_id', $this->user_id)->where('is_default', 1)->exists()){
            $this->makeDefault();
        }

        return $verified;
    }

}