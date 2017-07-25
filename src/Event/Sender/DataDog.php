<?php

namespace Cmp\Monitoring\Event\Sender;

use Cmp\Monitoring\Event\Event;
use Cmp\Monitoring\Event\SenderInterface;
use Cmp\Monitoring\Metric\Sender\Socket;

class DataDog implements SenderInterface
{
    const DATADOG_DEFAULT_SERVER = '127.0.0.1';
    const DATADOG_DEFAULT_PORT = 8125;

    /**
     * Socket for sending messages
     *
     * @var string
     */
    protected $socket;

    /**
     * DataDog Agent server ip
     *
     * @var string
     */
    protected $server;

    /**
     * DataDog Agent port
     *
     * @var int
     */
    protected $port;

    /**
     * @param Socket $socket Socket for sending messages
     * @param string $server DataDog agent host
     * @param int    $port   Port where DataDog agent is listening to
     */
    public function __construct(Socket $socket, $server = self::DATADOG_DEFAULT_SERVER, $port = self::DATADOG_DEFAULT_PORT)
    {
        $this->socket = $socket;
        $this->server = $server;
        $this->port = $port;
    }

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
     * Send the message over UDP
     *
     * @param string $message
     */
    protected function flush($message)
    {
        $this->socket->create(AF_INET, SOCK_DGRAM, SOL_UDP);
        $this->socket->setNonBlocking();
        $this->socket->sendMessage($message, $this->server, $this->port);
        $this->socket->close();
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
