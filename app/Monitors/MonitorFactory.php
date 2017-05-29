<?php

namespace App\Monitors;

class MonitorFactory
{
    /**
     * @param array $monitorConfiguration
     * @param array $filter
     * @return mixed
     */
    public static function get(array $filter = ['*'])
    {
        $monitors = [
            'disk' => DiskUsageMonitor::class,
            'ping' => HttpPingMonitor::class,
            'ssl' => SSLCertificateMonitor::class,
        ];

        if($filter != ['*']) {
            $monitors = array_intersect_key($monitors, array_flip($filter));
        }

        $fields = collect($monitors)->mapWithKeys(function($monitor) {

            $monitor = app($monitor);

            return [$monitor->getTitle() => (
                $monitor->hasErrors() ? "`{$monitor->getResult()}`" : $monitor->getResult()
            )];
        });

        $hasErrors = !!collect($monitors)->filter(function($monitor){
            return app($monitor)->hasErrors();
        })->count();

        return compact('fields', 'hasErrors');
    }
}