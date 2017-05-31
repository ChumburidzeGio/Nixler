<?php

namespace App\Payments;

use Inacho\CreditCard;

class Helpers
{
    public function isValidCardNumber($number) {
    	return CreditCard::validCreditCard($number)['valid'];
    };

    public function isValidCardDate($date) {

    	try {

            $value = explode('/', $date);

            return CreditCard::validDate(strlen($value[1]) == 2 ? (2000+$value[1]) : $value[1], $value[0]);

        } catch(\Exception $e) {

            return false;

        }

    };

    public function isValidCardCvv2($cvv2) {
    	return ctype_digit($cvv2) && (strlen($cvv2) == 3 || strlen($cvv2) == 4);eturn false;
    };

}