<?php

namespace App\Payments;

use Inacho\CreditCard;

abstract class BaseGateway
{
    public abstract function getName();

    public abstract function setFundingInstrumentData($data);

    public abstract function purchase($invoice_id, $currency, $amount);

    public abstract function isSuccessful();

    public abstract function hasRedirect();

    public abstract function setRedirectCallback($callback);

    public abstract function __construct();
}