<?php

namespace Cmp\Monitoring;

use Monolog\Logger;
use Cmp\Monitoring\Metric\AbstractMetric;
use Cmp\Monitoring\Metric\MetricFactory;
use Cmp\Monitoring\Metric\SenderInterface as MetricSender;
use Cmp\Monitoring\Event\Event;
use Cmp\Monitoring\Event\EventFactory;
use Cmp\Monitoring\Event\SenderInterface as EventSender;

/**
 * Used for sending monitoring metrics and events to multiple back ends
 * 
 * Class Monitor
 * @package Cmp\Monitoring
 */
class Monitor
{
    /**
     * Factory to create metrics
     * 
     * @var MetricFactory
     */
    protected $metricFactory;

    /**
     * Factory to create events
     * 
     * @var EventFactory
     */
    protected $eventFactory;

    /**
     * Logger for errors
     * 
     * @var Logger
     */
    protected $logger;

    /**
     * Metric senders
     * 
     * @var MetricSender[]
     */
    protected $metricSenders = array();
    /**
     * Event senders
     *
     * @var EventSender[]
     */
    protected $eventSenders = array();
    /**
     * Registry of timers
     * 
     * @var \Cmp\Monitoring\Metric\Type\Timer[]
     */
    protected $timers;

    /**
     * On debug mode exceptions will be thrown
     *
     * @param MetricFactory $metricFactory Factory for creating metrics
     * @param EventFactory  $eventFactory  Factory for creating events
     * @param Logger        $logger        For logging errors
     */
    public function __construct(MetricFactory $metricFactory, EventFactory $eventFactory, Logger $logger)
    {
        $this->metricFactory = $metricFactory;
        $this->eventFactory = $eventFactory;
        $this->logger = $logger;
    }

    /**
     * Returns the metric factory
     * 
     * @return MetricFactory
     */
    public function getMetricFactory()
    {
        return $this->metricFactory;
    }

    /**
     * Returns the event factory
     * 
     * @return EventFactory
     */
    public function getEventFactory()
    {
        return $this->eventFactory;
    }

    /**
     * Sends a counter metric
     * 
     * @param string $metric     Metric Name
     * @param int    $count      Count value
     * @param array  $tags       Metric Tags
     * @param int    $sampleRate Sample rate for the metric
     * 
     * @return $this
     */
    public function counter($metric, $count = 1, array $tags = array(), $sampleRate = AbstractMetric::DEFAULT_SAMPLE_RATE)
    {
        try {
            $this->sendMetric($this->metricFactory->counter($metric, $count, $tags, $sampleRate));
        } catch (\Exception $exception) {
            $this->logException($exception);
        }

        return $this;
    }

    /**
     * Increments a metric in 1 point
     * 
     * @param string $metric     Metric name
     * @param array  $tags       Metric Tags
     * @param int    $sampleRate Sample rate for the metric
     * 
     * @return $this
     */
    public function increment($metric, array $tags = array(), $sampleRate = AbstractMetric::DEFAULT_SAMPLE_RATE)
    {
        try {
            $this->sendMetric($this->metricFactory->counter($metric, 1, $tags, $sampleRate));
        } catch (\Exception $exception) {
            $this->logException($exception);
        }

        return $this;
    }

    /**
     * Decrements a metric in 1 point
     * 
     * @param string $metric     Metric name
     * @param array  $tags       Metric tags
     * @param int    $sampleRate Sample rate for the metric
     * 
     * @return $this
     */
    public function decrement($metric, array $tags = array(), $sampleRate = AbstractMetric::DEFAULT_SAMPLE_RATE)
    {
        try {
            $this->sendMetric($this->metricFactory->counter($metric, -1, $tags, $sampleRate));
        } catch (\Exception $exception) {
            $this->logException($exception);
        }

        return $this;
    }

    /**
     * Sends a gauge metric
     * 
     * @param string  $metric     Metric name
     * @param int     $level      Gauge level
     * @param array   $tags       Metric tags
     * @param int     $sampleRate Sample rate for the metric
     * 
     * @return $this
     */
    public function gauge($metric, $level, array $tags = array(), $sampleRate = AbstractMetric::DEFAULT_SAMPLE_RATE)
    {
        try {
            $this->sendMetric($this->metricFactory->gauge($metric, $level, $tags, $sampleRate));
        } catch (\Exception $exception) {
            $this->logException($exception);
        }

        return $this;
    }

    /**
     * Sends a set metric
     * 
     * @param string $metric      Metric name
     * @param mixed  $uniqueValue Metric unique value
     * @param array  $tags        Metric tags
     * @param int    $sampleRate  Sample rate for the metric
     * 
     * @return $this
     */
    public function set($metric, $uniqueValue, array $tags = array(), $sampleRate = AbstractMetric::DEFAULT_SAMPLE_RATE)
    {
        try {
            $this->sendMetric($this->metricFactory->set($metric, $uniqueValue, $tags, $sampleRate));
        } catch (\Exception $exception) {
            $this->logException($exception);
        }

        return $this;
    }

    /**
     * Sends an histogram
     * 
     * @param string $metric     Metric name
     * @param int    $duration   Duration of the metric in milliseconds
     * @param array  $tags       Metric tags
     * @param int    $sampleRate Sample rate for the metric
     * 
     * @return $this
     */
    public function histogram($metric, $duration, array $tags = array(), $sampleRate = AbstractMetric::DEFAULT_SAMPLE_RATE)
    {
        try {
            $this->sendMetric($this->metricFactory->histogram($metric, $duration, $tags, $sampleRate));
        } catch (\Exception $exception) {
            $this->logException($exception);
        }

        return $this;
    }

    /**
     * Starts a timer metric
     * 
     * @param string $metric     Metric name
     * @param array  $tags       Metric tags
     * @param int    $sampleRate Sample rate for metric
     * 
     * @return $this
     */
    public function start($metric, array $tags = array(), $sampleRate = AbstractMetric::DEFAULT_SAMPLE_RATE)
    {
        try {
            $this->timers[$metric] = $this->metricFactory->timer($metric, null, $tags, $sampleRate);
            $this->timers[$metric]->start();
        } catch (\Exception $exception) {
            $this->logException($exception);
        }

        return $this;
    }

    /**
     * Ends a previously started timer
     * 
     * @param string $metric Metric name
     * @param array  $tags   Extra tags to set for the metric
     * 
     * @return $this
     */
    public function end($metric, array $tags = array())
    {
        try {
            if (!isset($this->timers[$metric])) {
                throw new \RuntimeException("Timer for metric $metric was not started");
            }

            if (!empty($tags)) {
                $this->timers[$metric]->setTags(array_merge($this->timers[$metric]->getTags(), $tags));
            }

            $this->sendMetric($this->timers[$metric]->end());
            unset ($this->timers[$metric]);

        } catch (\Exception $exception) {
            $this->logException($exception);
        }

        return $this;
    }

    /**
     * Sends an event
     * 
     * @param string $title     Event title
     * @param string $text      Event text description
     * @param string $type      Event type
     * @param array  $tags      Event tags
     * @param null   $timestamp Event timestamp, null for now
     * 
     * @return $this
     */
    public function event($title, $text, $type = Event::INFO, array $tags = array(), $timestamp = null)
    {
        try {
            $this->sendEvent($this->eventFactory->event($title, $text, $type, $tags, $timestamp));
        } catch (\Exception $exception) {
            $this->logException($exception);
        }

        return $this;
    }

    /**
     * Pushes a new metric sender to the monitor
     * 
     * @param MetricSender $sender
     * 
     * @return $this
     */
    public function pushMetricSender(MetricSender $sender)
    {
        $this->metricSenders[] = $sender;

        return $this;
    }

    /**
     * Pushes a new event sender to the monitor
     * 
     * @param EventSender $sender
     * 
     * @return $this
     */
    public function pushEventSender(EventSender $sender)
    {
        $this->eventSenders[] = $sender;

        return $this;
    }

    /**
     * Sends a metric through all metric senders adding the default tags
     * 
     * @param AbstractMetric $metric
     * 
     * @return $this
     */
    public function sendMetric(AbstractMetric $metric)
    {
        try {
            $this->metricFactory->addDefaultTagsToEntity($metric);
            $this->sendMetricThroughSenders($metric);
        } catch (\Exception $exception) {
            $this->logException($exception);
        }

        return $this;
    }

    /**
     * Sends an event through all event senders adding the default tags
     * 
     * @param Event $event
     *
     * @return $this
     */
    public function sendEvent(Event $event)
    {
        try {
            $this->eventFactory->addDefaultTagsToEntity($event);
            $this->sendEventThroughSenders($event);
        } catch (\Exception $exception) {
            $this->logException($exception);
        }
    }

    /**
     * Sends the metric through all senders
     *
     * @param AbstractMetric $metric
     */
    protected function sendMetricThroughSenders(AbstractMetric $metric)
    {
        foreach ($this->metricSenders as $sender) {
            try {
                $sender->send($metric);
            } catch (\Exception $exception) {
                $this->logException($exception);
            }
        }
    }

    /**
     * Sends the event through all senders
     *
     * @param Event $event
     *
     * @return $this
     */
    protected function sendEventThroughSenders(Event $event)
    {
        foreach ($this->eventSenders as $sender) {
            try {
                $sender->send($event);
            } catch (\Exception $exception) {
                $this->logException($exception);
            }
        }

        return $this;
    }

    /**
     * Logs the exception found
     *
     * @param \Exception $exception
     */
    protected function logException(\Exception $exception)
    {
        try {
            $this->logger->error($exception->getMessage() , array('exception' => $exception));
        } catch (\Exception $exception) {
            // Do nothing
        }
    }
}