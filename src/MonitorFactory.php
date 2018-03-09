<?php

namespace Cmp\Monitoring;

use Cmp\Monitoring\Event\EventFactory;
use Cmp\Monitoring\Event\Sender\DataDog as DataDogEvents;
use Cmp\Monitoring\Event\Sender\PsrLogger as PsrLoggerEvents;
use Cmp\Monitoring\Metric\MetricFactory;
use Cmp\Monitoring\Metric\Sender\DataDog as DataDogMetrics;
use Cmp\Monitoring\Metric\Sender\PsrLogger as PsrLoggerMetrics;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class MonitorFactory
{
    /**
     * Creates a monitor in a single command by passing a configuration
     *
     * See the method 'mergeDefaults' for info on the config format
     *
     * @param array $config
     *
     * @return Monitor
     */
    public static function create(array $config = [])
    {
        $config        = self::mergeDefaults($config);
        $metricFactory = new MetricFactory($config['default_tags']);
        $eventFactory  = new EventFactory($config['hostname']);
        $logger        = $config['logger']['instance'] instanceof LoggerInterface ? $config['logger']['instance'] : null;
        $debug         = $config['logger']['debug'];
        $monitor       = new Monitor($metricFactory, $eventFactory, $debug ? $logger : null);

        if (!empty($config['datadog']['metrics'])) {
            $monitor->pushMetricSender(DataDogMetrics::create($config['datadog']['host'], $config['datadog']['port']));
        }

        if (!empty($config['datadog']['events'])) {
            $monitor->pushEventSender(DataDogEvents::create($config['datadog']['host'], $config['datadog']['port']));
        }

        if ($logger && $config['logger']['metrics']) {
            $monitor->pushMetricSender(new PsrLoggerMetrics($logger), $config['logger']['level']);
        }

        if ($logger && $config['logger']['events']) {
            $monitor->pushEventSender(new PsrLoggerEvents($logger), $config['logger']['level']);
        }

        return $monitor;
    }

    /**
     * @param array $config
     *
     * @return array
     */
    private static function mergeDefaults(array $config)
    {
        return array_replace_recursive([
            'hostname'     => gethostname(),    # Hostname of the server
            'default_tags' => [],               # A key-value array with default tags for metrics and events
            'logger'       => [
                'instance' => null,             # A Psr\LoggerInterface instance
                'debug'    => false,            # If true, it will log debug messages from the monitor  
                'level'    => LogLevel::INFO,   # The level for debug message
                'metrics'  => false,            # If true, metrics will be sent trough the provided logger instance
                'events'   => false,            # If true, events will be sent trough the provided logger instance
            ],
            'datadog'      => [
                'metrics'  => false,            # If true, metrics will be sent trough the datadog agent
                'events'   => false,            # If true, events will be sent trough the datadog agent
                'host'     => null,             # The datadog agent host, if null the default will be used
                'port'     => null,             # The datadog agent port, if null the default will be used
            ],
        ], $config);
    }
}
