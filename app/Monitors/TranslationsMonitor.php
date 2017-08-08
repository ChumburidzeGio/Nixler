<?php

namespace App\Monitors;

use Carbon\Carbon;
use Exception;

class TranslationsMonitor extends BaseMonitor
{
    /**
     * @param array $config
     */
    public function __construct()
    {
        
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return 'Untraslated parts';
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