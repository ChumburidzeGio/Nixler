<?php

namespace App\Payments\Gateways;

use App\Payments\BaseGateway;

class CardGateway extends BaseGateway
{  
    /**
     * Get gateway metadata
     *
     * @return array
     */
    public function metadata() {

        return [
            'id' => 'crd',
            'name' => 'Visa/Mastercard/AMEX',
            'icon' => 'credit_card',
            'instruction' => null,
        ];

    }

    /**
     * Pay with gateway
     *
     * @return this
     */
    public function pay() {

        $this->markOrderAsProccessing();

        return $this;

    }

    /**
     * Proccess redirect callback
     *
     * @return this
     */
    public function proccess($data = []) {

        $this->markOrderAsPayed();

        return $this;

    }
}