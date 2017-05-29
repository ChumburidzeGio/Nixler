<?php

namespace App\Monitors;

abstract class BaseMonitor
{
    public abstract function getResult();

    public abstract function isDangerouse();

    public abstract function __construct();

    /**
     * @param int $sizeInBytes
     *
     * @return string
     */
    public function getHumanReadableSize($sizeInBytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        if ($sizeInBytes === 0) {
            return '0 '.$units[1];
        }
        for ($i = 0; $sizeInBytes > 1024; ++$i) {
            $sizeInBytes /= 1024;
        }

        return round($sizeInBytes, 2).' '.$units[$i];
    }
}