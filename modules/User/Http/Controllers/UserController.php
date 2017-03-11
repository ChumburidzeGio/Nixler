<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\User\Entities\User;

class UserController extends Controller
{

    /**
     * Show the user profile
     *
     * @return \Illuminate\Http\Response
     */
    public function find($id, Request $req)
    {   
        if(auth()->check() && auth()->user()->username == $id){
            $user = auth()->user();
        } else {
            $user = User::where('username', $id)->firstOrFail();
        }

        $tab_whitelist = ['products', 'followers', 'followings', 'photos'];

        $tab = $req->has('tab') && in_array($req->input('tab'), $tab_whitelist) ? $req->input('tab') : 'profile';

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

        return view('user::profile.'.$tab, compact('user', 'data', 'tab'));
    }




    /**
     * Follow user
     *
     * @return \Illuminate\Http\Response
     */
    public function follow($id, Request $request)
    {   
        $target = User::whereUsername($id)->firstOrFail();
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

        return redirect($target->link());
    }



    /**
     * Upload photo for user
     *
     * @return \Illuminate\Http\Response
     */
    public function uploadPhoto($id, Request $request)
    {   
        $this->validate($request, [
              '_s' => 'required|image',
              '_t' => 'required|numeric',
        ]);

        $target = User::whereUsername($id)->firstOrFail();
        $user = auth()->user();
        
        if($user->id != $target->id){
            return redirect()->back();
        }

        $user->uploadPhoto($request->file('_s'), ($request->input('_t') == 1 ? 'avatar' : 'cover'));

        return redirect($user->link('photos'));
    }



    /**
     * Redirect to users avatar
     *
     * @return \Illuminate\Http\Response
     */
    public function avatar($uid, $place, Request $request)
    {
        $user = app()->make(config('auth.providers.users.model'))->find($uid);

        $media = $user->getMedia('avatar')->first();
        $id = $media ? $media->id : '-';
        $ts = isset($media->created_at) ? strtotime($media->created_at) : 0;

        return redirect('/media/'.$id.'/avatar/'.$place.'.jpg?='.$ts, 302);
    }

}
