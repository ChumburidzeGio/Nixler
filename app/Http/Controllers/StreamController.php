<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Entities\Product;
use App\Entities\ProductCategory;
use App\Repositories\ProductRepository;
use App\Repositories\UserRepository;

class StreamController extends Controller
{

    public function __construct(){
        parent::__construct();
    }


    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $capsule = capsule('stream');

        if($request->has('query') || $request->has('cat') || $request->has('tag')){

            $capsule = $capsule->search(request('query'));

            if(!$request->has('cat') && !$request->has('tag')){
                $users = app(UserRepository::class)->search($request->input('query'));
            }

        } else {

            $capsule = auth()->check() ? $capsule->recommendedFor(auth()->id()) : $capsule->popular();

        }

        $capsule = $capsule->get();

        return $request->isMethod('post') ? $capsule->toArray() : view('stream.index', compact('capsule', 'users'));
    }

}
