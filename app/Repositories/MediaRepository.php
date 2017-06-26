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

        return Cache::remember($hash, (60 * 24), function () use ($media, $type, $place) {

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

            return $basic->encode('jpg', 90);

        });

    }


    /**
     * @param $data array
     * @return Article
     */
    public function combo($media_ids, $size = 600)
    {
        $medias = $this->model->whereIn('id', $media_ids)->get();

        if(!$medias->count()) {
            return false;
        }

        if($medias->count() == 1) {

            $partial = Image::make($medias->first()->getAbsolutePath());

            $partial->fit($size, $size);

            return $partial;

        }

        if($medias->count() !== 4) {
            $medias->take(2);
        }

        $img = Image::canvas($size, $size);

        $placements = ($medias->count() == 4) ? [
            'bottom-right', 'top-right', 'top-left', 'bottom-left'
        ] : [
            'left', 'right'
        ];

        $partial_width = $size / 2;

        $partial_height = ($medias->count() == 4) ? $size / 2 : $size;

        foreach ($medias as $key => $media) {

            $partial = Image::make($media->getAbsolutePath());

            $partial->fit($partial_width, $partial_height);

            $img->insert($partial, array_get($placements, $key));

        }

        return $img;

    }

}
