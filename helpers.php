<?php

use App\Services\CurrencyService;

if (! function_exists('money')) {

    function money($currency, $amount = null) {
        return (new CurrencyService)->get($currency, $amount);
    }
}

if (! function_exists('media')) {

    function media($media, $type, $place, $default = '-') {

        if(is_object($media) && $media) {
            $media = (isset($media->id) && $media->getTable() == 'media') ? $media->id : $media->media_id;
        }

        if(is_null($default) && !$media) {
            return null;
        }

        return route('photo', [
            'id' => $media ?? $default,
            'type' => $type,
            'place' => $place,
        ]);

    }
}

if (! function_exists('capsule')) {

    function capsule($name) {

    	$capsule = array_get([
            'stream' => \App\Capsules\StreamCapsule::class,
            'reco' => \App\Capsules\RecoCapsule::class,
            'collections' => \App\Capsules\CollectionsCapsule::class,
            'frontend' => \App\Capsules\FrontendCapsule::class,
            'comments' => \App\Capsules\CommentsCapsule::class,
        ], $name);

        return app($capsule);

    }
}