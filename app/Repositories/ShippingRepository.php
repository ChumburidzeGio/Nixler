<?php

namespace App\Repositories;

use App\Repositories\BaseRepository;
use App\Entities\ShippingPrice;
use App\Entities\Country;

class ShippingRepository extends BaseRepository {
    

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return ShippingPrice::class;
    }


    /**
     * Get all information about shipping prices for merchant
     *
     * @return \Illuminate\Http\Response
     */
    public function all($user = null)
    {
        $user = $user ? : auth()->user();

        $country_code = $user->country;

        $country = Country::where('iso_code', $country_code)->with('cities', 'cities.translations')->first();

        $prices = ShippingPrice::where('user_id', auth()->id())->where('type', 'city')->with('city', 'city.translations')->get();

        if($user->getMeta('delivery_full')){

            $country_price = ShippingPrice::where([
                'user_id' => auth()->id(),
                'location_id' => $country->id,
                'type' => 'country'
            ])->first();

            if(!$country_price){
              
              $country_price = $this->settingsUpdate([
                'delivery_full' => 1,
                'has_return' => 1,
                'policy' => '',
              ]);

            }

        } else {
            $country_price = [];
        }

        return compact('prices', 'country', 'country_price');
    }


    /**
     * Create new phone model
     *
     * @return \Illuminate\Http\Response
     */
    public function settingsUpdate($data, $user = null)
    {
        $user = $user ? : auth()->user();

        $country = Country::where('iso_code', $user->country)->first();

        $user->setMeta('delivery_full', array_get($data, 'delivery_full'));
        $user->setMeta('has_return', array_get($data, 'has_return'));
        $user->setMeta('return_policy', array_get($data, 'policy'));

        if(!array_get($data, 'delivery_full')){

            $country_price = $this->model->where([
                'user_id' => $user->id,
                'location_id' => $country->id,
                'type' => 'country'
            ])->delete();

        } else {

            $country_price = $this->model->firstOrCreate([
                'user_id' => $user->id,
                'location_id' => $country->id,
                'type' => 'country'
            ], [
                'price' => 0,
                'currency' => $country->currency,
                'window_from' => 1,
                'window_to' => 3
            ]);

        }

        return $country_price;
    }

}