<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Help extends Controller
{
    /**
     * Show the help page
     *
     * @return Response
     */
    public function __invoke()
    {
    	$qa = json_decode(file_get_contents(resource_path('docs/qa.ka.json')));
    	
        return view('pages.help', compact('qa'));
    }
}