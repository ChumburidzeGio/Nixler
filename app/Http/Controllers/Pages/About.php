<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class About extends Controller
{
    /**
     * Show the about page
     *
     * @return Response
     */
    public function __invoke()
    {
        return view('pages.about');
    }
}