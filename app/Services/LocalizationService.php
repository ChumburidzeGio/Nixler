<?php

namespace App\Services;

class LocalizationService
{
    public function __construct()
    {
        $locales = ['pl', 'ka', 'ru'];

		$strings = [];

		foreach ($locales as $locale) 
		{
			$file = file_get_contents(resource_path("lang/{$locale}.json"));

			$strings[$locale] = json_decode($file, true);
		}

		$keys = array_flip(array_unique(array_flatten(array_map('array_keys', $strings))));

		$keys = array_map(function($string) {
			return "";
		}, $keys);

		foreach ($strings as $locale => $lstrings) 
		{
			$strings[$locale] = $lstrings + $keys;
		}

		return compact('keys', 'strings');
    }

}