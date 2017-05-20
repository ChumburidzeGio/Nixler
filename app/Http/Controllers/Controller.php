<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Artesaos\SEOTools\Traits\SEOTools as SEOToolsTrait;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, SEOToolsTrait;

    public function __construct(){
        $this->seo()->metatags()->setTitleDefault("Nixler");
        $this->seo()->opengraph()->setTitle(trans('app.title'));
        $this->seo()->setDescription(trans('app.description'));
        $this->seo()->addImages([url('/img/meta.jpg')]);
    }
}
