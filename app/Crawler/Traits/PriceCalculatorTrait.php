<?php

namespace App\Crawler\Traits;

trait PriceCalculatorTrait {

	private $exchangeRates = [
		'USD2GEL' => 2.40,
		'EUR2GEL' => 2.80,
	];

	private $shippingCosts = [
		'IT2GE' => 4,
		'US2GE' => 2,
	];

	private $countryCurrencies = [
		'GE' => 'GEL',
		'IT' => 'EUR',
		'US' => 'USD',
	];

	private $generalFee = 0.10;

	/**
	 * @param $sCode string {2} Seller Country Code
	 * @param $lCode string {2} Local Country Code
	 * @param $pPrice float Product price
	 */
	public function calculatePrice($sCode, $lCode, $pPrice)
	{
		$sellerCurrency = array_get($this->countryCurrencies, $sCode);

		$localCurrency = array_get($this->countryCurrencies, $lCode);

		$shippingCost = array_get($this->shippingCosts, "{$sCode}2{$lCode}");

		$exchangeRate = array_get($this->exchangeRates, "{$sellerCurrency}2{$localCurrency}");

		$subTotal = ($pPrice + $shippingCost) * $exchangeRate;

    	$total = $this->roundUpTo5Or10($subTotal + $subTotal * $this->generalFee) - 0.01;

        $formated = str_replace(',', '', money(null, $total));

        return $formated;
	}

	/**
	 * @param $number integer
	 */
	public function roundUpTo5Or10($number, $x=5)
	{
		return (ceil($number) % $x === 0) ? ceil($number) : round(($number+$x/2)/$x)*$x;
	}
}
