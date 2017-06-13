<?php

namespace App\Services;

use PeterColes\Languages\Maker as Languages;
use App\Entities\Country;
use Carbon\Carbon;
use Session, Cache, Linguist, Exception;

class LocationService
{

    /**
     * Check all possible ways to find locale for user and return its key as a string
     *
     * @return string
     */
	public function findLocale(){

		$segment = request()->segment(1);

		if(!in_array(request()->getHttpHost(), ['www.nixler.pl', 'nixler.app', 'www.nixler.ge'])) {
			header('Location: '.config('app.url'));
			exit();
		}

		$locale = null;

		if(Linguist::workingLocale()){

			$locale = Linguist::workingLocale();

		} elseif(auth()->check() && $this->isAvailableLocaleKey(auth()->user()->locale)) {

			$locale = auth()->user()->locale;

		} elseif(auth()->guest() && $this->isAvailableLocaleKey(Session::get('locale'))) {

			$locale = Session::get('locale');
		}

		if(is_null($locale) || (auth()->check() && !auth()->user()->country)){
			$locale = $this->setLocaleByGeo();
		}

		app()->setLocale($locale);

		Carbon::setLocale($locale);

		setlocale(LC_TIME, config('app.lang_to_locale.'.$locale));

		$timezone = auth()->check() ? auth()->user()->timezone : session('timezone');

		$currency = auth()->check() ? auth()->user()->currency : session('currency');

		$country = auth()->check() ? auth()->user()->country : session('country');

		config([
			'app.url' => url('/'),
			'app.currency' => $currency,
			'app.country' => $country,
			'app.timezone' => $timezone,
		]);

		return $locale;
	}


    /**
     * Set user language by Geo location
     *
     * @return string
     */
	public function setLocaleByGeo(){

		$tld = $this->getTLD();

		if($tld == 'pl') {
			$locale = 'pl';
			$currency = 'PLN';
			$country = 'PL';
			$timezone = 'Europe/Warsaw';
		}

		elseif($tld == 'ge') {
			$locale = 'ka';
			$currency = 'GEL';
			$country = 'GE';
			$timezone = 'Asia/Tbilisi';
		}

		else {
			$locale = 'en';
			$currency = 'USD';
			$country = 'US';
			$timezone = 'UTC';
		}

		if(auth()->guest()){
			Session::put('locale', $locale);
			Session::put('currency', $currency);
			Session::put('country', $country);
			Session::put('timezone', $timezone);
		}

		if(auth()->check()){
			auth()->user()->update(compact('locale', 'currency', 'country', 'timezone'));
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
     * Get from config all available locale keys 
     *
     * @return array
     */
	public function getAvailableLocaleKeys(){
		return config('linguist.locales');
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
     * Get user data by IP
     *
     * @return array
     */
	public function get($ip = null){

		$data = geoip($ip)->getLocation();

		$country = Country::where('iso_code', $data->iso_code)->first();

		if(!$country) {
			$country = Country::where('iso_code', 'GE')->first();
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