<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

use Modules\User\Entities\Profile;
use Socialite;

class SocialAuthController extends Controller
{

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

        $socialUser = Socialite::driver($provider);
        $fields = config('services.'.$provider.'.fields');

        if($fields) $socialUser = $socialUser->fields($fields);

        $socialUser = $socialUser->user();

        $id = $socialUser->getId();
        $name = $socialUser->getName();
        $email = $socialUser->getEmail();
        $gender = $socialUser->offsetExists('gender') ? $socialUser->offsetGet('gender') : null;
        $birthday = $socialUser->offsetExists('birthday') ? $socialUser->offsetGet('birthday') : null;
        $photo =  $socialUser->getAvatar();

        if($provider == 'facebook'){
            $photo = 'http://graph.facebook.com/'.$id.'/picture?type=large&width=800';
        }

        $user = (new Profile)->findBySocialAccount($provider, $id, $name, $email, $gender, $birthday, $photo);

        auth()->login($user, true);

        return redirect()->to('/');
    }
    
}