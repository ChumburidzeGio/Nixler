<?php 

namespace Modules\Address\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Contracts\CacheableInterface;
use Prettus\Repository\Traits\CacheableRepository;
use Modules\Address\Entities\Country;

class AddressRepository extends BaseRepository implements CacheableInterface {

	use CacheableRepository;

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return "Modules\\Address\\Entities\\UserAddress";
    }


    /**
     * Get all addresses for user
     *
     * @return array
     */
    public function all($columns = array('*'))
    {
    	$user = auth()->user();

    	$country = Country::where('iso_code', $user->country)->with('cities.translations')->first();

        $addresses = $this->model->where('user_id', $user->id)->with('city')->get();

        return compact('addresses', 'country');
    }


    /**
     * Store new resource
     *
     * @return void
     */
    public function create(array $attributes = [])
    {
    	$user = auth()->user();

    	$country = Country::where('iso_code', $user->country)->first();

    	$this->updateOrCreate([
            'user_id' => $user->id,
            'country_id' => $country->id,
            'city_id' => array_get($attributes, 'city_id'),
            'post_code' => array_get($attributes, 'post_code'),
            'street' => array_get($attributes, 'street'),
            'phone' => array_get($attributes, 'phone'),
        ], [
            'name' => array_get($attributes, 'name'),
            'note' => array_get($attributes, 'note'),
        ]);
    }


    /**
     * Prepare resource for editing
     *
     * @return object
     */
    public function edit($id)
    {
    	$user = auth()->user();

    	$address = $this->model->where('id', $id)->where('user_id', auth()->id())->firstOrFail();

    	return compact('address');
    }


    /**
     * Update resource
     *
     * @return object
     */
    public function update(array $attributes = [], $id)
    {
    	$user = auth()->user();
        
        $resource = $this->model->where('id', $id)->where('user_id', $user->id)->firstOrFail();

        $resource->update([
            'post_code' => array_get($attributes, 'post_code'),
            'street' => array_get($attributes, 'street'),
            'phone' => array_get($attributes, 'phone'),
            'name' => array_get($attributes, 'name'),
            'note' => array_get($attributes, 'note'),
        ]);

        return $resource;
    }


    /**
     * Delete resource
     *
     * @return boolean
     */
    public function destroy($id)
    {
    	$user = auth()->user();
        
        $resource = $this->model->where('id', $id)->where('user_id', $user->id)->firstOrFail();

        return $resource->delete();
    }



    /**
     * Delete resource
     *
     * @return boolean
     */
    public static function validatePostCode($code, $country_code = null)
    {
    	$user = auth()->user();
        
       	if(is_null($country_code)){
       		$country_code = $user->country;
       	}

       	$country = Country::where('iso_code', $country_code)->first();

        return preg_match('/'.$country->zip_format.'$/', $code);
    }

}