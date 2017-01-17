<?php

namespace Cmp\Monitoring\Event;

use Cmp\Monitoring\Traits\AbstractHasDefaultTags;

/**
 * Factory to build events
 * 
 * Class EventFactory
 * @package Cmp\Monitoring\Event
 */
class EventFactory extends AbstractHasDefaultTags
{
    /**
     * Host where the events are being created
     * 
     * @var string
     */
    protected $host;

    /**
     * @param string $host        Host where the events are being created
     * @param array  $defaultTags Default tags that should be merged on the event
     */
    public function __construct($host, array $defaultTags = array())
    {
        $this->host = $host;
        $this->defaultTags = $defaultTags;
    }

    /**
     * Method to build a single event
     *
     * @param string $title     Title of the event
     * @param string $text      Extra information of the event
     * @param string $type      Type of the event
     * @param array  $tags      Array of tags for the event
     * @param int    $timestamp Timestamp when the event happened
     * 
     * @return Event
     */
    public function event($title, $text = null, $type = Event::INFO, array $tags =array(), $timestamp = null)
    {
        return new Event($title, $text, $this->host, $type, array_merge($this->defaultTags, $tags), $timestamp);
    }
}