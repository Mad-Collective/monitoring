<?php

namespace spec\Cmp\Monitoring\Metric\Sender;

use Cmp\Monitoring\Metric\AbstractMetric;
use PhpSpec\ObjectBehavior;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class PsrLoggerSpec extends ObjectBehavior
{
    function let(LoggerInterface $logger)
    {
        $level = LogLevel::DEBUG;
        $this->beConstructedWith($logger, $level);
    }

    function it_can_be_initialized()
    {
        $this->shouldHaveType('Cmp\Monitoring\Metric\Sender\PsrLogger');
    }

    function it_can_log_a_counter_metric(LoggerInterface $logger, AbstractMetric $metric)
    {
        $metricTags = array('foo' => 'bar', 'bool' => true, 'number' => 3);
        $metric->getType()->willReturn("counter");
        $metric->getName()->willReturn("foo.counter");
        $metric->getValue()->willReturn(5.5);
        $metric->getSampleRate()->willReturn(1.0);
        $metric->getTags()->willReturn($metricTags);

        $this->send($metric);

        $message = "Metric: counter | foo.counter | 5.5 | 1";
        $tags    = array_merge(array(
            'type'        => 'counter',
            'name'        => 'foo.counter',
            'value'       => 5.5,
            'sample_rate' => 1.0
        ), $metricTags);

        $logger->log(LogLevel::DEBUG, $message, $tags)->shouldHaveBeenCalled();
    }
}
