<?php

namespace App\Repositories;

use App\Repositories\BaseRepository;
use MenaraSolutions\Geographer\Country as GCountry;
use App\Services\GeonamesService;
use App\Services\RestcountriesService;
use App\Entities\Country;
use App\Entities\Region;
use App\Entities\City;

class LocationRepository {
    
    /**
     * @param $name string
     * @return App\Entities\City
     */
    function findCityByName($name)
    {
        return City::whereTranslation('name', $name)->first();
    }


    /**
     * @return App\Entities\Country
     */
    function updateOrCreateCountry($code, $locale, $data)
    {
        $country = Country::updateOrCreate([
            'iso_code' => $code
        ], array_only($data, [ 
            'geonames_id', 'area', 'continent', 'population', 
            'currency', 'calling_code', 'language', 'gini', 'capital', 'currency_symbol' 
        ]));

        $country->translateOrNew('en')->name = array_get($data, 'name');

        $country->translateOrNew($locale)->name = array_get($data, 'name_original');

        $country->save();

        return $country;
    }


    /**
     * @return void
     */
    function updateOrCreateRegion($code, $locale, $data)
    {        
        $region = Region::updateOrCreate([
            'iso_code' => $code
        ], array_only($data, [ 
            'geonames_id', 'country_id', 'population' 
        ]));

        $region->translateOrNew('en')->name = array_get($data, 'name');

        $region->translateOrNew($locale)->name = array_get($data, 'name_original');

        $region->save();

        return $region;
    }


    /**
     * @return void
     */
    function updateOrCreateCity($code, $locale, $data)
    {
        $city = City::updateOrCreate([
            'geonames_id' => $code
        ], array_only($data, [ 
            'country_id', 'region_id', 'population', 'lat', 'lng'
        ]));

        $city->translateOrNew('en')->name = array_get($data, 'name');

        $city->translateOrNew($locale)->name = array_get($data, 'name_original');

        $city->save();

        return $city;

    }


    /**
     * @return void
     */
    function downloadCountry($iso_code)
    {
        $country = GCountry::build($iso_code);

        $lang = $country->language;

        $restcountries = app(RestcountriesService::class)->get($iso_code);

        $translation = app(GeonamesService::class)->getName($country->geonamesCode, $lang);

        $model = $this->updateOrCreateCountry($iso_code, $lang, [
            'geonames_id' => $country->geonamesCode,
            'area' => $country->area,
            'continent' => $country->continent,
            'population' => $country->population,
            'currency' => $country->currency,
            'calling_code' => $country->phonePrefix,
            'language' => $lang,
            'gini' => $restcountries->gini,
            'capital' => $restcountries->capital,
            'currency_symbol' => collect($restcountries->currencies)->where('code', $country->currency)->first()->symbol,
            'name' => $restcountries->name,
            'name_original' => $translation,
        ]);

        foreach ($country->getStates() as $region) {
            $this->donwloadRegion($region, $lang, $model->id);
        }
    }



    /**
     * @return void
     */
    function donwloadRegion($region, $lang, $country_id)
    {
        $geonames = app(GeonamesService::class)->get($region->geonamesCode);

        $translation = app(GeonamesService::class)->getName($region->geonamesCode, $lang, $geonames);

        $model = $this->updateOrCreateRegion($region->isoCode, $lang, [
            'geonames_id' => $region->geonamesCode,
            'country_id' => $country_id,
            'population' => $geonames->population,
            'name' => $region->name,
            'name_original' => $translation,
        ]);
            
        foreach ($region->getCities() as $city) {
            $this->donwloadCity($city, $lang, $country_id, $model->id);
        }
    }


    /**
     * @return void
     */
    function donwloadCity($city, $lang, $country_id, $region_id)
    {
        $geonames = app(GeonamesService::class)->get($city->geonamesCode);

        if(!isset($geonames->fcodeName) || $geonames->fcodeName == 'populated place') {
            return false;
        }

        $translation = app(GeonamesService::class)->getName($city->geonamesCode, $lang, $geonames);

        $model = $this->updateOrCreateCity($city->geonamesCode, $lang, [
            'country_id' => $country_id,
            'region_id' => $region_id,
            'population' => $geonames->population,
            'lat' => $geonames->lat,
            'lng' => $geonames->lng,
            'name' => $geonames->asciiName,
            'name_original' => $translation,
        ]);
    }

}