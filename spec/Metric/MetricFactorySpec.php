<?php

namespace spec\Cmp\Monitoring\Metric;

use Cmp\Monitoring\Metric\AbstractMetric;
use PhpSpec\ObjectBehavior;

class MetricFactorySpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(array('default_tag' => 'default'), 'prefix.');
    }

    function it_can_be_initialized()
    {
        $this->shouldHaveType('Cmp\Monitoring\Metric\MetricFactory');
    }

    function it_can_create_counters()
    {
        $counter = $this->counter('name', 10, array('tags'), .5);
        $counter->shouldBeAnInstanceOf('\Cmp\Monitoring\Metric\Type\Counter');

        $counter->getName()->shouldReturn('prefix.name');
    }

    function it_can_create_gauges()
    {
        $this->gauge('name', 10, array('tags'), .5)
            ->shouldReturnAnInstanceOf('\Cmp\Monitoring\Metric\Type\Gauge');
    }

    function it_can_create_histograms()
    {
        $this->histogram('name', 10, array('tags'), .5)
            ->shouldReturnAnInstanceOf('\Cmp\Monitoring\Metric\Type\Histogram');
    }

    function it_can_create_timers()
    {
        $this->timer('name', 10, array('tags'), .5)
            ->shouldReturnAnInstanceOf('\Cmp\Monitoring\Metric\Type\Timer');
    }

    function it_can_create_sets()
    {
        $this->set('name', 10, array('tags'), .5)
            ->shouldReturnAnInstanceOf('\Cmp\Monitoring\Metric\Type\Set');
    }

    function it_can_add_default_tags_to_events(AbstractMetric $metric)
    {
        $metric->getTags()->willReturn(array('event_tag' => 'tag'));
        $metric->setTags(array('default_tag' => 'default', 'event_tag' => 'tag'))->willReturn($metric);

        $this->addDefaultTagsToEntity($metric)->shouldReturn($metric);
    }

    function it_allows_to_add_default_tags()
    {
        $this->addDefaultTags(array('new_tag' => 'new'));
        $this->getDefaultTags()->shouldReturn(array('default_tag' => 'default', 'new_tag' => 'new'));
    }
}
