<?php

namespace spec\Cmp\Monitoring\Metric\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CounterSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('name', 'value', array('tags'), 1);
    }

    function it_can_be_initialized()
    {
        $this->shouldHaveType('Cmp\Monitoring\Metric\Type\Counter');
    }

    function it_can_return_the_name()
    {
        $this->getName()->shouldReturn('name');
    }

    function it_can_return_the_value()
    {
        $this->getValue()->shouldReturn('value');
    }

    function it_can_return_the_tags()
    {
        $this->getTags()->shouldReturn(array('tags'));
    }

    function it_can_return_the_sample_rate()
    {
        $this->getSampleRate()->shouldReturn(1);
    }

    function it_can_return_the_correct_type()
    {
        $this->getType()->shouldReturn('counter');
    }
}
