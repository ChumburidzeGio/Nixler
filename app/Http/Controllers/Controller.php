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

    /**
     * Return page view
     *
     * @return \Illuminate\Http\Response
     */
    public function view($name, $params)
    {
        return view("pages.{$name}.template", $params);
    }

}
