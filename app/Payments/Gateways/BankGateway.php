<?php

namespace App\Payments\Gateways;

use App\Payments\BaseGateway;

class BankGateway extends BaseGateway
{  
    /**
     * Get gateway metadata
     *
     * @return array
     */
    public function metadata() {

        return [
            'id' => 'bnk',
            'name' => 'Bank transfer',
            'icon' => 'account_balance',
            'instruction' => "
                შპს \"ტექ\"\n
                საინდენტიფიკაციო კოდი: 404954484\n
                ბანკი: TBC\n
                ბანკის კოდი: TBCBGE22\n
                IBAN ანგარიში: GE20TB7725436020100004\n
                ტელეფონი: +995 591 81 50 10\n
                დირექტორი: გიორგი ჭუმბურიძე",
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