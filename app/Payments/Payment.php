<?php

namespace App\Payments;

use App\Payments\Gateways\CodGateway;
use App\Payments\Gateways\BankGateway;
use App\Payments\Gateways\BitcoinGateway;
use App\Payments\Gateways\CardGateway;

class Payment
{  
    /**
     * Get the list of available payment providers
     *
     * @return array
     */
    public function providers() {
        
        $providers = [];

        $available = [
            'cod' => CodGateway::class,
            'bnk' => BankGateway::class,
            'bit' => BitcoinGateway::class,
            'crd' => CardGateway::class,
        ];

        foreach ($available as $key => $value) {
                
            $provider = app($value);

            $providers[$key] = $provider->toArray();

        }

        return $providers;

    }

}