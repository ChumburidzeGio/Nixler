<?php

namespace App\Repositories;

use App\Repositories\BaseRepository;
use App\Entities\User;
use App\Entities\Metric;
use App\Repositories\LocationRepository;
use App\Repositories\ProductRepository;
use App\Entities\Profile;
use App\Services\Facebook;
use App\Services\PhoneService;
use App\Services\RecommService;
use App\Services\SystemService;
use App\Services\AnalyticsService;
use App\Services\UserAgentService;
use App\Events\UserFollowed;
use App\Events\UserUnfollowed;
use App\Notifications\SendVerificationCode;
use Carbon\Carbon;
use Session, DB;

class UserRepository extends BaseRepository {


    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return User::class;
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

        if($tab == 'products'){
            $data = capsule('stream')->whereSeller($user->id)->get();
            $view = 'products';
        }
        elseif($tab == 'followers'){
            $data = $user->followers()->isFollowing()->take(20)->get();
            $view = 'people';
        }
        elseif($tab == 'followings'){

            $data = $user->followings()->isFollowing()->take(20)->get();
            $view = 'people';
        }
        elseif($tab == 'photos'){
            $data = $user->media()->take(20)->get();
            $view = 'media';
        }
        elseif($tab == 'about'){
            $data = [];
            $view = 'about';
        }
        else {
            $data = capsule('stream')->whereLikeBy($user->id)->get();
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

        if($user->id == $target->id) return false;

        if($user->isFollowing($target->id)){

            $user->unfollow($target->id);

            event(new UserUnfollowed($target, $user));

        } else {

            $user->follow($target->id);

            event(new UserFollowed($target, $user));

        } 

        return $target;
    }


    /**
     * Follow user
     */
    public function streamRemoveBySource($user, $actor)
    {
        $actor->streamRemoveBySource('user:'.$user->id);
    }


    /**
     * Follow user
     */
    public function streamPushBySource($user, $actor)
    {
        $products = $user->products()->take(10)->orderBy('likes_count', 'desc')->active()->pluck('id');

        $actor->pushInStream($products, 'user:'.$user->id);
    }



    /**
     * Follow user
     */
    public function updateStats($user)
    {
        $user->update([
            'products_count' => $user->products()->active()->count(),
            'sales_count' => $user->sales()->count(),
            'followers_count' => $user->followers()->count(),
        ]);
    }


    /**
     * Soft delete user
     */
    public function deactivate()
    {
        $user = auth()->user();
        
        $user->products()->pluck('id')->map(function($product){
            app(ProductRepository::class)->hide($product->id);
        });

        return $user->delete();
    }


    /**
     * Soft delete user
     */
    public function activate($user)
    {
        $user->restore();

        $user->products()->pluck('id')->map(function($product){
            app(ProductRepository::class)->publish($product->id);
        });
    }


    /**
     * Callback for facebook auth
     */
    public function facebookProviderCallback($user)
    {
        $model = null;

        $city = $user->offsetExists('location') ? $this->getCityModelByFacebookLocation($user->offsetGet('location'), $user->token) : null;

        $account = Profile::firstOrCreate([
            'provider' => 'facebook',
            'external_id' => $user->getId()
        ]);

        if($account->user_id) {
            $model = $this->model->where('id', $account->user_id)->withTrashed()->first();
        }

        if($model && $model->trashed()) {
            Session::flash('message', __('Your account successfully restored. Have a good day and thank you for coming back!'));
            $this->activate($user);
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
     * @return App\Entities\City
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
     * @param $account App\Entities\Profile
     * @param $user object
     * @return App\Entities\User
     */
    public function findOrCreateUserByFacebookProfile(Profile $account, $user)
    {
        $model = $this->model->whereEmail($user->getEmail())->whereNotNull('email')->withTrashed()->first();

        if(is_null($model)){
            $model = $account->user()->create([
                'email' => $user->getEmail(),
                'name'  => $user->offsetGet('name')
            ]);
        }

        if($account->wasRecentlyCreated && !$model->firstMedia('avatar')){
            $model->uploadPhoto($user->getAvatar(), 'avatar');
        }

        $account->update([
            'user_id' => $model->id
        ]);

        return $model;
    }

    /**
     * @param $data array
     * @param ? $user \App\Entities\User|null
     * @return \App\Entities\User
     */
    public function update($data, $user = null)
    {
        $user = $user ? : auth()->user();

        $user->fill(array_only($data, [
            'username', 'name', 'email', 'city_id'
        ]));

        if(array_get($data, 'phone')) {
            $user->phone = PhoneService::parse(array_get($data, 'phone'), $user->country)->number;
        }
        
        if($user->phone != $user->getOriginal('phone')) {
            $user->verified = false;
            $code = mt_rand(100000, 999999);
            $user->setMeta('phone_vcode', $code);

            if (app()->environment('local')) {
                app(SystemService::class)->notify(new SendVerificationCode($code));
            } else {
                $user->notify(new SendVerificationCode($code));
            }
        }

        if(array_get($data, 'pcode') and $user->phone and array_get($data, 'pcode') == $user->getMeta('phone_vcode')) {
            $user->removeMeta('phone_vcode');
            $user->verified = true;
        }

        if(array_get($data, 'address')){
            $user->setMeta('address', array_get($data, 'address'));
        }

        if(array_get($data, 'headline')){
            $user->setMeta('headline', array_get($data, 'headline'));
        }

        if(array_get($data, 'website')){
            $user->setMeta('website', array_get($data, 'website'));
        }
        
        $user->save();

        return $user;
    }

    /**
     * @param $new string
     * @param ? $user \App\Entities\User|null
     * @return \App\Entities\User
     */
    public function setPassword($new, $user = null)
    {
        $user = $user ? : auth()->user();

        $user->password = bcrypt($new);

        $user->save();

        return $user;
    }

    /**
     * @param $user User
     */
    public function recommendProducts(User $user)
    {
        //$city = $user->city;

        //$locationFilter = $city ? " and earth_distance('lat','lng', ".floatval($city->lat).", ".floatval($city->lng).") < 50000" : "";

        $recommendations = capsule('reco')->forUser($user)->get();

        $products = app(ProductRepository::class)->findByIds($recommendations)->pluck('id');

        $user->pushInStream($products, 'recs');
    }



    /**
     * 
     */
    public function updateStreams()
    {
        $models = $this->model->whereHas('sessions', function($q){
            return $q->whereBetween('last_activity', [strtotime('-10 min'), strtotime('now')]);
        })->get();

        $models->map(function($model){
            $this->recommendProducts($model);
        });
    }


    /**
     * Refresh analytics data from GA
     *
     * @return boolean
     */
    public function updateAnalytics()
    {
        $metrics = app(AnalyticsService::class)->getBasicAnalyticsForPopularMerchants();

        return $metrics->map(function($metric) {
            return $this->findByIdAndSetAnalytics(...$metric);
        });
    }


    /**
     * Refresh analytics data from GA
     *
     * @return boolean
     */
    public function findByIdAndSetAnalytics($username, $data)
    {
        $user = $this->model->where('username', $username)->first();

        if(!$user) {
            return false;
        }

        foreach ($data as $metric => $values) {

            $values->map(function($value, $key) use ($user, $metric) {

                $metric = Metric::firstOrCreate([
                    'object_id' => $user->id,
                    'object_type' => get_class($user),
                    'key' => $metric,
                    'date' => date('Y-m-d'),
                    'target' => $key
                ], [
                    'value' => $value
                ]);

            });
            
        }

    }


    /**
     * Search in users
     *
     * @return \Illuminate\Http\Response
     */
    public function search($query)
    {
        return $this->model->whereKeyword($query)->take(6)->get();
    }


    /**
     * Search in users
     *
     * @return \Illuminate\Http\Response
     */
    public function getSessions()
    {
        $sessions = DB::table('sessions')->where('user_id', auth()->id())->whereNotNull('user_id')->get();
        
        return $sessions->map(function($session) {

            $carbon = Carbon::createFromTimestamp($session->last_activity);

            $geoip = geoip($session->ip_address);

            return [
                'id' => $session->id,
                'location' => array_get($geoip, 'country'). ', ' .array_get($geoip, 'city'),
                'user_agent' => app(UserAgentService::class)->forHumans($session->user_agent),
                'ip_address' => $session->ip_address,
                'time' => $carbon->diffForHumans(),
                'is_current' => (session()->getId() == $session->id)
            ];
        });
    }



    /**
     * Search in users
     *
     * @return \Illuminate\Http\Response
     */
    public function killSessionById($id)
    {
        return session()->getHandler()->destroy($id);
    }

}