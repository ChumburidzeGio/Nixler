<?php

namespace App\Http\Controllers\Collection;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Index extends Controller
{
    /**
     * Show all collections
     *
     * @return Response
     */
    public function __invoke()
    {
        $collections = capsule('collections')->get();

        return $this->view('collections-index', compact('collections'));
    }
}