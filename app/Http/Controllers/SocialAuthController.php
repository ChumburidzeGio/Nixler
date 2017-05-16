<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Entities\Profile;
use App\Entities\User;
use App\Repositories\UserRepository;
use Socialite;

class SocialAuthController extends Controller
{

    protected $repository;

    public function __construct(UserRepository $repository) {
        $this->repository = $repository;
    }

    public function redirect($provider)
    {
        if(!isset(config('services')[$provider])){
            return redirect()->back();
        }

        $socialite = Socialite::driver($provider);
        $fields = config('services.'.$provider.'.fields');
        $scopes = config('services.'.$provider.'.scopes');

        if($fields) $socialite = $socialite->fields($fields);
        if($scopes) $socialite = $socialite->scopes($scopes);

        return $socialite->redirect();
    }

    public function callback($provider)
    {
        if(!isset(config('services')[$provider])){
            return [];
        }

        $provider = Socialite::driver($provider)->fields(config('services.'.$provider.'.fields'));

        $socialUser = $provider->user();

        $this->repository->facebookProviderCallback($socialUser);

        return redirect()->intended('/');
    }
    
}