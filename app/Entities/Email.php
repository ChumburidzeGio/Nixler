<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Illuminate\Notifications\Notifiable;
use App\Notifications\SendVerificationCode;
use Exception;

class Email extends Model
{
    use Notifiable;
    
    public $table = 'user_emails';
    
    protected $fillable  = [
        'user_id', 'address', 'is_verified', 'is_default'
    ];
    
    protected $casts = [
        'is_verified' => 'boolean',
        'is_default' => 'boolean'
    ];

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

        $this->user->email = $this->address;
        $this->user->save();

        return true;
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

        if(!$this->where('user_id', $this->user_id)->where('is_verified', 1)->where('is_default', 1)->exists()){
            $this->makeDefault();
        }

        return $code;
    }


    public function makeVerified($code)
    {
        if($this->verification_code == $code){
            return $this->update([
                'is_verified' => true
            ]);
        }

        return false;
    }

}