<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{

    /**
     * Show the application welcome page.
     *
     * @return \Illuminate\Http\Response
     */
    public function welcome()
    {   
            $this->seo()->setTitle(trans('landing.meta.title'));

            $this->seo()->setDescription(trans('landing.meta.description'));

            $this->seo()->opengraph()->setUrl(request()->fullUrl());
            
            $this->seo()->opengraph()->addProperty('type', 'website');

            $what = collect(trans('landing.what.items'))->chunk(2);

            $why = collect(trans('landing.why.items'))->chunk(3);

            $who = collect(trans('landing.who.items'))->chunk(4);

            return view('landing.page', compact('what', 'why', 'who'));

    }
}
