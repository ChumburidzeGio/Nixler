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
        $this->seo()->setTitle('Welcome to Nixler');
        $this->seo()->setDescription('E-commerce platform focused on simplicity and sociality. Buy and sell, its free just join us.');
        $this->seo()->opengraph()->setUrl(request()->fullUrl());
        $this->seo()->opengraph()->addProperty('type', 'website');

        $what = collect(trans('landing.what.items'))->chunk(2);

        $why = collect(trans('landing.why.items'))->chunk(3);

        $who = collect(trans('landing.who.items'))->chunk(3);

        return view('landing.page', compact('what', 'why', 'who'));
    }
}
