<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;

class ProductStats extends Model
{
	
    public $table = 'product_stats';

    protected $fillable = [
        'action', 'actor', 'object', 'gender', 'is_mobile', 'age_range', 'country', 'city'
    ];

    public $ageRanges  = [
        0 => [0,13],
        1 => [14,17],
        2 => [18,20],
        3 => [21,24],
        4 => [25,29],
        5 => [30,34],
        6 => [35,44],
        7 => [45,54],
        8 => [55,63],
        9 => [64,0]
    ];


    public function calculate($data){

        $ageranger = $this->ageRanges;

        $devices = $data->groupBy('is_mobile')->mapWithKeys(function($item, $key){
            return [($key == 1 ? 'mobile' : 'desktop') => count($item)];
        });

        $gender = $data->groupBy('gender')->mapWithKeys(function($item, $key){
            $key = (!$key ? 'np' : $key);
            return ["$key" => count($item)];
        });

        $age_range = $data->groupBy('age_range')->sortBy(function ($product, $key) {
            return $key;
        })->mapWithKeys(function($item, $key) use ($ageranger) {
            $name = (!$key ? 'np' :  implode('-', array_get($ageranger, $key)));
            return ["$name" => count($item)];
        });

        $country = $data->groupBy('country')->map(function($item){
            return count($item);
        })->sortByDesc(function ($item, $key) {
            return $item;
        });

        $city = $data->groupBy('city')->map(function($item){
            return count($item);
        })->sortByDesc(function ($item, $key) {
            return $item;
        });

        $activityByDate = $data->groupBy('action')->map(function($item){
            return $item->groupBy(function($item){
                return $item->created_at->year;
            })->map(function($item){
                return $item->groupBy(function($item){
                    return $item->created_at->month;
                })->map(function($item){
                    return count($item);
                });
            });
        });

        return compact('devices', 'gender', 'age_range', 'country', 'city', 'activityByDate');

    }
}