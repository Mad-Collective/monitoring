<?php

namespace Cmp\Monitoring\Traits;

/**
 * This trait allows the classes using it to have a timestamp
 * 
 * Trait HasTimestampTrait
 * @package Cmp\Monitoring\Traits
 */
trait HasTimestampTrait
{
    /**
     * Timestamp
     *
     * @var int
     */
    protected $timestamp;

    /**
     * Gets the timestamp
     *
     * @return mixed
     */
    public function getTimeStamp()
    {
        return $this->timestamp;
    }

    /**
     * Sets the timestamp. If null given the current timestamp is used
     *
     * @param mixed $timestamp
     *
     * @return $this
     */
    public function setTimeStamp($timestamp = null)
    {
        $this->timestamp = $timestamp ?: time();

        return $this;
    }
}
