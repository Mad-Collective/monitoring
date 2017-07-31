<?php

namespace spec\Cmp\Monitoring\Event\Sender;

use Cmp\Monitoring\Event\Event;
use Cmp\Monitoring\Event\Sender\DataDog;
use Cmp\Monitoring\Integrations\DataDogClient;
use Cmp\Monitoring\Integrations\Socket;
use PhpSpec\ObjectBehavior;

class DataDogSpec extends ObjectBehavior
{
    function let(Socket $socket)
    {
        $this->beConstructedWith($socket);
    }

    function it_can_be_initialized()
    {
        $this->shouldHaveType(DataDog::class);
    }

    function it_can_log_an_event(Socket $socket, Event $event)
    {
        $eventTags = ['foo' => 'bar', 'bool' => true, 'number' => 3];
        $title = 'title';
        $text = "line 1\nline 2";
        $timestamp = 1457690400;
        $host = '127.0.0.1';
        $type = Event::SUCCESS;

        $event->getTitle()->willReturn($title);
        $event->getText()->willReturn($text);
        $event->getTimeStamp()->willReturn($timestamp);
        $event->getHost()->willReturn($host);
        $event->getType()->willReturn($type);
        $event->getTags()->willReturn($eventTags);

        $this->send($event);

        $message = sprintf(
            "_e{%d,%d}:%s|%s|d:%d|h:%s|t:%s|#%s:%s,%s:%s,%s:%d",
            5,
            15,
            $title,
            'line 1\\\nline 2',
            $timestamp,
            $host,
            $type,
            'foo',
            'bar',
            'bool',
            'true',
            'number',
            3
        );

        $socket->create(AF_INET, SOCK_DGRAM, SOL_UDP)->shouldHaveBeenCalled();
        $socket->setNonBlocking()->shouldHaveBeenCalled();
        $socket->sendMessage(
            $message,
            DataDogClient::DATADOG_DEFAULT_SERVER,
            DataDogClient::DATADOG_DEFAULT_PORT
        )->shouldHaveBeenCalled();
        $socket->close()->shouldHaveBeenCalled();
    }
}
