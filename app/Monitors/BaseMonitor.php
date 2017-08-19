<?php

namespace App\Monitors;

abstract class BaseMonitor
{
    public abstract function __construct();
    
    public abstract function getResult();

    public abstract function getTitle();

    public abstract function hasErrors();
}