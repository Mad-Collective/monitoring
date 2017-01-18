<?php

namespace spec\Cmp\Monitoring\Metric\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TimerSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('name', 'value',array('tags'), 1);
    }

    function it_can_be_initialized()
    {
        $this->shouldHaveType('Cmp\Monitoring\Metric\Type\Timer');
    }

    function it_can_return_the_correct_type()
    {
        $this->getType()->shouldReturn('timer');
    }

    function it_can_be_started_and_finished()
    {
        $this->start();
        $this->end();

        $this->getValue()->shouldBeInteger();
    }
}
