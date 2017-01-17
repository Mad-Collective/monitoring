<?php

namespace Cmp\Monitoring\Metric\Type;

use Cmp\Monitoring\Metric\AbstractMetric;

/**
 * Represents a histogram metric
 * 
 * Class Histogram
 * @package Cmp\Monitoring\Metric\Type
 */
class Histogram extends AbstractMetric 
{
    const TYPE = 'histogram';

    /**
     * AbstractMetric type
     *
     * @var string
     */
    protected $type = self::TYPE;
}