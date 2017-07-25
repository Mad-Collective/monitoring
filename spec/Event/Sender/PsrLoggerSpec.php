<?php

namespace spec\Cmp\Monitoring\Event\Sender;

use Cmp\Monitoring\Event\Event;
use Cmp\Monitoring\Event\Sender\PsrLogger;
use PhpSpec\ObjectBehavior;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class PsrLoggerSpec extends ObjectBehavior
{
    function let(LoggerInterface $logger)
    {
        $this->beConstructedWith($logger);
    }

    function it_can_be_initialized()
    {
        $this->shouldHaveType(PsrLogger::class);
    }

    function it_can_log_an_event(LoggerInterface $logger, Event $event)
    {
        $eventTags = ['foo' => 'bar', 'bool' => true, 'number' => 3];
        $title = 'title';
        $text = 'text';
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
            '{"title":"%s","text":"%s","timestamp":%d,"host":"%s","type":"%s"}',
            $title,
            $text,
            $timestamp,
            $host,
            $type
        );

        $eventTags['bool'] = 'true';

        $logger->log(LogLevel::NOTICE, $message, $eventTags)->shouldHaveBeenCalled();
    }
}
