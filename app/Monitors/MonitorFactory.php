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
            'ssl' => SSLCertificateMonitor::class,
            'disk' => DiskUsageMonitor::class,
            'ping' => HttpPingMonitor::class,
        ];

        if($filter != ['*']) {
            $monitors = array_intersect_key($monitors, array_flip($filter));
        }

        return collect($monitors)->map(function($monitor) {
            return [
                'isDangerouse' => app($monitor)->isDangerouse(),
                'getResult' => app($monitor)->getResult(),
            ];
        });
    }
}