<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\OrderShipped;
use Illuminate\Support\Facades\Mail;
use App\User;

class UserController extends Controller
{
    /**
     * Show the user profile
     *
     * @return \Illuminate\Http\Response
     */
    private function user($id)
    {   
        if(auth()->check() && auth()->user()->username == $id){
            $user = auth()->user();
        } else {
            $user = User::where('username', $id)->firstOrFail();
        }

        $user->liked_count = $user->liked()->count();
        $user->selling_count = $user->products()->where('status', 'active')->count();
        $user->followers_count = $user->followers()->count();
        $user->followings_count = $user->followings()->count();
        $user->media_count = $user->media()->count();

        return $user;
    }

    /**
     * Show the user profile
     *
     * @return \Illuminate\Http\Response
     */
    private function generate($user, $view, $data, $page)
    {   
        return view('user.'.$view, compact('user', 'data', 'page'));
    }


    /**
     * Show the user profile
     *
     * @return \Illuminate\Http\Response
     */
    public function find($id)
    {   
        $user = $this->user($id);
        $data = $user->liked()->withMedia()->take(20)->get();

        return $this->generate($user, 'profile', $data, 'liked');
    }


    /**
     * Show the user profile
     *
     * @return \Illuminate\Http\Response
     */
    public function products($id)
    {   
        $user = $this->user($id);
        
        $data = $user->products()->where('status', 'active')->withMedia()->take(20)->get();

        return $this->generate($user, 'products', $data, 'products');
    }


    /**
     * Show the user profile
     *
     * @return \Illuminate\Http\Response
     */
    public function followers($id)
    {   
        $user = $this->user($id);
        
        $data = $user->followers()->get();

        return $this->generate($user, 'followers', $data, 'followers');
    }


    /**
     * Show the user profile
     *
     * @return \Illuminate\Http\Response
     */
    public function followings($id)
    {   
        $user = $this->user($id);

        $data = $user->followings()->get();

        return $this->generate($user, 'followers', $data, 'followings');
    }


    /**
     * Show the user profile
     *
     * @return \Illuminate\Http\Response
     */
    public function media($id)
    {   
        $user = $this->user($id);

        return $this->generate($user, 'media', [], 'media');
    }
}
