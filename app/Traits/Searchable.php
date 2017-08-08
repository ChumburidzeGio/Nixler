<?php

namespace App\Traits;

use Laravel\Scout\Searchable as ScoutSearchable;
use TeamTNT\TNTSearch\TNTSearch;
use DB;

trait Searchable {
    
    use ScoutSearchable;

    /**
     * Get all activities for model
     *
     * @return void
     */
    public function scopeWhereKeyword($builder, $query)
    {
        return $this->search($query);
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