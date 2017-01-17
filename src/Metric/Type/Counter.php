<?php

namespace Cmp\Monitoring\Metric\Type;

use Cmp\Monitoring\Metric\AbstractMetric;

/**
 * Represents a counter metric
 * 
 * Class Counter
 * @package Cmp\Monitoring\Metric\Type
 */
class Counter extends AbstractMetric
{
    const TYPE = 'counter';

    /**
     * AbstractMetric type
     *
     * @var string
     */
    protected $type = self::TYPE;
}