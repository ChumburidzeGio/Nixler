<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HelpController extends Controller
{
    /**
     * Return all Q/A
     *
     * @return Response
     */
    public function index()
    {
        $qa = capsule('help')->get();

        return view('help.index', compact('qa'));
    }
}
