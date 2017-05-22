<?php

namespace App\Services;

use AlgoliaSearch\Client;

class AlgoliaService
{
    protected $client;

    /**
     * Construct Algolia service
     */
    public function __construct() 
    {
        $this->clint = new Client(
          config('services.algolia.app_id'),
          config('services.algolia.app_admin_key')
        );

    }

    /**
     * @param $content array
     * @param $objectId integer
     */
    public function addObject($index, $content, $objectId) 
    {
        $index = $this->clint->initIndex(config('app.env')."_{$index}");
        
        return $index->addObject($content, $objectId);
    }

    /**
     * @param $content array
     * @param $objectId integer
     */
    public function deleteObject($index, $objectId) 
    {
        $index = $this->clint->initIndex(config('app.env')."_{$index}");
        
        return $index->deleteObject($objectId);
    }

    /**
     * @param $content array
     * @param $objectId integer
     */
    public function search($index, $query, $settings) 
    {
        $generalSettings = [];

        $searchSettings = [];

        foreach ($settings as $key => $value) {
            if(in_array($key, ['facets', 'numericFilters', 'filters'])){
                $searchSettings[$key] = $value;
            } else {
                $generalSettings[$key] = $value;
            }
        }

        $index = $this->clint->initIndex(config('app.env')."_{$index}");
        
        $index->setSettings($generalSettings);

        return $index->search($query, $searchSettings);
    }

}