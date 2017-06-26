<?php

use App\Services\CurrencyService;

if (! function_exists('money')) {

    function money($currency, $amount = null) {
        return (new CurrencyService)->get($currency, $amount);
    }
}

if (! function_exists('media')) {

    function media($media, $type, $place) {

    	if(is_object($media) && $media) {
    		$media = isset($media->id) ? $media->id : $media->media_id;
    	}

        return route('photo', [
        	'id' => $media ?? '-',
        	'type' => $type,
        	'place' => $place,
        ]);

    }
}