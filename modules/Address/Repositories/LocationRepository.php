<?php

namespace Modules\Address\Repositories;

use App\Repositories\BaseRepository;
use Modules\Address\Entities\Country;
use Modules\Address\Entities\City;

class LocationRepository extends BaseRepository {
    

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return City::class;
    }
    
    
    /**
     * @param $name string
     * @return Modules\Address\Entities\City
     */
    function findCityByName($name)
    {
        return City::whereTranslation('name', $name)->first();
    }

}