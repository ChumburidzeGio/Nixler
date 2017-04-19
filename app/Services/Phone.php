<?php

namespace App\Services;

use Modules\Address\Entities\Country;
use libphonenumber\PhoneNumberUtil;
use stdClass;

class Phone
{

    public static function parse($number, $country)
    {
       if(starts_with($number, ['+', '0'])){
            $rPhone = $number;
        } else {
            $mCountry = Country::where('iso_code', strtoupper($country))->firstOrFail();
            $country = $mCountry->iso_code;
            $rPhone = $mCountry->calling_code . $number;
        }

        $phoneUtil = PhoneNumberUtil::getInstance();

        $phone = $phoneUtil->parse($rPhone, $country);

       	$data = new stdClass();

       	$data->is_valid = $phoneUtil->isValidNumber($phone);

        $data->country_code = $phone->getCountryCode();

        $data->number = $phone->getNationalNumber();

        return $data;
    }

}