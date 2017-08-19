<?php

namespace App\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Cache;
use Intervention\Image\ImageManagerStatic as Image;
use App\Entities\Media;

class MediaRepository extends BaseRepository {

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return Media::class;
    }


    /**
     * @param $data array
     * @return Article
     */
    public function generate ($media, $type, $place)
    {
        $hash = md5(($media ? $media->id : '-').$type.$place);

        return Cache::remember($hash, (60 * 24 * 120), function () use ($media, $type, $place) {

            $default = config("filesystems.media.{$type}.default") ? : abort(404);
            $sizes = config("filesystems.media.{$type}.sizes.{$place}") ? : abort(404);
            $width = array_last($sizes);
            $height = array_first($sizes);
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
                $basic->heighten($height, $func);
            } else {
                $basic->fit($height, $width);
            }

            return $basic->encode('jpg', 100);

        });

    }

}
