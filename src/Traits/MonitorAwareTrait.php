<?php

namespace Cmp\Monitoring\Traits;

use Cmp\Monitoring\Monitor;

/**
 * Trait for classes that accept a monitor
 *
 * Class MonitorAwareTrait
 * @package Cmp\Monitoring\Traits
 */
trait MonitorAwareTrait
{
    /**
     * @var Monitor
     */
    protected $monitor;

    /**
     * Sets the monitor
     *
     * @param Monitor $monitor
     */
    public function setMonitor(Monitor $monitor)
    {
        $this->monitor = $monitor;
    }
}