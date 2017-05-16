<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{

    public $table = 'user_profiles';
    
    protected $fillable  = [
        'user_id', 'provider', 'external_id'
    ];

    public function user()
    {   
        return $this->belongsTo(config('auth.providers.users.model'));
    }

}