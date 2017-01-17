<?php

namespace Cmp\Monitoring\Event;

/**
 * Represents an object that can send events to a back end
 * 
 * Interface SenderInterface
 * @package Cmp\Monitoring\Event
 */
interface SenderInterface 
{
    /**
     * Send an event to a backend
     * 
     * @param Event $event
     */
    public function send(Event $event);
}