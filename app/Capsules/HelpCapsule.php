<?php

namespace App\Capsules;

use App\Entities\ProductCategory;
use App\Entities\Product;

class HelpCapsule {

    private $qa;

    /**
     * Construct the capsule
     *
     * @return void
     */
    public function __construct()
    {
        $this->qa = json_decode(file_get_contents(resource_path('docs/qa.ka.json')), 1);

    	return $this;
    }

    /**
     * Return capsule as array
     *
     * @return array
     */
    public function get()
    {
        return $this->qa;
    }

    /**
     * Return capsule as array
     *
     * @return array
     */
    public function toJson()
    {
    	return json_encode($this->get());
    }
    
}