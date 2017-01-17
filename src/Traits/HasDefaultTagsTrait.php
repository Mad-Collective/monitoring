<?php

namespace Cmp\Monitoring\Traits;

use Cmp\Monitoring\Interfaces\IsTaggableInterface;

/**
 * This trait allows the classes using it to have default tags as an array
 * 
 * Trait HasDefaultTagsTrait
 * @package Cmp\Monitoring\Traits
 */
trait HasDefaultTagsTrait
{
    /**
     * Default tags to set on all metric
     *
     * @var array
     */
    protected $defaultTags =array();

    /**
     * Adds default tags to the factory
     *
     * @param array $tags
     *
     * @return $this
     */
    public function addDefaultTags(array $tags)
    {
        $this->defaultTags = array_merge($this->defaultTags, $tags);

        return $this;
    }

    /**
     * Returns the factory default tags
     *
     * @return array
     */
    public function getDefaultTags()
    {
        return $this->defaultTags;
    }

    /**
     * Adds the default tags to a taggable entity
     *
     * @param IsTaggableInterface $taggable
     *
     * @return IsTaggableInterface
     */
    public function addDefaultTagsToEntity(IsTaggableInterface $taggable)
    {
        return $taggable->setTags(array_merge($this->defaultTags, $taggable->getTags()));
    }
}