<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    /**
     * Show the application welcome page.
     *
     * @return \Illuminate\Http\Response
     */
    public function welcome()
    {   
        $what = collect(trans('landing.what.items'))->chunk(2);

        $why = collect(trans('landing.why.items'))->chunk(3);

        $who = collect(trans('landing.who.items'))->chunk(3);

        return view('landing.page', compact('what', 'why', 'who'));
    }
}
