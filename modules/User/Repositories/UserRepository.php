<?php

namespace Modules\User\Repositories;

use App\Repositories\BaseRepository;
use Modules\User\Entities\User;

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
     *
     * @return \Illuminate\Http\Response
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
        }
        elseif($tab == 'followers'){
            $data = $user->followers()->take(20)->get();
        }
        elseif($tab == 'followings'){
            $data = $user->followings()->take(20)->get();
        }
        elseif($tab == 'photos'){
            $data = $user->media()->take(20)->get();
        }
        else {
            $data = $user->liked()->withMedia()->take(20)->get();
        }

        return compact('user', 'data', 'tab');
    }


    /**
     * Follow user by username
     *
     * @return \Illuminate\Http\Response
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
                $user->followCallback($target);
            } 

        }

        return $target;
    }

}