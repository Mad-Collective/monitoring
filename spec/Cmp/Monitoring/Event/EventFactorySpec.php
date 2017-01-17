<?php

namespace spec\Cmp\Monitoring\Event;

use Cmp\Monitoring\Event\Event;
use PhpSpec\ObjectBehavior;

class EventFactorySpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('host', array( 'default_tag' => 'default') );
    }

    function it_can_be_initialized()
    {
        $this->shouldHaveType('Cmp\Monitoring\Event\EventFactory');
    }

    function it_can_create_events()
    {
        $this->event('title', 'text', Event::WARNING, array('tags'), 100000)
            ->shouldReturnAnInstanceOf('\Cmp\Monitoring\Event\Event');
    }

    function it_can_add_default_tags_to_events(Event $event)
    {
        $event->getTags()->willReturn(array('event_tag' => 'tag'));

        // Setting this tags on the event is the expected behaviour and the real test here
        $event->setTags(array('default_tag' => 'default', 'event_tag' => 'tag'))->willReturn($event);

        $this->addDefaultTagsToEntity($event)->shouldReturn($event);
    }

    function it_allows_to_add_default_tags()
    {
        $this->addDefaultTags(array('new_tag' => 'new'));
        $this->getDefaultTags()->shouldReturn(array('default_tag' => 'default', 'new_tag' => 'new'));
    }
}
