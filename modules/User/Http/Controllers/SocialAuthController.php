<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

use Modules\User\Entities\Profile;
use Overtrue\Socialite\SocialiteManager;

class SocialAuthController extends Controller
{

    protected $socialite;

    public function __construct(){
        $this->socialite = new SocialiteManager(config('services'));
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

        $socialUser = $this->socialite->driver($provider);
        $fields = config('services.'.$provider.'.fields');

        if($fields) $socialUser = $socialUser->fields($fields);

        $socialUser = $socialUser->user();

        $id = $socialUser->getId();
        $name = $socialUser->getName();
        $email = $socialUser->getEmail();
        $gender = $socialUser['original']['gender'];
        $birthday = $socialUser['original']['birthday'];
        $photo =  $socialUser->getAvatar();

        if($provider == 'facebook'){
            $photo = 'http://graph.facebook.com/'.$id.'/picture?type=large&width=800';
        }

        $user = (new Profile)->findBySocialAccount($provider, $id, $name, $email, $gender, $birthday, $photo);

        auth()->login($user, true);

        return redirect()->to('/');
    }
    
}