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
    	MetaTag::set('title', trans('app.title'));
    	MetaTag::set('description', trans('app.description'));
        MetaTag::set('image', url('/img/meta.jpg'));
        MetaTag::set('type', 'website');
    }
}
