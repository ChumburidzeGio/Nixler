<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Entities\Product;
use App\Entities\Comment;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $product = Product::findOrFail($request->target);
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
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', Comment::class);

        $product = Product::findOrFail($request->target);
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
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id, Request $request)
    {
        $product = Product::findOrFail($request->target);
        $comment = $product->comments()->findOrFail($id);

        $this->authorize('delete', $comment);

        return [
            'success' => $comment->delete()
        ];
    }
}
