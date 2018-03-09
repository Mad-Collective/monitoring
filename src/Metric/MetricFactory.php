<?php

namespace Cmp\Monitoring\Metric;

use Cmp\Monitoring\Metric\Type\Set;
use Cmp\Monitoring\Metric\Type\Gauge;
use Cmp\Monitoring\Metric\Type\Counter;
use Cmp\Monitoring\Metric\Type\Histogram;
use Cmp\Monitoring\Metric\Type\Timer;
use Cmp\Monitoring\Traits\AbstractHasDefaultTags;

/**
 * Factory to build metric
 * 
 * Class MetricFactory
 * @package Cmp\Monitoring\Metric
 */
class MetricFactory extends AbstractHasDefaultTags
{
    const COUNTER   = 'counter';
    const GAUGE     = 'gauge';
    const HISTOGRAM = 'histogram';
    const SET       = 'set';
    const TIMER     = 'timer';

    /**
     * @var string
     */
    private $prefix;

    /**
     * @param array  $defaultTags
     * @param string $prefix
     */
    public function __construct(array $defaultTags = array(), $prefix = "")
    {
        $this->defaultTags = $defaultTags;
        $this->prefix      = $prefix;
    }

    /**
     * Creates a new counter
     * 
     * @param string $metric     Metric name
     * @param int    $count      Count value
     * @param array  $tags       Metric tags
     * @param int    $sampleRate Metric sample rate 
     * 
     * @return Counter
     */
    public function counter($metric, $count = 1, array $tags = array(), $sampleRate = AbstractMetric::DEFAULT_SAMPLE_RATE)
    {
        return new Counter($this->prefix($metric), $count, array_merge($this->defaultTags, $tags), $sampleRate);
    }

    /**
     * Creates a new gauge
     * 
     * @param string $metric     Metric name
     * @param int    $level      Gauge level value
     * @param array  $tags       Metric tags
     * @param int    $sampleRate Metric sample rate
     * 
     * @return Gauge
     */
    public function gauge($metric, $level, array $tags = array(), $sampleRate = AbstractMetric::DEFAULT_SAMPLE_RATE)
    {
        return new Gauge($this->prefix($metric), $level, array_merge($this->defaultTags, $tags), $sampleRate);
    }

    /**
     * Creates a new histogram
     * 
     * @param string $metric     Metric name
     * @param int    $duration   Histogram duration in milliseconds
     * @param array  $tags       Metric tags
     * @param int    $sampleRate Metric sample rate
     * 
     * @return Histogram
     */
    public function histogram($metric, $duration = null, array $tags = array(), $sampleRate = AbstractMetric::DEFAULT_SAMPLE_RATE)
    {
        return new Histogram($this->prefix($metric), $duration, array_merge($this->defaultTags, $tags), $sampleRate);
    }

    /**
     * Creates a new timer
     * 
     * @param string $metric     Metric name
     * @param int    $duration   Timer duration in milliseconds
     * @param array  $tags       Metric tags
     * @param int    $sampleRate Metric sample rate
     * 
     * @return Timer
     */
    public function timer($metric, $duration = null, array $tags = array(), $sampleRate = AbstractMetric::DEFAULT_SAMPLE_RATE)
    {
        return new Timer($this->prefix($metric), $duration, array_merge($this->defaultTags, $tags), $sampleRate);
    }

    /**
     * Creates a new set
     * 
     * @param string $metric      Metric name
     * @param mixed  $uniqueValue Unique value for the set
     * @param array  $tags        Metric tags
     * @param int    $sampleRate  Metric sample rate
     * 
     * @return Set
     */
    public function set($metric, $uniqueValue, array $tags =array(), $sampleRate = AbstractMetric::DEFAULT_SAMPLE_RATE)
    {
        return new Set($this->prefix($metric), $uniqueValue, array_merge($this->defaultTags, $tags), $sampleRate);
    }

    /**
     * @param string $metric
     *
     * @return string
     */
    private function prefix($metric)
    {
        return $this->prefix ? $this->prefix.$metric : $metric;
    }
}
