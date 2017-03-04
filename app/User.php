<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Nixler\People\Person;
use Nixler\Sellable\Merchantable;

class User extends Authenticatable
{
    use Person, Merchantable;

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
