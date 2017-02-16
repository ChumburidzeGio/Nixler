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

    public function photo($params){

        $names = [
            'citystreet', 'raspberries', 'bridge'
        ];

        $endpoint = 'https://assets.imgix.net/unsplash/'.$names[array_rand($names)].'.jpg?fm=pjpg&q=80&usm=10';

        if(starts_with($params, 'resize')){

            $sizes = explode('x', substr($params, strpos($params, ":") + 1));

            return url($endpoint.http_build_query([
                'crop' => 'faces',
                'fit' => 'crop',
                'h' => $sizes[0],
                'w' => $sizes[1],
            ]));
        }

    }
}
