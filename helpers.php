<?php

use App\Services\CurrencyService;

if (! function_exists('money')) {

    function money($currency, $amount = null) {
        return (new CurrencyService)->get($currency, $amount);
    }
}