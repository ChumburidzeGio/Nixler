<?php

namespace Modules\Address\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client as GuzzleHttp;
use Modules\Address\Entities\Country;
use Modules\Address\Entities\Region;
use Modules\Address\Entities\City;
use MenaraSolutions\Geographer\Country as GCountry;

class AddressDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->updateTables('GE');
        $this->updateTables('PL');
        $this->updateTables('UA');
        
    }

    /**
     * Add seperate country by code
     *
     * @return void
     */
    public function updateTables($code)
    {
        $country = $this->findAndupdateCountry($code);
        $regions = $this->findAndUpdateRegions($country['lcData'], $country['country']->id);
    }

    /**
     * Add seperate country by code
     *
     * @return void
     */
    public function findAndupdateCountry($code)
    {
        $lcData = GCountry::build($code);

        $rcData = $this->restCountriesGet($code);

        $country = Country::updateOrCreate([
            'iso_code' => $code
        ],[
            'geonames_id' => $lcData->geonamesCode,
            'area' => $lcData->area,
            'continent' => $lcData->continent,
            'population' => $lcData->population,
            'currency' => $lcData->currency,
            'calling_code' => $lcData->phonePrefix,
            'language' => $lcData->language,

            'gini' => $rcData->gini,
            'capital' => $rcData->capital,
            'currency_symbol' => collect($rcData->currencies)->where('code', $lcData->currency)->first()->symbol
        ]);

        $country->fill([
            'en'  => ['name' => $rcData->name],
            "{$lcData->language}"  => ['name' => $this->geonamesGetName($lcData->geonamesCode, $lcData->language)],
        ]);

        $country->save();

        return compact('country', 'lcData');
    }


    private function restCountriesGet($code) {

        return json_decode(((new GuzzleHttp)->request('GET', 'https://restcountries.eu/rest/v2/alpha/'.$code))->getBody());

    }


    private function geonamesGet($id, $locale) {

        $url = 'http://api.geonames.org/getJSON?geonameId='.$id.'&username=nixler';
        return json_decode(((new GuzzleHttp)->request('GET', $url))->getBody());

    }


    private function geonamesGetName($id, $locale, $response = null) {

        $response = $response ? : $this->geonamesGet($id, $locale);
        $name = collect($response->alternateNames)->where('lang', $locale)->last();
        return isset($name->name) ? $name->name : $response->name;

    }

    /**
     * Add regions for country
     *
     * @return void
     */
    public function findAndUpdateRegions($lcData, $cid)
    {
        $regions = $lcData->getStates();

        foreach ($regions as $lcRegion) {
            
            $geRegion = $this->geonamesGet($lcRegion->geonamesCode, $lcData->language);

            $region = Region::updateOrCreate([
                'iso_code' => $lcRegion->isoCode
            ],[
                'geonames_id' => $lcRegion->geonamesCode,
                'country_id' => $cid,
                'population' => $geRegion->population,
            ]);

            $region->fill([
                'en'  => ['name' => $lcRegion->name],
                "{$lcData->language}"  => ['name' => $this->geonamesGetName($lcRegion->geonamesCode, $lcData->language, $geRegion)],
            ]);

            $region->save();

            $this->findAndUpdateCities($lcData, $lcRegion, $cid, $region->id);
        }

    }

    /**
     * Add regions for country
     *
     * @return void
     */
    public function findAndUpdateCities($lcData, $lcRegion, $cid, $rid)
    {
        $cities = $lcRegion->getCities();

        foreach ($cities as $lcCity) {
            
            $geCity = $this->geonamesGet($lcCity->geonamesCode, $lcData->language);

            if($geCity->fcodeName == 'populated place') continue;

            $city = City::updateOrCreate([
                'geonames_id' => $lcCity->geonamesCode
            ],[
                'country_id' => $cid,
                'region_id' => $rid,
                'population' => $geCity->population,
            ]);

            $city->fill([
                'en'  => ['name' => $geCity->asciiName],
                "{$lcData->language}"  => ['name' => $this->geonamesGetName($lcCity->geonamesCode, $lcData->language, $geCity)],
            ]);

            $city->save();
        }

    }

}
