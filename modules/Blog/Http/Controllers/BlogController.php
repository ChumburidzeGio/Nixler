<?php

namespace Modules\Blog\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Blog\Entities\Article;

class BlogController extends Controller
{

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show($slug)
    {   
        $article = Article::whereSlug($slug)->first();
        return view('blog::show', compact('article'));
    }

}
