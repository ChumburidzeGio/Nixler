<?php

namespace App\Services;

use Facebook\Facebook as FacebookGraph;

class Facebook
{
    protected $fb;

    /**
     * Contruct Facebook service
     */
    public function __construct($token = null) 
    {
        $this->fb = new FacebookGraph([
          'app_id' => config('services.facebook.client_id'),
          'app_secret' => config('services.facebook.client_secret'),
          'default_access_token' => $token,
        ]);
    }

    /**
     * Make the get request to Facebook API
     */
    public function get($path, $token = null)
    {
        //try {
          return json_decode($this->fb->get($path)->getBody(), 1);
        //} catch(\Exception $e) {
        //    return null;
        //}
    }

    /**
     * Make the get request to Facebook API
     */
    public function getLocation($id, $field)
    {
        $data = $this->get("/{$id}?fields=location");

        return array_get($data, $field);
    }

}