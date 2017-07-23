<?php

namespace App\Payments\Gateways;

use App\Payments\BaseGateway;

class CodGateway extends BaseGateway
{  
    /**
     * Get gateway metadata
     *
     * @return array
     */
    public function metadata() {

        return [
            'id' => 'cod',
            'name' => 'Cash on delivery',
            'icon' => 'transfer_within_a_station',
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

    /**
     * Check if provider is active
     *
     * @return bool
     */
    public function isActive() {

        return false;
        
    }
}