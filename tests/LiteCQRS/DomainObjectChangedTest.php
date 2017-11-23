<?php

namespace LiteCQRS;

use PHPUnit\Framework\TestCase;

class DomainObjectChangedTest extends TestCase
{
    public function testThrowsExceptionOnNonExistent()
    {
        $this->expectException("RuntimeException");
        $event = new DomainObjectChanged("TestEvent", array("foo" => "bar"));

        $event->baz;
    }

    public function testEventNameIsDynamic()
    {
        $event = new DomainObjectChanged("Test", array("foo" => "bar"));

        $this->assertEquals("Test", $event->getEventName());
    }

    public function testGetMessageHeader()
    {
        $event = new DomainObjectChanged("Test", array("test" => "value"));

        $this->assertInstanceOf('LiteCQRS\Bus\EventMessageHeader', $event->getMessageHeader());
    }

    public function testGetAggregateIdIsNullAfterCreation()
    {
        $event = new DomainObjectChanged("Test", array("test" => "value"));
        $this->assertNull($event->getAggregateId());
    }
}
