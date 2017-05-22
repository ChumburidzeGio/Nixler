<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use App\Entities\User;
use Illuminate\Http\Request;
use Hash;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('guest', ['except' => 'logout']);
    }


    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        if (!session()->has('url.intended')) {
            session(['url.intended' => url()->previous()]);
        }

        return view('auth.login');    
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        $username = $email = $request->input('email');
        $password = $request->input('password');

        $attempt = Auth::attempt(compact('email', 'password'), true);

        if(!$attempt) {
            $attempt = Auth::attempt(compact('username', 'password'), true);
        }

        if(!$attempt) {

            $user = User::where('email', $email)->orWhere('username', $username)->withTrashed()->first();

            if($user && $user->trashed() && Hash::check($password, $user->password)) {
                $user->restore();
                Auth::login($user, true);
                $attempt = true;
            }

        }

        return $attempt;
    }
}
