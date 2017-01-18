<?php

namespace spec\Cmp\Monitoring\Event;

use Cmp\Monitoring\Event\Event;
use PhpSpec\ObjectBehavior;

class EventSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('title', 'text', 'host', Event::WARNING, array('tags'), 100000);
    }

    function it_can_be_initialized()
    {
        $this->shouldHaveType('Cmp\Monitoring\Event\Event');
    }

    function it_can_return_the_title()
    {
        $this->getTitle()->shouldReturn('title');
    }

    function it_can_return_the_text()
    {
        $this->getText()->shouldReturn('text');
    }

    function it_can_return_the_host()
    {
        $this->getHost()->shouldReturn('host');
    }

    function it_can_return_the_type()
    {
        $this->getType()->shouldReturn(Event::WARNING);
    }

    function it_can_return_the_tags()
    {
        $this->getTags()->shouldReturn(array('tags'));
    }

    function it_can_return_the_timestamp()
    {
        $this->getTimestamp()->shouldReturn(100000);
    }

    function it_can_set_current_timestamp_if_not_given()
    {
        $this->setTimestamp()->getTimestamp()->shouldBeInteger();
    }

    function it_can_throw_an_exception_if_the_given_type_is_not_valid()
    {
        $this->shouldThrow('\InvalidArgumentException')->during('setType', array('foo_type'));
    }
}
