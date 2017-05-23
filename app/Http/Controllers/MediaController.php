<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Repositories\MediaRepository;

class MediaController extends Controller
{
    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {
        $media = app()->make(config('mediable.model'))->find($id);
        $media->delete();
        return redirect()->back();
    }


    public function generate ($id = '-', $type, $place)
    {   
        $media = app()->make(config('mediable.model'))->find($id);

        return response()->photo(
            app(MediaRepository::class)->generate($media, $type, $place), $media
        );
    }
}
