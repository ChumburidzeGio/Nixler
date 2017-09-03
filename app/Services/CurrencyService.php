<?php

namespace App\Services;

class CurrencyService {

    /**
     * @return string
     */
    public function get($currency, $amount)
    {	
        $price = '';

        $amount = $amount ? number_format(filter_var($amount, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION), 2) : '';

        switch ($currency) {
            case 'USD':
                return "\${$amount}";
            case 'GEL':
                return "{$amount} ლარი";
            case 'PLN':
                return "{$amount} zł";
            default:
                return "$amount";
        }
    }

}