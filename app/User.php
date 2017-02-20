<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Nixler\People\Person;

class User extends Authenticatable
{
    use Person;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}
