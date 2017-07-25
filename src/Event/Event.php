<?php

namespace Cmp\Monitoring\Event;

use Cmp\Monitoring\Interfaces\IsTaggableInterface;
use Cmp\Monitoring\Traits\AbstractHasTagsAndTimestamp;

/**
 * Represents a monitoring event. For example: the deploy of a new release.
 * 
 * Class Event
 * @package Cmp\Monitoring\Event
 */
class Event extends AbstractHasTagsAndTimestamp implements IsTaggableInterface
{
    /** Valid event types */
    const INFO    = 'info';
    const SUCCESS = 'success';
    const WARNING = 'warning';
    const ERROR   = 'error';

    /**
     * Event title
     * 
     * @var string
     */
    protected $title;

    /**
     * Text describing the event
     * 
     * @var string
     */
    protected $text;

    /**
     * Host where the event occurred
     * 
     * @var string
     */
    protected $host;

    /**
     * Event type. Must be one of the valid event types.
     * 
     * @var string
     */
    protected $type;

    /**
     * @param string $title
     * @param string $text
     * @param string $host
     * @param string $type
     * @param array  $tags
     * @param int    $timestamp If null given the current timestamp is used
     */
    public function __construct($title, $text = null, $host = null, $type = self::INFO, array $tags = array(), $timestamp = null)
    {
        $this->setTitle($title);
        $this->setText($text);
        $this->setHost($host);
        $this->setType($type);
        $this->setTimeStamp($timestamp);
        $this->setTags($tags);
    }

    /**
     * Returns the event title
     * 
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the event title
     * 
     * @param mixed $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Returns the event text
     * 
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set the event test
     * 
     * @param mixed $text
     *
     * @return $this
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Returns the host where the event occurred
     * 
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Sets the host where the event occurred
     * 
     * @param string $host
     *
     * @return $this
     */
    public function setHost($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * Returns the event type
     * 
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the event type 
     * 
     * @param string $type
     *
     * @return $this
     * @throw \InvalidArgumentException If the type is not valid
     */
    public function setType($type)
    {
        if (!self::isValidType($type)) {
            throw new \InvalidArgumentException("$type is not a valid event type");
        }

        $this->type = $type;

        return $this;
    }

    /**
     * Checks that the given type is valid
     * 
     * @param string $type
     * 
     * @return bool
     */
    public static function isValidType($type)
    {
        return in_array($type, self::validTypes());
    }

    /**
     * Returns the list of valid event types
     * 
     * @return array
     */
    public static function validTypes()
    {
        return array(self::INFO, self::SUCCESS, self::WARNING, self::ERROR);
    }
}
