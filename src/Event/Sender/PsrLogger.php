<?php

namespace Cmp\Monitoring\Event\Sender;

use Cmp\Monitoring\Event\Event;
use Cmp\Monitoring\Event\SenderInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class PsrLogger implements SenderInterface
{
    /**
     * PSR-3 logger
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param LoggerInterface $logger Logger to user
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Send an event to a backend
     *
     * @param Event $event
     */
    public function send(Event $event)
    {
        $level = $this->getEventLevel($event);
        $message = $this->getEventMessage($event);
        $tags = $this->getEventTags($event);

        $this->logger->log($level, $message, $tags);
    }

    /**
     * @param Event $event
     *
     * @return string
     */
    private function getEventMessage(Event $event)
    {
        return json_encode([
            'title' => $event->getTitle(),
            'text' => $event->getText(),
            'timestamp' => $event->getTimeStamp(),
            'host' => $event->getHost(),
            'type' => $event->getType()
        ]);
    }

    /**
     * @param Event $event
     *
     * @return string
     */
    private function getEventLevel(Event $event)
    {
        $type = $event->getType();

        switch ($type) {
            case Event::INFO:    return LogLevel::INFO;
            case Event::SUCCESS: return LogLevel::NOTICE;
            case Event::WARNING: return LogLevel::WARNING;
            case Event::ERROR:   return LogLevel::ERROR;
        }

        throw new \InvalidArgumentException("$type is not a valid event type");
    }

    /**
     * @param Event $event
     *
     * @return array
     */
    private function getEventTags(Event $event)
    {
        return array_map(function ($value) {
            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }
            return $value;
        }, $event->getTags());
    }
}
