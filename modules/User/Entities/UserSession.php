<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Agent\Agent;
use Modules\Address\Services\LocationService;
use URL;

class UserSession extends Model
{

    public $table = 'user_sessions';
    
    protected $fillable  = [
        'user_id', 'device', 'platform', 'browser', 'is_phone', 'ip', 'country_code',
    ];

    public static function log(){

        if(auth()->guest()) return false;

    	$agent = new Agent();
    	$ip = request()->ip();
    	$location = new LocationService();

        ///$ref = request()->header('referer', '') ? :  URL::previous();
        ///$ref_domain = str_ireplace('www.', '', parse_url($ref, PHP_URL_HOST));

    	self::firstOrCreate([
    		'user_id' => auth()->id(),
    		'device' => $agent->device(),
    		'platform' => $agent->platform(),
    		'browser' => $agent->browser(),
    		'is_phone' => $agent->isPhone(),
            'ip' => request()->ip(),
    		'country_code' => auth()->user()->country
    	])->touch();
    }

    public function user()
    {   
        return $this->belongsTo(config('auth.providers.users.model'));
    }
}