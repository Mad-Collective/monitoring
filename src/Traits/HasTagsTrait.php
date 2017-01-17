<?php

namespace Cmp\Monitoring\Traits;

/**
 * This trait allows the classes using it to have tags as an array
 * 
 * Trait HasTagsTrait
 * @package Cmp\Monitoring\Traits
 */
trait HasTagsTrait 
{
    /**
     * Instance tags
     *
     * @var array
     */
    protected $tags =array();

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
}