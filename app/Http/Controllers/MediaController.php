<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
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
        return response()->photo(
            app(MediaRepository::class)->generate($id, $type, $place)
        );
    }
}
