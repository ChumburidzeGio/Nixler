<?php

namespace Modules\Address\Services;

use PeterColes\Languages\Maker as Languages;
use Session, Cache;

class LocationService
{

    /**
     * Check all possible ways to find locale for user and return its key as a string
     *
     * @return string
     */
	public function findLocale(){

		$segment = request()->segment(1);

		$locale = null;

		if($this->isAvailableLocaleKey($segment)){

			$locale = $segment;

		} elseif(auth()->check() && $this->isAvailableLocaleKey(auth()->user()->locale)) {

			$locale = auth()->user()->locale;

		} elseif(auth()->guest() && $this->isAvailableLocaleKey(Session::get('locale'))) {

			$locale = Session::get('locale');
		}

		if(is_null($locale) || (auth()->check() && !auth()->user()->country)){
			$locale = $this->setLocaleByGeo();
		}

		app()->setLocale($locale);

		return $locale;
	}


    /**
     * Set user language by Geo location
     *
     * @return string
     */
	public function setLocaleByGeo(){

		$geoData = $this->get();
		$locale = array_get($geoData, 'locale');

		if($this->isAvailableLocaleKey($locale)){
			Session::put('locale', $locale);
		} else {
			$locale = null;
		}

		if(auth()->check()){
			auth()->user()->setGeoData($geoData);
		}

		return $locale;
	}



    /**
     * Update user locale by language key
     *
     * @return mixed
     */
	public function updateLocaleByKey($locale){

		if(!$this->isAvailableLocaleKey($locale)){
			return false;
		}

		if(auth()->check()){
			$user = auth()->user();
			$user->locale = $locale;
			$user->update();
		} else {
			Session::put('locale', $locale);
		}

		return $locale;
	}



    /**
     * Check if url first segment is valid language key
     *
     * @return void
     */
	public function segment(){

		$segment = request()->segment(1);

		if($this->isAvailableLocaleKey($segment)){
			return $segment;
		}

		return null;

	}



    /**
     * Get from config all available locale keys 
     *
     * @return array
     */
	public function getAvailableLocaleKeys(){
		return is_array(config('app.locales')) ? config('app.locales') : [config('app.locale')];
	}




    /**
     * Check if locale key is valid
     *
     * @return boolean
     */
	public function isAvailableLocaleKey($code){
		return (!in_array($code, $this->getAvailableLocaleKeys())) ? false : true;
	}




    /**
     * Get availabe locale names in native
     *
     * @return array
     */
	public function getAvailableLocales(){
		return (new Languages)->lookup($this->getAvailableLocaleKeys(), 'mixed');
	}




    /**
     * Get all countries around the world
     *
     * @return object
     */
	public function getCountries(){
		return Cache::remember('restcountries.all', (60 * 24 * 30), function () {
		    return collect(json_decode(file_get_contents('https://restcountries.eu/rest/v1/all')));
		});
	}




    /**
     * Get country info by country code
     *
     * @return object
     */
	public function getCountryParamByCode($code, $param){
		return $this->getCountries()->where('alpha2Code', $code)->pluck($param)->first();
	}




    /**
     * Get user data by IP
     *
     * @return array
     */
	public function get($ip = null){

		$data = geoip($ip)->getLocation();

		$country = (new \Modules\Address\Entities\Country)->where('iso_code', $data->iso_code)->first();

		if(!$country) {
			$country = (new \Modules\Address\Entities\Country)->where('iso_code', 'GE')->first();
		}
		
		$currency = $country->currency;
		$country = $data->iso_code;
		$timezone =$data->timezone;
		$city = $data->city;
		$locale = array_first($this->getCountryParamByCode($country, 'languages'));

		return compact('currency', 'country', 'locale', 'timezone', 'city');
	}




    /**
     * Get TLD from url
     *
     * @return string
     */
	public function getTLD(){
		return substr(request()->root(), strrpos(request()->root(), ".")+1);
	}

}