<?php

namespace spec\Cmp\Monitoring;

use Cmp\Monitoring\Monitor;
use Cmp\Monitoring\MonitorFactory;
use PhpSpec\ObjectBehavior;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class MonitorFactorySpec extends ObjectBehavior
{
    function it_can_be_initialized()
    {
        $this->shouldHaveType(MonitorFactory::class);
    }

    function it_can_built_an_empty_monitor()
    {
        $monitor = $this->create();
        $monitor->shouldBeAnInstanceOf(Monitor::class);
    }

    function it_can_built_an_working_monitor(LoggerInterface $logger)
    {
        $monitor = $this->create([
            'hostname'     => 'fooserver',
            'default_tags' => ['foo' => 'bar'],
            'logger'       => [
                'instance' => $logger,
                'debug'    => true,  
                'level'    => LogLevel::WARNING,
                'metrics'  => true,
                'events'   => true,
            ],
            'datadog'      => [
                'metrics'  => true,
                'events'   => true,
                'host'     => '10.0.0.1',
                'port'     => 8822,
            ],
        ]);

        $monitor->shouldBeAnInstanceOf(Monitor::class);
    }

    function it_can_built_an_working_monitor_from_half_config(LoggerInterface $logger)
    {
        $monitor = $this->create([
            'hostname'     => 'fooserver',
            'default_tags' => ['foo' => 'bar'],
            'logger'       => [
                'instance' => $logger,
                'debug'    => true,
                'metrics'  => true,
                'events'   => true,
            ],
            'datadog'      => [
                'metrics'  => true,
                'events'   => true,
                'host'     => '10.0.0.1'
            ],
        ]);

        $monitor->shouldBeAnInstanceOf(Monitor::class);
    }
}
