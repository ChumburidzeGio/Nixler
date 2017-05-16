<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Cache;

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
        return Cache::remember(md5($id.$type.$place), (60 * 24), function () use ($id, $type, $place) {

            $default = config('filesystems.media.'.$type.'.default') ? : abort(404);
            $sizes = config('filesystems.media.'.$type.'.sizes.'.$place) ? : abort(404);
            $width = array_last($sizes);
            $height = array_first($sizes);
            $media = app()->make(config('mediable.model'))->find($id);
            $path = $media ? $media->getAbsolutePath() : $default;

            Image::configure(array('driver' => 'gd'));

            $basic = Image::make($path);

            $func = function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            };

            if(is_null($height)){
                $basic->widen($width, $func);
            } elseif(is_null($width)){
                $basic->heighten($width, $func);
            } else {
                $basic->fit($height, $width, $func);
            }

            $response = new Response($basic->encode('jpg', 90));
            $response->header('Content-Type', 'image/jpg');
            $response->header('content-transfer-encoding', 'binary');
            $response->header('Pragma', 'public');
            $response->header('Cache-Control', 'max-age=86400');
            $response->header('Expires', gmdate('D, d M Y H:i:s \G\M\T', time() + 86400));

            return $response;
        });

    }
}
