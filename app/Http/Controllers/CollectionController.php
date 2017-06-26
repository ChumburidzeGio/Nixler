<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Repositories\CollectionRepository;

class CollectionController extends Controller
{

    /**
     * @var PostRepository
     */
    protected $repository;

    public function __construct(CollectionRepository $repository){

        parent::__construct();

        $this->repository = $repository;

    }


    /**
     * Show the collection page
     *
     * @return \Illuminate\Http\Response
     */
    public function find($id)
    {
        $collection = $this->repository->find($id);

        //$this->meta('title', "{$collection->title}");
        //$this->meta('description', $product->description);
        //$this->meta('image', $product->photo('full'));
        //$this->meta('type', 'product');

        return view('collection.show', compact('collection'));
    }

}