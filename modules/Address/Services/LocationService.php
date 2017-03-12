<?php

namespace Modules\Address\Services;

use Torann\GeoIP\GeoIP;
use Torann\GeoIP\Services\MaxMindDatabase;
use PeterColes\Languages\Maker as Languages;
use Illuminate\Support\Facades\Cache;
use Session;

class LocationService
{
    
	private $provider;


    /**
     * Create a Location Service instance
     *
     * @return void
     */
	public function __construct()
	{
		
		$dd_path = storage_path('app/geoip.mmdb');
    	$du_url = 'https://geolite.maxmind.com/download/geoip/database/GeoLite2-City.mmdb.gz';

    	$config = [
    		'service' => 'maxmind_database',
    		'services' => [
    			'maxmind_database' => [
				    'class' => MaxMindDatabase::class,
				    'database_path' => config('services.maxmind.database_path') ? : $dd_path,
				    'update_url' => config('services.maxmind.update_url') ? : $du_url,
				    'locales' => ['en'],
				]
			],
			'cache' => 'none'
    	];

    	$this->provider = new GeoIP($config, app()['cache']);

	}


    /**
     * Update MaxMind database file
     *
     * @return void
     */
	public function updateDatabase(){
		return $this->provider->getService()->update();
	}



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

		if(is_null($locale)){
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

		if(is_null($ip)){
			$ip = request()->ip();
		}

		$data = $this->provider->getLocation($ip);

		$country = (new \Modules\Address\Entities\Country)->where('iso_code', array_get($data, 'iso_code'))->first();

		$currency = $country->currency;
		$country = array_get($data, 'iso_code');
		$timezone = array_get($data, 'timezone');
		$city = array_get($data, 'city');
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