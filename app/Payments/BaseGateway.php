<?php

namespace App\Payments;

use App\Entities\User;
use App\Entities\Order;

class BaseGateway
{  
    protected $actor;

    protected $order;

    protected $payed;

    protected $redirectLink;

    /**
     * Construct gateway
     *
     * @return void
     */
    public function __construct() {

        $this->payed = false;

    }

    /**
     * Get gateway metadata
     *
     * @return array
     */
    public function metadata() {
        return [];
    }

    /**
     * Set actor model
     *
     * @return this
     */
    public function withActor(User $user) {

        $this->actor = $user;

        return $this;

    }

    /**
     * Set order model
     *
     * @return this
     */
    public function withOrder(Order $order) {

        $this->order = $order;

        return $this;

    }

    /**
     * Pay with gateway
     *
     * @return this
     */
    public function pay() {

        return $this;

    }

    /**
     * Redirect gateway
     *
     * @return this
     */
    public function redirect() {

        return false;

    }

    /**
     * Proccess redirect callback
     *
     * @return this
     */
    public function proccess($data = []) {

        return $this;

    }

    /**
     * Check if transaction already made
     *
     * @return string
     */
    public function isPayed() {

    	return $this->payed;

    }

    /**
     * Check if gateway should be redirected
     *
     * @return string
     */
    public function isRedirect() {

    	return $this->redirectLink;
        
    }

    /**
     * Check if provider is active
     *
     * @return bool
     */
    public function isActive() {

        return false;
        
    }

    /**
     * Mark order as proccessing
     *
     * @return string
     */
    public function markOrderAsProccessing() {

    	$this->order->update([
    		'payment_status' => 'proccessing',
    	]);
        
    }

    /**
     * Mark order as proccessing
     *
     * @return string
     */
    public function markOrderAsPayed() {

        $this->order->update([
            'payment_status' => 'payed',
        ]);

        $this->payed = true;
        
    }

    /**
     * Return gateway as array
     *
     * @return array
     */
    public function toArray() {
        
        return array_merge($this->metadata(), [
            'isActive' => $this->isActive()
        ]);

    }
}