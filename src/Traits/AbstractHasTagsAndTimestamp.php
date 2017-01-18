<?php
namespace Cmp\Monitoring\Traits;

/**
 * This trait allows the classes using it to have default tags as an array
 * 
 * Trait HasDefaultTagsTrait
 * @package Cmp\Monitoring\Traits
 */
abstract class AbstractHasTagsAndTimestamp
{
    /**
     * Instance tags
     *
     * @var array
     */
    protected $tags =array();

    /**
     * Timestamp
     *
     * @var int
     */
    protected $timestamp;


    /**
     * Returns the tags
     *
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Sets the tags
     *
     * @param array $tags
     *
     * @return $this
     */
    public function setTags(array $tags = array())
    {
        $this->tags = $tags;

        return $this;
    }

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
