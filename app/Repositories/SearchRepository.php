<?php

namespace App\Repositories;

use App\Repositories\BaseRepository;
use App\Entities\User;
use App\Notifications\SomeoneFollowedYou;
use App\Repositories\LocationRepository;
use App\Entities\Profile;
use App\Services\Facebook;
use Carbon\Carbon;

class SearchRepository extends BaseRepository {

    protected $user = null;

    protected $facets = [];

    protected $facetFilters = [];

    protected $numericFilters = [];

    protected $aroundLatLng = null;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->setUserLocation();

        $this->setFacets(['price', 'category_id', 'variants', 'tags']);

        return $this;
    }


    /**
     * Find user by username
     */
    public function setUserLocation()
    {

    }


    /**
     * @param $facets array
     */
    public function setFacets($facets)
    {
        $this->facets = $facets;
    }


    /**
     * @param $value string
     * @param ? $type string (numeric|facet)
     */
    public function setFilter($value, $type = 'facet')
    {
        if($type == 'numeric') {
            $this->numericFilters[] = $value;
        } else {
            $this->facetFilters[] = $value;
        }

        return $this;
    }


    /**
     * @param $column string
     * @param $value string
     */
    public function where($column, $value)
    {
        $this->setFilter("{$column}:{$value}");

        return $this;
    }


    /**
     * @param $column string
     * @param $values array
     */
    public function whereIn($column, $values)
    {
        foreach ($values as $value) {
            $this->setFilter("{$column}:{$min} TO {$max}", 'numeric');
        }

        return $this;
    }


    /**
     * @param $column string
     * @param $min integer
     * @param $max integer
     */
    public function whereBetween($column, $min, $max)
    {
        $this->setFilter("{$column}:{$min} TO {$max}", 'numeric');

        return $this;
    }


    /**
     * @param $query string
     */
    public function parseQuery($query)
    {
        $query_params = explode(' ', $query);

        $clean_query = [];

        $price = null;

        foreach ($query_params as $word) {
            if(starts_with($word, 'price')){
                $price = $this->parsePriceFromString($word);
            } else {
                $clean_query[] = $word;
            }
        }

        return implode(' ', $clean_query);
    }


    /**
     * @param $string string
     */
    public function parsePriceFromString($string)
    {
        $price = str_replace('price:', '', $price);

        $min = $max = filter_var($price, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

        if(starts_with($price, '<')) {
            $min = null;
        } 

        elseif(starts_with($price, '>')) {
            $min = null;
        } 

        elseif (strpos($price, '...') !== false) {
            $range = explode('...', $price);
            $min = array_first($range);
            $max = array_last($range);
        }

        if($min > $max){
            $temp_min = $min;
            $min = $max;
            $max = $temp_min;
        }

        return compact('min', 'max');
    }


}