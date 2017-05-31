<?php

namespace App\Payments;

use Inacho\CreditCard;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\CreditCard;
use PayPal\Api\Payer;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Api\CreditCardToken;
use PayPal\Api\FundingInstrument;
use PayPal\Api\Amount;
use PayPal\Api\Payment as PPayment;

class PayPalGateway extends BaseGateway
{
    public abstract function __construct();

    public function getName() {
    	return 'PayPal';
    };

    public function purchase($invoice_id, $currency, $amount) {

    };

    public function isSuccessful() {
    	return false;
    };

    public function hasRedirect() {
    	return true;
    };

    public function setRedirectCallback($callback) {

    };
}