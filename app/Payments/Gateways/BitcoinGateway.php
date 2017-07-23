<?php

namespace App\Payments\Gateways;

use App\Payments\BaseGateway;

class BitcoinGateway extends BaseGateway
{  
    /**
     * Get gateway metadata
     *
     * @return array
     */
    public function metadata() {

        return [
            'id' => 'bit',
            'name' => 'Bitcoin',
            'icon' => 'format_bold',
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