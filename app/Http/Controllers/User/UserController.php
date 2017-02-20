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
    public function find($id)
    {   
        $user = User::where('username', $id)->firstOrFail();
        return view('user.profile', compact('user'));
    }
}
