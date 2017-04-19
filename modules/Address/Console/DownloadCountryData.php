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
            'zip_format' => $this->zipFormatByISO($code),
            'currency_symbol' => collect($rcData->currencies)->where('code', $lcData->currency)->first()->symbol
        ]);

        $country->translateOrNew('en')->name = $rcData->name;
        $country->translateOrNew($lcData->language)->name = $this->geonamesGetName($lcData->geonamesCode, $lcData->language);
        $country->save();

        return compact('country', 'lcData');
    }


    private function restCountriesGet($code) {
        $guzzle = new GuzzleHttp;
        $req = $guzzle->request('GET', 'https://restcountries.eu/rest/v2/alpha/'.$code);
        return json_decode($req->getBody());
    }


    private function geonamesGet($id, $locale) {
        $guzzle = new GuzzleHttp;
        $username = collect(['nixler', 'nixe', 'nixlerinfo'])->random();
        $req = $guzzle->request('GET', 'http://api.geonames.org/getJSON?geonameId='.$id.'&username='.$username);
        return json_decode($req->getBody());
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

    /**
     * Get Postal code by country ISO code
     *
     * @return void
     */
    public function zipFormatByISO($iso)
    {

        return array_get([ 
            "GB" => "GIR[ ]?0AA|((AB|AL|B|BA|BB|BD|BH|BL|BN|BR|BS|BT|CA|CB|CF|CH|CM|CO|CR|CT|CV|CW|DA|DD|DE|DG|DH|DL|DN|DT|DY|E|EC|EH|EN|EX|FK|FY|G|GL|GY|GU|HA|HD|HG|HP|HR|HS|HU|HX|IG|IM|IP|IV|JE|KA|KT|KW|KY|L|LA|LD|LE|LL|LN|LS|LU|M|ME|MK|ML|N|NE|NG|NN|NP|NR|NW|OL|OX|PA|PE|PH|PL|PO|PR|RG|RH|RM|S|SA|SE|SG|SK|SL|SM|SN|SO|SP|SR|SS|ST|SW|SY|TA|TD|TF|TN|TQ|TR|TS|TW|UB|W|WA|WC|WD|WF|WN|WR|WS|WV|YO|ZE)(\d[\dA-Z]?[ ]?\d[ABD-HJLN-UW-Z]{2}))|BFPO[ ]?\d{1,4}",
            "JE" => "JE\d[\dA-Z]?[ ]?\d[ABD-HJLN-UW-Z]{2}",
            "GG" => "GY\d[\dA-Z]?[ ]?\d[ABD-HJLN-UW-Z]{2}",
            "IM" => "IM\d[\dA-Z]?[ ]?\d[ABD-HJLN-UW-Z]{2}",
            "US" => "\d{5}([ \-]\d{4})?",
            "CA" => "[ABCEGHJKLMNPRSTVXY]\d[ABCEGHJ-NPRSTV-Z][ ]?\d[ABCEGHJ-NPRSTV-Z]\d",
            "DE" => "\d{5}",
            "JP" => "\d{3}-\d{4}",
            "FR" => "\d{2}[ ]?\d{3}",
            "AU" => "\d{4}",
            "IT" => "\d{5}",
            "CH" => "\d{4}",
            "AT" => "\d{4}",
            "ES" => "\d{5}",
            "NL" => "\d{4}[ ]?[A-Z]{2}",
            "BE" => "\d{4}",
            "DK" => "\d{4}",
            "SE" => "\d{3}[ ]?\d{2}",
            "NO" => "\d{4}",
            "BR" => "\d{5}[\-]?\d{3}",
            "PT" => "\d{4}([\-]\d{3})?",
            "FI" => "\d{5}",
            "AX" => "22\d{3}",
            "KR" => "\d{3}[\-]\d{3}",
            "CN" => "\d{6}",
            "TW" => "\d{3}(\d{2})?",
            "SG" => "\d{6}",
            "DZ" => "\d{5}",
            "AD" => "AD\d{3}",
            "AR" => "([A-HJ-NP-Z])?\d{4}([A-Z]{3})?",
            "AM" => "(37)?\d{4}",
            "AZ" => "\d{4}",
            "BH" => "((1[0-2]|[2-9])\d{2})?",
            "BD" => "\d{4}",
            "BB" => "(BB\d{5})?",
            "BY" => "\d{6}",
            "BM" => "[A-Z]{2}[ ]?[A-Z0-9]{2}",
            "BA" => "\d{5}",
            "IO" => "BBND 1ZZ",
            "BN" => "[A-Z]{2}[ ]?\d{4}",
            "BG" => "\d{4}",
            "KH" => "\d{5}",
            "CV" => "\d{4}",
            "CL" => "\d{7}",
            "CR" => "\d{4,5}|\d{3}-\d{4}",
            "HR" => "\d{5}",
            "CY" => "\d{4}",
            "CZ" => "\d{3}[ ]?\d{2}",
            "DO" => "\d{5}",
            "EC" => "([A-Z]\d{4}[A-Z]|(?:[A-Z]{2})?\d{6})?",
            "EG" => "\d{5}",
            "EE" => "\d{5}",
            "FO" => "\d{3}",
            "GE" => "\d{4}",
            "GR" => "\d{3}[ ]?\d{2}",
            "GL" => "39\d{2}",
            "GT" => "\d{5}",
            "HT" => "\d{4}",
            "HN" => "(?:\d{5})?",
            "HU" => "\d{4}",
            "IS" => "\d{3}",
            "IN" => "\d{6}",
            "ID" => "\d{5}",
            "IL" => "\d{5}",
            "JO" => "\d{5}",
            "KZ" => "\d{6}",
            "KE" => "\d{5}",
            "KW" => "\d{5}",
            "LA" => "\d{5}",
            "LV" => "\d{4}",
            "LB" => "(\d{4}([ ]?\d{4})?)?",
            "LI" => "(948[5-9])|(949[0-7])",
            "LT" => "\d{5}",
            "LU" => "\d{4}",
            "MK" => "\d{4}",
            "MY" => "\d{5}",
            "MV" => "\d{5}",
            "MT" => "[A-Z]{3}[ ]?\d{2,4}",
            "MU" => "(\d{3}[A-Z]{2}\d{3})?",
            "MX" => "\d{5}",
            "MD" => "\d{4}",
            "MC" => "980\d{2}",
            "MA" => "\d{5}",
            "NP" => "\d{5}",
            "NZ" => "\d{4}",
            "NI" => "((\d{4}-)?\d{3}-\d{3}(-\d{1})?)?",
            "NG" => "(\d{6})?",
            "OM" => "(PC )?\d{3}",
            "PK" => "\d{5}",
            "PY" => "\d{4}",
            "PH" => "\d{4}",
            "PL" => "\d{2}-\d{3}",
            "PR" => "00[679]\d{2}([ \-]\d{4})?",
            "RO" => "\d{6}",
            "RU" => "\d{6}",
            "SM" => "4789\d",
            "SA" => "\d{5}",
            "SN" => "\d{5}",
            "SK" => "\d{3}[ ]?\d{2}",
            "SI" => "\d{4}",
            "ZA" => "\d{4}",
            "LK" => "\d{5}",
            "TJ" => "\d{6}",
            "TH" => "\d{5}",
            "TN" => "\d{4}",
            "TR" => "\d{5}",
            "TM" => "\d{6}",
            "UA" => "\d{5}",
            "UY" => "\d{5}",
            "UZ" => "\d{6}",
            "VA" => "00120",
            "VE" => "\d{4}",
            "ZM" => "\d{5}",
            "AS" => "96799",
            "CC" => "6799",
            "CK" => "\d{4}",
            "RS" => "\d{6}",
            "ME" => "8\d{4}",
            "CS" => "\d{5}",
            "YU" => "\d{5}",
            "CX" => "6798",
            "ET" => "\d{4}",
            "FK" => "FIQQ 1ZZ",
            "NF" => "2899",
            "FM" => "(9694[1-4])([ \-]\d{4})?",
            "GF" => "9[78]3\d{2}",
            "GN" => "\d{3}",
            "GP" => "9[78][01]\d{2}",
            "GS" => "SIQQ 1ZZ",
            "GU" => "969[123]\d([ \-]\d{4})?",
            "GW" => "\d{4}",
            "HM" => "\d{4}",
            "IQ" => "\d{5}",
            "KG" => "\d{6}",
            "LR" => "\d{4}",
            "LS" => "\d{3}",
            "MG" => "\d{3}",
            "MH" => "969[67]\d([ \-]\d{4})?",
            "MN" => "\d{6}",
            "MP" => "9695[012]([ \-]\d{4})?",
            "MQ" => "9[78]2\d{2}",
            "NC" => "988\d{2}",
            "NE" => "\d{4}",
            "VI" => "008(([0-4]\d)|(5[01]))([ \-]\d{4})?",
            "PF" => "987\d{2}",
            "PG" => "\d{3}",
            "PM" => "9[78]5\d{2}",
            "PN" => "PCRN 1ZZ",
            "PW" => "96940",
            "RE" => "9[78]4\d{2}",
            "SH" => "(ASCN|STHL) 1ZZ",
            "SJ" => "\d{4}",
            "SO" => "\d{5}",
            "SZ" => "[HLMS]\d{3}",
            "TC" => "TKCA 1ZZ",
            "WF" => "986\d{2}",
            "XK" => "\d{5}",
            "YT" => "976\d{2}",
        ], $iso);

    }
}