<?php

namespace Cmp\Monitoring\Event\Sender;

use Cmp\Monitoring\Integrations\DataDogClient;
use Cmp\Monitoring\Event\Event;
use Cmp\Monitoring\Event\SenderInterface;

class DataDog extends DataDogClient implements SenderInterface
{
    /**
     * Send an event to a backend
     *
     * @param Event $event
     */
    public function send(Event $event)
    {
        $message = $this->getEventMessage($event);
        $this->flush($message);
    }

    /**
     * Builds the event udp message
     *
     * @param Event $event
     *
     * @return string
     */
    protected function getEventMessage(Event $event)
    {
        $sanitizedText = $this->getSanitizedText($event);

        $message = sprintf(
            "_e{%d,%d}:%s|%s|d:%d|h:%s|t:%s",
            strlen($event->getTitle()),
            strlen($sanitizedText),
            $event->getTitle(),
            $sanitizedText,
            $event->getTimeStamp(),
            $event->getHost(),
            $event->getType()
        );

        return $message . $this->getFormattedTags($event);
    }

    /**
     * Gets the tags formatted ready to use on the udp message
     *
     * @param Event $event
     *
     * @return null|string
     */
    protected function getFormattedTags(Event $event)
    {
        $tags = array_map(function ($key, $value) {
            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }
            return "$key:$value";
        }, $event->getTags());

        return empty($tags) ? '' : '|#'.implode(',', $tags);
    }

    /**
     * Sanitizes the text in order to prepare it to be sent as part of an UDP datagram
     *
     * @param Event $event
     *
     * @return string
     */
    protected function getSanitizedText(Event $event)
    {
        return str_replace(['\n', "\n"], '\\\n', $event->getText());
    }
}
