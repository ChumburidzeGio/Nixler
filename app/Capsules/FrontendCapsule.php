<?php

namespace App\Capsules;

use App\Entities\ProductCategory;
use App\Entities\Product;
use Illuminate\Support\Facades\Auth;
use App\Services\RecommService;
use MetaTag;

class FrontendCapsule {
    
    protected $jsKey = 'app.frontend.js_vars';

    protected $metaKey = 'app.frontend.meta_vars';

    /**
     * Construct the capsule
     *
     * @return void
     */
    public function __construct()
    {
        if(!is_array(config($this->jsKey))) 
        {
            config([$this->jsKey => [
                'csrfToken' => csrf_token(),
                'currency' => config('app.currency'),
                'currencySymbol' => trim(money(config('app.currency'))),
            ]]);
        }

        if(!is_array(config($this->metaKey))) 
        {
        	config([$this->metaKey => []]);
        }
    }

    public function addJs($js) 
    {
        $vars = config($this->jsKey);

        config([
            $this->jsKey => array_merge($vars, $js)
        ]);

        return $this;
    }

    public function addMeta($tags) 
    {
        foreach ($tags as $key => $value) 
        {
            MetaTag::set($key, $value);
        }

        return $this;
    }

    public function toJson() 
    {
        return json_encode(config($this->jsKey));
    }

}