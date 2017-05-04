<?php

namespace Modules\User\Repositories;

use App\Repositories\BaseRepository;
use Modules\User\Entities\User;
use Modules\User\Notifications\SomeoneFollowedYou;
use Modules\Address\Repositories\LocationRepository;
use Modules\User\Entities\Profile;
use App\Services\Facebook;
use Carbon\Carbon;

class UserRepository extends BaseRepository {


    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return "Modules\\User\\Entities\\User";
    }


    /**
     * Find user by username
     */
    public function find($username, $tab)
    {
        if(auth()->check() && auth()->user()->username == $username){
            $user = auth()->user();
        } else {
            $user = $this->model->where('username', $username)->firstOrFail();
        }

        $user->liked_count = $user->liked()->count();
        $user->selling_count = $user->products()->where('status', 'active')->count();
        $user->followers_count = $user->followers()->count();
        $user->followings_count = $user->followings()->count();
        $user->media_count = $user->media()->unordered()->count();

        if($tab == 'products'){
            $data = $user->products()->where('status', 'active')->withMedia()->take(20)->get();
            $view = 'products';
        }
        elseif($tab == 'followers'){
            $data = $user->followers()->take(20)->get();
            $view = 'people';
        }
        elseif($tab == 'followings'){
            $data = $user->followings()->take(20)->get();
            $view = 'people';
        }
        elseif($tab == 'photos'){
            $data = $user->media()->take(20)->get();
            $view = 'media';
        }
        else {
            $data = $user->liked()->withMedia()->take(20)->get();
            $view = 'products';
        }

        return compact('user', 'data', 'tab', 'view');
    }


    /**
     * Follow user
     */
    public function follow($username)
    {
        $target = $this->model->whereUsername($username)->firstOrFail();
        $user = auth()->user();
        
        if($user->id !== $target->id){

            if($user->isFollowing($target->id)){
                $user->unfollow($target->id);
                $user->unfollowCallback($target);
            } else {
                $user->follow($target->id);
                $target->notify(new SomeoneFollowedYou($user));
                $user->followCallback($target);
            } 

        }

        return $target;
    }


    /**
     * Soft delete user
     */
    public function deactivate()
    {
        $user = auth()->user();
        return $user->delete();
    }


    /**
     * Callback for facebook auth
     */
    public function facebookProviderCallback($user)
    {
        $model = null;

        $city = $this->getCityModelByFacebookLocation($user->offsetGet('location'), $user->token);

        $account = Profile::firstOrCreate([
            'provider' => 'facebook',
            'external_id' => $user->getId()
        ]);

        if($account->user_id) {
            $model = $this->model->where('id', $account->user_id)->withTrashed()->first();
        }

        if($model && $model->trashed()) {
            $model->restore();
        }

        if (is_null($model)) {
            $model = $this->findOrCreateUserByFacebookProfile($account, $user);
        }

        if($user->offsetExists('birthday') && !$model->hasMeta('birthday')){
            list($month,$day,$year) = explode('/', $user->offsetGet('birthday'));
            $datetime = app(Carbon::class)->createFromDate($year, $month, $day);
            $model->setMeta('birthday', $datetime);
        }

        if($user->offsetExists('gender') && !$model->hasMeta('gender')){
            $model->setMeta('gender', $user->offsetGet('gender'));
        }

        if(is_null($model->city_id) && $city) {
            $model->update([
                'city_id' => $city->id
            ]);
        }

        auth()->login($model, true);
    }


    /**
     * @param $location array
     * @param $token string
     * @return Modules\Address\Entities\City
     */
    public function getCityModelByFacebookLocation($location, $token)
    {
        if(!is_array($location) || !isset($location['id'])){
            return null;
        }

        $service = new Facebook($token);

        $name = $service->getLocation($location['id'], 'location.city');

        return app(LocationRepository::class)->findCityByName($name);
    }


    /**
     * @param $account Modules\User\Entities\Profile
     * @param $user object
     * @return Modules\User\Entities\User
     */
    public function findOrCreateUserByFacebookProfile(Profile $account, $user)
    {
        $model = $this->model->whereEmail($user->getEmail())->withTrashed()->first();

        if(is_null($model)){
            $model = $account->user()->create([
                'email' => $user->getEmail(),
                'name'  => $user->offsetGet('name')
            ]);
        }

        if($account->wasRecentlyCreated && !$model->firstMedia('avatar')){
            $model->changeAvatar($user->getAvatar());
        }

        $account->update([
            'user_id' => $model->id
        ]);

        return $model;
    }

}