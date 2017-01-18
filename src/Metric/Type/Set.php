<?php

namespace Cmp\Monitoring\Metric\Type;

use Cmp\Monitoring\Metric\AbstractMetric;

/**
 * Represents a set metric
 * 
 * Class Set
 * @package Cmp\Monitoring\Metric\Type
 */
class Set extends AbstractMetric 
{
    const TYPE = 'set';

    /**
     * AbstractMetric type
     *
     * @var string
     */
    protected $type = self::TYPE;
}
