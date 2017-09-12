<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\BlogRepository;
use Illuminate\Validation\Rule;

class BlogController extends Controller
{
    protected $repository;

    public function __construct(BlogRepository $repository) {
        parent::__construct();
        $this->repository = $repository;
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
       $model = $this->repository->create();

       return redirect()->route('articles.edit', $model->id);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function edit($slug, Request $request)
    {
        $article = $this->repository->findBySlug($slug);

        return $this->view('articles-edit', compact('article'));
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function update($slug, Request $request)
    {
        $this->validate($request, [
            'title' => 'required|string',
            'slug' => ['required', 'alpha_dash', Rule::unique('articles')->ignore($slug, 'slug')],
            'body' => ['required', 'string'],
        ]);

        $model = $this->repository->update($slug, $request->only('title', 'slug', 'body'));

        return redirect()->route('articles.edit', [
            'id' => $model->slug
        ]);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $article = $this->repository->findBySlug($slug);

        return $this->view('articles-show', compact('article'));
    }

}