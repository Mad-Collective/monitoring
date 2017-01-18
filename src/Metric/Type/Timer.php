<?php

namespace Cmp\Monitoring\Metric\Type;

use Cmp\Monitoring\Metric\AbstractMetric;

/**
 * Represents a timer metric
 * 
 * Class Timer
 * @package Cmp\Monitoring\Metric\Type
 */
class Timer extends AbstractMetric 
{
    const TYPE = 'timer';
    const MILLISECONDS_IN_A_SECOND = 1000;

    /**
     * AbstractMetric type
     *
     * @var string
     */
    protected $type = self::TYPE;

    /**
     * Start time for the timer
     *
     * @var float
     */
    private $startTime;

    /**
     * Starts the timer
     * 
     * @return $this
     */
    public function start()
    {
        $this->startTime = microtime(true);

        return $this;
    }

    /**
     * Ends the timer
     * 
     * @return $this
     */
    public function end()
    {
        $endTime = microtime(true);
        $this->value = (int) round(($endTime - $this->startTime) * self::MILLISECONDS_IN_A_SECOND);

        return $this;
    }
}
