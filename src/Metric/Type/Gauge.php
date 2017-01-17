<?php

namespace Cmp\Monitoring\Metric\Type;

use Cmp\Monitoring\Metric\AbstractMetric;

/**
 * Represents a gauge metric
 * 
 * Class Gauge
 * @package Cmp\Monitoring\Metric\Type
 */
class Gauge extends AbstractMetric 
{
    const TYPE = 'gauge';

    /**
     * AbstractMetric type
     *
     * @var string
     */
    protected $type = self::TYPE;
}