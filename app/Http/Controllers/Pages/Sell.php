<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Sell extends Controller
{
    /**
     * Show the help page
     *
     * @return Response
     */
    public function __invoke()
    {
        return view('layouts.app');
    }
}