<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\User\Entities\Profile;
use Modules\User\Entities\User;
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

        $name = sprintf("%s %s", $socialUser->offsetGet('first_name'), $socialUser->offsetGet('last_name'));
        $photo =  $socialUser->getAvatar();

        $account = Profile::firstOrCreate([
            'provider' => $provider,
            'external_id' => $socialUser->getId()
        ]);

        $user = $account->user_id ? User::find($account->user_id) : null;

        if (is_null($user)) {

            $user = User::whereEmail($socialUser->getEmail())->first();

            if(is_null($user)){
                $user = $account->user()->create([
                    'email' => $socialUser->getEmail(),
                    'name' => $name
                ]);
            }

            if($account->wasRecentlyCreated && !$user->firstMedia('avatar')){
                $user->changeAvatar($socialUser->getAvatar());
            }

            $account->update([
                'user_id' => $user->id
            ]);
        }

        if($socialUser->offsetExists('birthday') && !$user->hasMeta('birthday')){
            $carbon = new \Carbon\Carbon;
            list($month,$day,$year) = explode('/', $socialUser->offsetGet('birthday'));
            $user->setMeta('birthday', $carbon->createFromDate($year, $month, $day));
        }

        if($socialUser->offsetExists('gender') && !$user->hasMeta('gender')){
            $user->setMeta('gender', $socialUser->offsetGet('gender'));
        }

        auth()->login($user, true);

        return redirect()->to('/');
    }
    
}