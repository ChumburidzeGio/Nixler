<?php

namespace Modules\Comment\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $product = \Modules\Product\Entities\Product::findOrFail($request->target);
        return $product->comments()->sortBy('most_recent')->paginate()->map(function($comment){
            return [
                'id' => $comment->id,
                'avatar' => $comment->author->avatar('comments'),
                'author' => $comment->author->name,
                'text' => nl2br(str_limit($comment->text, 1000)),
                'time' => $comment->created_at->format('c'),
                'can_delete' => auth()->check() && auth()->user()->can('delete', $comment) ? 1 : 0
            ];
        })->toJson();
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('comment::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', \Modules\Comment\Entities\Comment::class);

        $product = \Modules\Product\Entities\Product::findOrFail($request->target);
        $comment = $product->comment($request->input('text'));

        return [
            'id' => $comment->id,
            'avatar' => $comment->author->avatar('comments'),
            'author' => $comment->author->name,
            'text' => nl2br(str_limit($comment->text, 1000)),
            'time' => $comment->created_at->format('c'),
            'can_delete' => auth()->check() && auth()->user()->can('delete', $comment) ? 1 : 0
        ];
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('comment::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('comment::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id, Request $request)
    {
        $product = \Modules\Product\Entities\Product::findOrFail($request->target);
        $comment = $product->comments()->findOrFail($id);

        $this->authorize('delete', $comment);

        return [
            'success' => $comment->delete()
        ];
    }
}
