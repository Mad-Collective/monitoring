<?php

namespace Cmp\Monitoring\Interfaces;

/**
 * Defines a taggable entity
 *
 * Interface IsTaggableInterface
 * @package Cmp\Monitoring
 */
interface IsTaggableInterface
{
    /**
     * Returns the entity tags
     *
     * @return array
     */
    public function getTags();

    /**
     * Sets the entity tags
     *
     * @param array $tags
     *
     * @return $this
     */
    public function setTags(array $tags = array());
}
