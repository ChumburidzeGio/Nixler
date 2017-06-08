<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use MetaTag;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct(){
    	
    	if(request()->isMethod('get')){
    		$this->meta('title', __('Buy and Sell Online Clothes, Shoes, Electronics & more'));
	    	$this->meta('description', __('Sign up and find the best offers from shops in your area or become a seller and get the new channel of sales for free.'));
	        $this->meta('image', url('/img/meta.jpg'));
	        $this->meta('type', 'website');
	    }

    }

    /**
     * Show the product page
     *
     * @return \Illuminate\Http\Response
     */
    public function meta($key, $value)
    {
        return MetaTag::set($key, $value);
    }

}
