<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Entities\Product;
use App\Entities\Comment;
use MediaUploader;
use App\Services\SystemService;
use Intervention\Image\ImageManagerStatic as Image;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $product = Product::findOrFail($request->target);
        return $product->comments()->latest('id')->paginate()->map(function($comment){
            return [
                'id' => $comment->id,
                'avatar' => $comment->author->avatar('comments'),
                'author' => $comment->author->name,
                'attachment' => media($comment, 'product', 'comment-attachment', null),
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

        if($request->file('file')) {

            $source = $request->file('file');

            $prop = 'attachment';

            try {

                Image::configure(array('driver' => 'gd'));

                $image = Image::make($source)->resize(null, 900, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->encode('jpg');

                $media = MediaUploader::fromString($image)
                ->toDirectory('comments/'.$prop)
                ->useHashForFilename()
                ->setAllowedAggregateTypes(['image'])
                ->setStrictTypeChecking(true)
                ->upload();

                $comment->attachMedia($media, $prop);

                $comment->update([
                    'media_id' => $media->id
                ]);

            } catch (\Exception $e){
                app(SystemService::class)->reportException($e);
            }

        }

        return [
            'id' => $comment->id,
            'avatar' => $comment->author->avatar('comments'),
            'author' => $comment->author->name,
            'attachment' => media($comment, 'product', 'comment-attachment', null),
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
