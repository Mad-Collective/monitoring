<?php

namespace Cmp\Monitoring\Metric;

use Cmp\Monitoring\Interfaces\IsTaggableInterface;
use Cmp\Monitoring\Traits\AbstractHasTagsAndTimestamp;

/**
 * Describe the basic metric characteristics
 * 
 * Class AbstractMetric
 * @package Cmp\Monitoring\Metric
 */
abstract class AbstractMetric extends AbstractHasTagsAndTimestamp implements IsTaggableInterface
{
     const DEFAULT_SAMPLE_RATE = 1;

    /**
     * The metric's name
     * 
     * @var string
     */
    protected $name;

    /**
     * @var mixed;
     */
    protected $value;

    /**
     * Metric type
     * 
     * @var string
     */
    protected $type;

    /**
     * Metric sample rate
     * 
     * @var int
     */
    protected $sampleRate = self::DEFAULT_SAMPLE_RATE;

    /**
     * Returns the metric type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Constructor, sets the metric's name
     *
     * @param string $name
     * @param null   $value
     * @param array  $tags
     * @param int    $sampleRate
     */
    public function __construct($name, $value = null, $tags = array(), $sampleRate = self::DEFAULT_SAMPLE_RATE)
    {
        $this->setName($name);
        $this->setValue($value);
        $this->setTags($tags);
        $this->setSampleRate($sampleRate);
    }

    /**
     * Get the metric's name
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the metric name
     * 
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Returns the metric value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Sets the metric value
     * 
     * @param mixed $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Return the metric sample rate
     * 
     * @return int
     */
    public function getSampleRate()
    {
        return $this->sampleRate;
    }

    /**
     * Sets the metric sample rate
     * 
     * @param int $sampleRate
     *
     * @return $this
     */
    public function setSampleRate($sampleRate)
    {
        $this->sampleRate = $sampleRate;

        return $this;
    }
}