<?php

namespace App\Traits;

use Laravel\Scout\Searchable as ScoutSearchable;
use DB;

trait Searchable {
    
    use ScoutSearchable;

    /**
     * Get all activities for model
     *
     * @return void
     */
    public function scopeWhereKeyword($query, $match, $keyword)
    {        
        return $query->selectRaw("MATCH($match) AGAINST (? IN NATURAL LANGUAGE MODE) as relevance", [$keyword])
                     ->whereRaw("MATCH($match) AGAINST (? IN NATURAL LANGUAGE MODE)", [$keyword]);

        /*
    	if(!$query) {
    		return $builder;
    	}

        $query = str_slug($query) !== $query ? $query . " " . str_slug($query) : $query;

		$ids = $this->search($query)->keys()->toArray();

		$ids_ordered = implode(',', $ids);

    	return $ids ? $builder->whereIn('id', $ids)->orderBy(DB::raw("FIELD(id, $ids_ordered)")) : $builder->whereNull('id');*/
    }
    
}