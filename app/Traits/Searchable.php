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
    	if(!$query) {
    		return $builder;
    	}

        $query = str_slug($query) !== $query ? $query . " " . str_slug($query) : $query;

    	$tnt = new TNTSearch();

        $driver = config('database.default');

        $config = config('scout.tntsearch') + config("database.connections.$driver");

        $tnt->loadConfig($config);

        $tnt->fuzziness = true;

		$tnt->selectIndex("{$this->getTable()}.index");

		$res = $tnt->search($query, 1000);

		$ids = $res['ids'];

		$ids_ordered = implode(',', $ids);

    	return $ids ? $builder->whereIn('id', $ids)->orderBy(DB::raw("FIELD(id, $ids_ordered)")) : $builder->whereNull('id');
    }
    
}