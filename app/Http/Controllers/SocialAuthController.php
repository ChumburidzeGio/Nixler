<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Entities\Profile;
use App\Entities\User;
use App\Repositories\UserRepository;
use Laravel\Socialite\SocialiteManager;

class SocialAuthController extends Controller
{

    protected $repository;

    protected $socialite;

    public function __construct(UserRepository $repository) {

        parent::__construct();

        $this->repository = $repository;

        config([
            "services.facebook.redirect" => url(config("services.facebook.redirect"))
        ]);

        $this->socialite = app(SocialiteManager::class, ['app' => app()]);

    }

    public function redirect($provider)
    {
        if(!isset(config('services')[$provider])){
            return redirect()->back();
        }

        $socialite = $this->socialite->driver($provider);

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

        try
        {
            $provider = $this->socialite->driver($provider)->fields(config('services.'.$provider.'.fields'));

            $socialUser = $provider->user();

            $this->repository->facebookProviderCallback($socialUser);
        }
        catch(\Exception $e) {}

        return redirect()->intended('/');
    }
    
}