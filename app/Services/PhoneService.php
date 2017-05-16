<?php

namespace App\Services;

use App\Entities\Country;
use libphonenumber\PhoneNumberUtil;
use stdClass;

class PhoneService
{

    public static function parse($number, $country = null)
    {
        $phoneUtil = PhoneNumberUtil::getInstance();

        if(starts_with($number, 0)){
            $number = ltrim($number, "+");
        }

        $phone = $phoneUtil->parse($number, $country);
        
       	$data = new stdClass();

       	$data->is_valid = $phoneUtil->isValidNumber($phone);

        if(!$data->is_valid && !starts_with($number, '+')) {
            $phone = $phoneUtil->parse('+'.$number, null);
            $data->is_valid = $phoneUtil->isValidNumber($phone);
        }

        $data->country_code = $phone->getCountryCode();

        $data->national_number = $phone->getNationalNumber();

        $data->number = $phone->getCountryCode().$phone->getNationalNumber();

        return $data;
    }

}