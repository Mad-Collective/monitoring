<?php

namespace Cmp\Monitoring\Interfaces;

use Cmp\Monitoring\Monitor;

/**
 * Identifies an object that can accept a monitor
 * 
 * Interface MonitorAwareInterface
 * @package Cmp\Monitoring\Interfaces
 */
interface MonitorAwareInterface 
{
    public function setMonitor(Monitor $monitor);
}
