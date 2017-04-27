<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\Product\Repositories\TagRepository;

class TagsController extends Controller
{

    /**
     * @var PostRepository
     */
    protected $repository;

    public function __construct(TagRepository $repository){
        $this->repository = $repository;
    }


    /**
     * Display tags by query
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
    	return $this->repository->search($request->input('query'))->take(10)->get()->pluck('name');
    }


    /**
     * Find or create tag
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
    	return $this->repository->firstOrCreate($request->input('tag'));
    }

}