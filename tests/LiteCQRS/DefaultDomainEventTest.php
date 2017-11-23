<?php

namespace LiteCQRS;

use PHPUnit\Framework\TestCase;

class DefaultDomainEventTest extends TestCase
{
    public function testArrayToProperties()
    {
        $event = new TestEvent(array("test" => "value"));

        $this->assertEquals("value", $event->test);
    }

    public function testWrongPropertyThrowsException()
    {
        $this->expectException("RuntimeException");
        $this->expectExceptionMessage("Property unknown is not a valid property on event Test");
        $event = new TestEvent(array("unknown" => "value"));
    }

    public function testGetEventName()
    {
        $event = new TestEvent(array("test" => "value"));

        $this->assertEquals("Test", $event->getEventName());
    }

    public function testGetMessageHeader()
    {
        $event = new TestEvent(array("test" => "value"));

        $this->assertInstanceOf('LiteCQRS\Bus\EventMessageHeader', $event->getMessageHeader());
    }

    public function testGetAggregateIdIsNullAfterCreation()
    {
        $event = new TestEvent(array("test" => "value"));
        $this->assertNull($event->getAggregateId());
    }
}

class TestEvent extends DefaultDomainEvent
{
    public $test;
}
