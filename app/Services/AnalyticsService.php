<?php

namespace App\Services;

use Spatie\Analytics\Period;
use Analytics;
use App\Entities\User;
use App\Entities\Metric;
use Carbon\Carbon;
use Lava;

class AnalyticsService
{

    public function getBasicAnalyticsForPopularProducts() 
    {
        $period = Period::days(1);

        $response = Analytics::performQuery($period, 'ga:pageviews', [
            'dimensions' => 'ga:pagePath',
            'filters' => "ga:pagePath=@/@",
        ]);

        return collect($response['rows'] ?? [])->map(function($item) {

            $path = array_get($item, 0);

            preg_match("/\/@(.*)\/(.*)/", $path, $output_array);

            $username = array_get($output_array, 1);

            $slug = array_get($output_array, 2);

            if(!$slug || !$username) {
                return false;
            }

            $views = intval(array_get($item, 1));

            $data = compact('views');

            return [$slug, $username, $data];

        })->filter()->values();
    }


    public function getBasicAnalyticsForPopularMerchants() 
    {
        $popularProducts = $this->getBasicAnalyticsForPopularProducts();

        $usernames = $popularProducts->groupBy('1')->keys();

        return $usernames->map(function($username) {

            $age = $this->getDimensionForUsername('ga:userAgeBracket', $username);

            $gender = $this->getDimensionForUsername('ga:userGender', $username);

            $city = $this->getDimensionForUsername('ga:city', $username)->filter(function($item, $key){
                return $key !== '(not set)';
            })->take(5);

            //$referrer = $this->getDimensionForUsername('ga:fullReferrer', $username);

            //$socialNetwork = $this->getDimensionForUsername('ga:socialNetwork', $username);

            //$keyword = $this->getDimensionForUsername('ga:keyword', $username);

            $data = compact('age', 'gender', 'city');

            return [$username, $data];

        });
    }


    public function getDimensionForUsername($dimension, $username) 
    {
        $period = Period::days(1);

        $response = \Analytics::performQuery(
            $period,
            'ga:pageviews',
            [
                'dimensions' => $dimension,
                'filters' => "ga:pagePath=@/@{$username}",
                'sort' => '-ga:pageviews',
            ]
        );

        return collect($response['rows'] ?? [])->mapWithKeys(function($item){
            return [
                $item[0] => $item[1]
            ];
        });
    }


    public function loadAnalytics($object_type, $object_id, $key) 
    {
        return Metric::where(compact('object_id', 'object_type', 'key'))->get();
    }


    public function avg($object_type, $object_id, $key) 
    {
        return Metric::where(compact('object_id', 'object_type'))->avg('key');
    }


    public function loadAreaChart($object_type, $object_id, $key, $legend, $id) 
    {
        $models = $this->loadAnalytics($object_type, $object_id, $key);

        if($models->count() < 1) {
            return false;
        }

        $data = Lava::DataTable();

        $data->addDateColumn()->addNumberColumn($legend);

        $models->map(function($item) use ($data) {
            $data->addRow([$item->date, $item->value]);
        });

        $chart = Lava::AreaChart($id, $data, [
          'hAxis' => [
            "format" => 'M/d/yy'
          ]
        ]);

        return true;
    }

}