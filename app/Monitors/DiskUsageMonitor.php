<?php

namespace App\Monitors;

use Carbon\Carbon;
use Exception;

class DiskUsageMonitor extends BaseMonitor
{
    /**  @var int */
    protected $totalSpace;

    /**  @var int */
    protected $usedSpace;

    /**  @var float */
    protected $percentageUsed;

    /** @var int */
    protected $alarmPercentage = 75;

    /**
     * @param array $config
     */
    public function __construct()
    {
        $path = __DIR__;

        $this->totalSpace = disk_total_space($path);

        $freeSpace = disk_free_space($path);

        $this->usedSpace = $this->totalSpace - $freeSpace;

        $this->percentageUsed = sprintf('%.2f',($this->usedSpace / $this->totalSpace) * 100);
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return 'Disk Usage';
    }

    /**
     * @return boolean
     */
    public function hasErrors()
    {
        return ($this->percentageUsed >= $this->alarmPercentage);
    }

    /**
     * @return string
     */
    public function getResult()
    {
        return $this->percentageUsed.'% used';
    }

}