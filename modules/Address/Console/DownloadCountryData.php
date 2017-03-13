<?php

namespace Modules\Address\Console;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client as GuzzleHttp;
use Modules\Address\Entities\Country;
use Modules\Address\Entities\Region;
use Modules\Address\Entities\City;
use MenaraSolutions\Geographer\Country as GCountry;

class DownloadCountryData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'countries:download {iso_code}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download data, regions and cities for particular country';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Model::unguard();

        $this->updateTables($this->argument('iso_code'));
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

        $country->translateOrNew('en')->name = $rcData->name;
        $country->translateOrNew($lcData->language)->name = $this->geonamesGetName($lcData->geonamesCode, $lcData->language);
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

            $translation = $this->geonamesGetName($lcRegion->geonamesCode, $lcData->language, $geRegion);
            $region->translateOrNew('en')->name = $lcRegion->name;
            $region->translateOrNew($lcData->language)->name = $translation;

            $region->save();

            $this->findAndUpdateCities($lcData, $lcRegion, $cid, $region->id);
        }

    }

    /**
     * Add cities for region
     *
     * @return void
     */
    public function findAndUpdateCities($lcData, $lcRegion, $cid, $rid)
    {
        $cities = $lcRegion->getCities();

        foreach ($cities as $lcCity) {
            
            $geCity = $this->geonamesGet($lcCity->geonamesCode, $lcData->language);

            if(!isset($geCity->fcodeName) || $geCity->fcodeName == 'populated place') continue;

            $city = City::updateOrCreate([
                'geonames_id' => $lcCity->geonamesCode
            ],[
                'country_id' => $cid,
                'region_id' => $rid,
                'population' => $geCity->population,
            ]);

            $translation = $this->geonamesGetName($lcCity->geonamesCode, $lcData->language, $geCity);
            $city->translateOrNew('en')->name = $geCity->asciiName;
            $city->translateOrNew($lcData->language)->name = $translation;

            $city->save();
        }

    }
}