<?php

namespace spec\Cmp\Monitoring;

use Cmp\Monitoring\Event\Event;
use Cmp\Monitoring\Event\EventFactory;
use Cmp\Monitoring\Event\SenderInterface as EventSender;
use Cmp\Monitoring\Metric\AbstractMetric;
use Cmp\Monitoring\Metric\MetricFactory;
use Cmp\Monitoring\Metric\SenderInterface as MetricSender;
use Cmp\Monitoring\Metric\Type\Timer;
use Monolog\Logger;
use PhpSpec\ObjectBehavior;

class MonitorSpec extends ObjectBehavior
{
    function let(MetricFactory $metricFactory, EventFactory $eventFactory, Logger $logger)
    {
        $this->beConstructedWith($metricFactory, $eventFactory, $logger);
    }

    function it_can_be_initialized()
    {
        $this->shouldHaveType('Cmp\Monitoring\Monitor');
    }

    function it_can_push_metric_senders(MetricSender $sender)
    {
        $this->pushMetricSender($sender)->shouldReturn($this);
    }

    function it_can_push_event_senders(EventSender $sender)
    {
        $this->pushEventSender($sender)->shouldReturn($this);
    }

    function it_can_send_metric_via_senders(AbstractMetric $metric, MetricSender $senderOne, MetricSender $senderTwo, MetricFactory $metricFactory)
    {
        $metricFactory->addDefaultTagsToEntity($metric)->shouldBeCalled();
        $senderOne->send($metric)->shouldBeCalled();
        $senderTwo->send($metric)->shouldBeCalled();

        $this->pushMetricSender($senderOne);
        $this->pushMetricSender($senderTwo);
        $this->sendMetric($metric);
    }

    function it_can_send_counter_metrics_directly(MetricSender $sender, MetricFactory $metricFactory, AbstractMetric $metric)
    {
        $metricFactory->counter('metric', 100,array('tags'), .5)->willReturn($metric);

        $metricFactory->addDefaultTagsToEntity($metric)->shouldBeCalled();
        $sender->send($metric)->shouldBeCalled();

        $this->pushMetricSender($sender);
        $this->counter('metric', 100,array('tags'), .5);
    }

    function it_can_increment_counter_metrics_directly(MetricSender $sender, MetricFactory $metricFactory, AbstractMetric $metric)
    {
        $metricFactory->counter('metric', +1,array('tags'), .5)->willReturn($metric);

        $metricFactory->addDefaultTagsToEntity($metric)->shouldBeCalled();
        $sender->send($metric)->shouldBeCalled();

        $this->pushMetricSender($sender);
        $this->increment('metric',array('tags'), .5);
    }

    function it_can_decrement_counter_metrics_directly(MetricSender $sender, MetricFactory $metricFactory, AbstractMetric $metric)
    {
        $metricFactory->counter('metric', -1,array('tags'), .5)->willReturn($metric);

        $metricFactory->addDefaultTagsToEntity($metric)->shouldBeCalled();
        $sender->send($metric)->shouldBeCalled();

        $this->pushMetricSender($sender);
        $this->decrement('metric',array('tags'), .5);
    }

    function it_can_send_gauge_metrics_directly(MetricSender $sender, MetricFactory $metricFactory, AbstractMetric $metric)
    {
        $metricFactory->gauge('metric', 100,array('tags'), .5)->willReturn($metric);

        $metricFactory->addDefaultTagsToEntity($metric)->shouldBeCalled();
        $sender->send($metric)->shouldBeCalled();

        $this->pushMetricSender($sender);
        $this->gauge('metric', 100,array('tags'), .5);
    }

    function it_can_send_set_metrics_directly(MetricSender $sender, MetricFactory $metricFactory, AbstractMetric $metric)
    {
        $metricFactory->set('metric', 100,array('tags'), .5)->willReturn($metric);

        $metricFactory->addDefaultTagsToEntity($metric)->shouldBeCalled();
        $sender->send($metric)->shouldBeCalled();

        $this->pushMetricSender($sender);
        $this->set('metric', 100,array('tags'), .5);
    }

    function it_can_send_histogram_metrics_directly(MetricSender $sender, MetricFactory $metricFactory, AbstractMetric $metric)
    {
        $metricFactory->histogram('metric', 100,array('tags'), .5)->willReturn($metric);

        $metricFactory->addDefaultTagsToEntity($metric)->shouldBeCalled();
        $sender->send($metric)->shouldBeCalled();

        $this->pushMetricSender($sender);
        $this->histogram('metric', 100,array('tags'), .5);
    }

    function it_can_start_and_end_a_timer(MetricFactory $metricFactory, Timer $timer)
    {
        $metricFactory->timer('metric', null,array('tags'), .5)->willReturn($timer);
        $timer->end()->willReturn($timer);

        $metricFactory->addDefaultTagsToEntity($timer)->shouldBeCalled();
        $timer->start()->shouldBeCalled();

        $this->start('metric',array('tags'), .5);
        $this->end('metric');
    }

    function it_can_send_an_event(EventFactory $eventFactory, EventSender $sender, Event $event)
    {
        $eventFactory->event('event', 'text', 'type',array('tags'), 10000)->willReturn($event);

        $eventFactory->addDefaultTagsToEntity($event)->shouldBeCalled();
        $sender->send($event)->shouldBeCalled();

        $this->pushEventSender($sender);
        $this->event('event', 'text', 'type',array('tags'), 10000);
    }

    function it_can_log_errors_when_an_exception_is_found(EventFactory $eventFactory, EventSender $sender, Event $event, Logger $logger)
    {
        $exception = new \Exception( 'message' );
        $sender->send($event)->willThrow( $exception );

        $logger->error('message', array('exception'=>$exception))->shouldBeCalled();
        $eventFactory->addDefaultTagsToEntity($event)->shouldBeCalled();

        $this->pushEventSender($sender);
        $this->sendEvent($event);
    }

    function it_can_add_tags_while_ending_a_timer(MetricFactory $metricFactory, Timer $timer)
    {
        $timer->end()->willReturn($timer);
        $timer->getTags()->willReturn(array('tag1' => 'one'));
        $metricFactory->timer('metric', null,array('tag1' => 'one'), .5)->willReturn($timer);

        $timer->start()->shouldBeCalled();
        $timer->setTags(array('tag1' => 'modified', 'tag2' => 'new_tag'))->shouldBeCalled();
        $metricFactory->addDefaultTagsToEntity($timer)->shouldBeCalled();

        $this->start('metric',array('tag1' => 'one'), .5);
        $this->end('metric',array('tag1' => 'modified', 'tag2' => 'new_tag'));
    }

    function it_can_return_the_metric_factory(MetricFactory $metricFactory)
    {
        $this->getMetricFactory()->shouldReturn($metricFactory);
    }

    function it_can_return_the_event_factory(EventFactory $eventFactory)
    {
        $this->getEventFactory()->shouldReturn($eventFactory);
    }
}
