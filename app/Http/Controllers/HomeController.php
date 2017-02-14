<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->middleware('auth');
        return view('home');
    }

    /**
     * Show the application welcome page.
     *
     * @return \Illuminate\Http\Response
     */
    public function welcome()
    {   
        //$this->seo()->setTitle('Welcome');

        $what = collect(trans('landing.what.items'))->chunk(2);

        $why = collect(trans('landing.why.items'))->chunk(3);

        $who = collect(trans('landing.who.items'))->chunk(3);

        return view('landing.page', compact('what', 'why', 'who'));
    }
}
